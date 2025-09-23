<?php
require_once 'includes/session.php';
require_once 'db.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please log in to place an order']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['action']) || $_POST['action'] !== 'place_order') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

$user_id = getUserId();
$shipping_address_id = (int)($_POST['shipping_address_id'] ?? 0);
$payment_method = $_POST['payment_method'] ?? '';

// Validate inputs
if ($shipping_address_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Please select a shipping address']);
    exit();
}

if (!in_array($payment_method, ['cod', 'bank_transfer', 'card', 'gcash'])) {
    echo json_encode(['success' => false, 'message' => 'Please select a valid payment method']);
    exit();
}

try {
    // Start transaction
    $pdo->beginTransaction();
    
    // Verify shipping address belongs to user
    $stmt = $pdo->prepare("SELECT id FROM user_addresses WHERE id = ? AND user_id = ?");
    $stmt->execute([$shipping_address_id, $user_id]);
    if (!$stmt->fetch()) {
        throw new Exception('Invalid shipping address');
    }
    
    // Get cart items with stock validation
    $stmt = $pdo->prepare("
        SELECT c.cart_id, c.product_id, c.quantity, c.size, 
               p.title, p.price, p.stock
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($cart_items)) {
        throw new Exception('Your cart is empty');
    }
    
    // Validate stock and sizes
    $total_price = 0;
    $items_without_size = [];
    
    foreach ($cart_items as $item) {
        // Check stock
        if ($item['quantity'] > $item['stock']) {
            throw new Exception("Not enough stock for {$item['title']}. Available: {$item['stock']}, Requested: {$item['quantity']}");
        }
        
        // Check size selection
        if (empty($item['size'])) {
            $items_without_size[] = $item['title'];
        }
        
        $total_price += $item['price'] * $item['quantity'];
    }
    
    if (!empty($items_without_size)) {
        throw new Exception('Please select sizes for all items: ' . implode(', ', $items_without_size));
    }
    
    // Add shipping and tax
    $shipping_fee = 150;
    $tax_rate = 0.12;
    $tax = $total_price * $tax_rate;
    $final_total = $total_price + $shipping_fee + $tax;
    
    // Create order
    $stmt = $pdo->prepare("
        INSERT INTO orders (user_id, status, total_price, payment_method, shipping_address_id, created_at) 
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    
    $order_status = ($payment_method === 'cod') ? 'Pending' : 'Processing';
    $stmt->execute([$user_id, $order_status, $final_total, $payment_method, $shipping_address_id]);
    $order_id = $pdo->lastInsertId();
    
    // Create order items and update stock
    foreach ($cart_items as $item) {
        // Insert order item
        $stmt = $pdo->prepare("
            INSERT INTO order_items (order_id, product_id, quantity, price, size) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$order_id, $item['product_id'], $item['quantity'], $item['price'], $item['size']]);
        
        // Update product stock (only for COD orders - immediate stock deduction)
        if ($payment_method === 'cod') {
            $stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
            $stmt->execute([$item['quantity'], $item['product_id']]);
        }
    }
    
    // Clear cart
    $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->execute([$user_id]);
    
    // Commit transaction
    $pdo->commit();
    
    // Log successful order
    error_log("Order created successfully: Order ID {$order_id}, User ID {$user_id}, Payment Method: {$payment_method}");
    
    echo json_encode([
        'success' => true,
        'message' => 'Order placed successfully',
        'order_id' => $order_id,
        'payment_method' => $payment_method
    ]);
    
} catch (Exception $e) {
    // Rollback transaction
    $pdo->rollBack();
    
    error_log("Checkout error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
