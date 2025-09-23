<?php
require_once 'includes/session.php';
require_once 'db.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

$user_id = getUserId();
$order_id = (int)($_GET['order_id'] ?? 0);

if ($order_id <= 0) {
    header("Location: index.php");
    exit();
}

// Get order details
try {
    $stmt = $pdo->prepare("
        SELECT o.*, ua.full_name, ua.address_line1, ua.address_line2, 
               ua.city, ua.state, ua.postal_code, ua.phone
        FROM orders o
        LEFT JOIN user_addresses ua ON o.shipping_address_id = ua.id
        WHERE o.id = ? AND o.user_id = ?
    ");
    $stmt->execute([$order_id, $user_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        header("Location: index.php");
        exit();
    }
    
    // Get order items
    $stmt = $pdo->prepare("
        SELECT oi.*, p.title, p.image, p.brand, p.color
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?
    ");
    $stmt->execute([$order_id]);
    $order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    error_log("Error fetching order details: " . $e->getMessage());
    header("Location: index.php");
    exit();
}

$currentUser = getCurrentUser();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - ShoeARizz</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/navbar.css">
    
    <style>
        .success-container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }
        
        .success-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .success-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 3rem 2rem;
            text-align: center;
        }
        
        .success-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-10px);
            }
            60% {
                transform: translateY(-5px);
            }
        }
        
        .order-details {
            padding: 2rem;
        }
        
        .detail-card {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid #667eea;
        }
        
        .item-card {
            background: white;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .btn-action {
            border-radius: 25px;
            padding: 0.75rem 2rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }
        
        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .payment-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 0.8rem;
        }
        
        .payment-cod {
            background: #fff3cd;
            color: #856404;
        }
        
        .payment-bank {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .payment-card {
            background: #d4edda;
            color: #155724;
        }
        
        .payment-gcash {
            background: #ffeaa7;
            color: #6c5ce7;
        }
        
        .status-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 0.8rem;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-processing {
            background: #d1ecf1;
            color: #0c5460;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="success-container">
        <div class="container">
            <div class="success-card">
                <div class="success-header">
                    <div class="success-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h1>Order Confirmed!</h1>
                    <p class="mb-0">Thank you for your purchase. Your order has been successfully placed.</p>
                </div>
                
                <div class="order-details">
                    <!-- Order Summary -->
                    <div class="detail-card">
                        <div class="row">
                            <div class="col-md-6">
                                <h5><i class="fas fa-receipt me-2"></i>Order Information</h5>
                                <p><strong>Order ID:</strong> #<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></p>
                                <p><strong>Order Date:</strong> <?php echo date('F j, Y g:i A', strtotime($order['created_at'])); ?></p>
                                <p><strong>Status:</strong> 
                                    <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
                                        <?php echo $order['status']; ?>
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h5><i class="fas fa-credit-card me-2"></i>Payment Information</h5>
                                <p><strong>Payment Method:</strong> 
                                    <span class="payment-badge payment-<?php echo str_replace('_', '', $order['payment_method']); ?>">
                                        <?php 
                                        switch($order['payment_method']) {
                                            case 'cod': echo 'Cash on Delivery'; break;
                                            case 'bank_transfer': echo 'Bank Transfer'; break;
                                            case 'card': echo 'Credit/Debit Card'; break;
                                            case 'gcash': echo 'GCash'; break;
                                            default: echo ucfirst($order['payment_method']);
                                        }
                                        ?>
                                    </span>
                                </p>
                                <p><strong>Total Amount:</strong> <span class="text-success fw-bold">₱<?php echo number_format($order['total_price'], 2); ?></span></p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Shipping Address -->
                    <div class="detail-card">
                        <h5><i class="fas fa-map-marker-alt me-2"></i>Shipping Address</h5>
                        <div class="row">
                            <div class="col-md-8">
                                <p class="mb-1"><strong><?php echo htmlspecialchars($order['full_name']); ?></strong></p>
                                <p class="mb-1"><?php echo htmlspecialchars($order['address_line1']); ?></p>
                                <?php if ($order['address_line2']): ?>
                                    <p class="mb-1"><?php echo htmlspecialchars($order['address_line2']); ?></p>
                                <?php endif; ?>
                                <p class="mb-1"><?php echo htmlspecialchars($order['city']); ?>, <?php echo htmlspecialchars($order['state']); ?> <?php echo htmlspecialchars($order['postal_code']); ?></p>
                                <?php if ($order['phone']): ?>
                                    <p class="mb-0"><i class="fas fa-phone me-1"></i><?php echo htmlspecialchars($order['phone']); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Order Items -->
                    <div class="detail-card">
                        <h5><i class="fas fa-box me-2"></i>Order Items</h5>
                        <?php foreach ($order_items as $item): ?>
                        <div class="item-card">
                            <div class="row align-items-center">
                                <div class="col-md-2">
                                    <img src="<?php echo htmlspecialchars($item['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($item['title']); ?>" 
                                         class="img-fluid rounded">
                                </div>
                                <div class="col-md-6">
                                    <h6 class="mb-1"><?php echo htmlspecialchars($item['title']); ?></h6>
                                    <p class="text-muted mb-1">
                                        <?php echo htmlspecialchars($item['brand']); ?> • 
                                        <?php echo htmlspecialchars($item['color']); ?>
                                    </p>
                                    <p class="mb-0">
                                        <strong>Size: </strong>
                                        <span class="badge bg-primary"><?php echo htmlspecialchars($item['size']); ?></span>
                                    </p>
                                </div>
                                <div class="col-md-2 text-center">
                                    <p class="mb-0"><strong>Qty: <?php echo $item['quantity']; ?></strong></p>
                                </div>
                                <div class="col-md-2 text-end">
                                    <p class="mb-0 fw-bold">₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Next Steps -->
                    <div class="detail-card">
                        <h5><i class="fas fa-info-circle me-2"></i>What's Next?</h5>
                        <?php if ($order['payment_method'] === 'cod'): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-truck me-2"></i>
                                <strong>Cash on Delivery:</strong> Your order is being prepared for shipment. 
                                You'll pay when you receive your items. We'll send you tracking information once your order ships.
                            </div>
                        <?php elseif ($order['payment_method'] === 'bank_transfer'): ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-university me-2"></i>
                                <strong>Bank Transfer:</strong> Please complete your payment to process your order. 
                                Your order will be shipped once payment is confirmed.
                            </div>
                        <?php else: ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check me-2"></i>
                                <strong>Payment Received:</strong> Your order is being prepared for shipment. 
                                We'll send you tracking information once your order ships.
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="text-center">
                        <a href="myorders.php" class="btn btn-primary btn-action me-3">
                            <i class="fas fa-list me-2"></i>View My Orders
                        </a>
                        <a href="index.php" class="btn btn-outline-primary btn-action">
                            <i class="fas fa-shopping-bag me-2"></i>Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Update user stats in navbar
        document.addEventListener('DOMContentLoaded', function() {
            // Update cart count to 0 since cart was cleared
            const cartBadge = document.querySelector('a[href="cart.php"] .notification-badge');
            if (cartBadge) {
                cartBadge.style.display = 'none';
            }
        });
    </script>
</body>
</html>
