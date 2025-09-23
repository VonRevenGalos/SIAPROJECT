<?php
require_once 'includes/session.php';
require_once 'db.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$user_id = getUserId();

// Fetch user's cart items
try {
    $stmt = $pdo->prepare("
        SELECT c.*, p.title, p.price, p.stock, p.image, p.brand, p.color 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.user_id = ? 
        ORDER BY c.date_added DESC
    ");
    $stmt->execute([$user_id]);
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate totals
    $subtotal = 0;
    $total_items = 0;
    foreach ($cart_items as $item) {
        $subtotal += $item['price'] * $item['quantity'];
        $total_items += $item['quantity'];
    }
    
    $shipping = $subtotal > 100 ? 0 : 10; // Free shipping over $100
    $tax = $subtotal * 0.08; // 8% tax
    $total = $subtotal + $shipping + $tax;
    
} catch (PDOException $e) {
    error_log("Cart fetch error: " . $e->getMessage());
    $cart_items = [];
    $subtotal = $total_items = $shipping = $tax = $total = 0;
}

// Get current user
$currentUser = getCurrentUser();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - ShoeARizz</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/navbar.css">
    <link rel="stylesheet" href="assets/css/cart.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container-fluid cart-container">
        <div class="row">
            <div class="col-12">
                <!-- Page Header -->
                <div class="cart-header">
                    <div class="container">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h1 class="page-title">
                                    <i class="fas fa-shopping-cart me-3"></i>Shopping Cart
                                </h1>
                                <p class="page-subtitle">
                                    <span id="cart-count"><?php echo $total_items; ?></span> item<?php echo $total_items !== 1 ? 's' : ''; ?> in your cart
                                </p>
                            </div>
                            <div class="col-md-6 text-end">
                                <?php if (!empty($cart_items)): ?>
                                <div class="header-actions">
                                    <button class="btn btn-outline-dark me-2" id="select-all-btn" onclick="toggleSelectAll()">
                                        <i class="fas fa-check-square me-1"></i>Select All
                                    </button>
                                    <button class="btn btn-dark me-2" id="remove-selected-btn" onclick="removeSelected()" disabled>
                                        <i class="fas fa-trash me-1"></i>Remove Selected
                                    </button>
                                    <button class="btn btn-dark" onclick="clearCart()">
                                        <i class="fas fa-trash-alt me-1"></i>Clear Cart
                                    </button>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cart Content -->
                <div class="cart-content">
                    <div class="container">
                        <?php if (empty($cart_items)): ?>
                            <!-- Empty State -->
                            <div class="empty-cart">
                                <div class="empty-icon">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                                <h3>Your cart is empty</h3>
                                <p>Start adding products to your cart to see them here.</p>
                                <a href="men.php" class="btn btn-dark btn-lg">
                                    <i class="fas fa-shopping-bag me-2"></i>Start Shopping
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <!-- Cart Items -->
                                <div class="col-lg-8">
                                    <div class="cart-items" id="cart-items">
                                        <?php foreach ($cart_items as $item): ?>
                                            <div class="cart-item" data-cart-id="<?php echo $item['cart_id']; ?>">
                                                <div class="item-checkbox">
                                                    <input type="checkbox" class="form-check-input cart-select" value="<?php echo $item['cart_id']; ?>" onchange="updateSelectedCount()">
                                                </div>
                                                <div class="item-image">
                                                    <a href="product.php?id=<?php echo $item['product_id']; ?>">
                                                        <img src="<?php echo htmlspecialchars(trim($item['image'])); ?>" 
                                                             alt="<?php echo htmlspecialchars($item['title']); ?>" 
                                                             class="img-fluid"
                                                             onerror="this.src='assets/img/placeholder.jpg'">
                                                    </a>
                                                </div>
                                                <div class="item-details">
                                                    <div class="item-brand"><?php echo htmlspecialchars($item['brand'] ?? 'Generic'); ?></div>
                                                    <h4 class="item-title">
                                                        <a href="product.php?id=<?php echo $item['product_id']; ?>">
                                                            <?php echo htmlspecialchars($item['title']); ?>
                                                        </a>
                                                    </h4>
                                                    <div class="item-info">
                                                        <span class="item-color">Color: <?php echo htmlspecialchars($item['color'] ?? 'N/A'); ?></span>
                                                        <span class="item-size">
                                                            Size: 
                                                            <button class="size-btn" onclick="editSize(<?php echo $item['cart_id']; ?>, '<?php echo htmlspecialchars($item['size'] ?? 'N/A'); ?>')">
                                                                <?php echo htmlspecialchars($item['size'] ?? 'N/A'); ?>
                                                                <i class="fas fa-edit ms-1"></i>
                                                            </button>
                                                        </span>
                                                    </div>
                                                    <div class="item-stock">
                                                        <?php if ($item['stock'] > 0): ?>
                                                            <span class="text-success">
                                                                <i class="fas fa-check-circle me-1"></i>In Stock (<?php echo $item['stock']; ?> available)
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="text-danger">
                                                                <i class="fas fa-times-circle me-1"></i>Out of Stock
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <div class="item-quantity">
                                                    <label>Quantity:</label>
                                                    <div class="quantity-controls">
                                                        <button class="qty-btn" onclick="updateQuantity(<?php echo $item['cart_id']; ?>, <?php echo $item['quantity'] - 1; ?>)" 
                                                                <?php echo ($item['quantity'] <= 1) ? 'disabled' : ''; ?>>
                                                            <i class="fas fa-minus"></i>
                                                        </button>
                                                        <input type="number" class="qty-input" value="<?php echo $item['quantity']; ?>" 
                                                               min="1" max="<?php echo $item['stock']; ?>"
                                                               onchange="updateQuantity(<?php echo $item['cart_id']; ?>, this.value)">
                                                        <button class="qty-btn" onclick="updateQuantity(<?php echo $item['cart_id']; ?>, <?php echo $item['quantity'] + 1; ?>)"
                                                                <?php echo ($item['quantity'] >= $item['stock']) ? 'disabled' : ''; ?>>
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="item-price">
                                                    <div class="price-per-item">₱<?php echo number_format($item['price'], 2); ?> each</div>
                                                    <div class="price-total" data-cart-id="<?php echo $item['cart_id']; ?>">
                                                        ₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                                                    </div>
                                                </div>
                                                <div class="item-actions">
                                                    <button class="btn-remove" onclick="removeFromCart(<?php echo $item['cart_id']; ?>)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                
                                <!-- Cart Summary -->
                                <div class="col-lg-4">
                                    <div class="cart-summary">
                                        <h3>Order Summary</h3>
                                        <div class="summary-row">
                                            <span>Subtotal (<span id="summary-items"><?php echo $total_items; ?></span> items):</span>
                                            <span id="summary-subtotal">₱<?php echo number_format($subtotal, 2); ?></span>
                                        </div>
                                        <div class="summary-row">
                                            <span>Shipping:</span>
                                            <span id="summary-shipping"><?php echo $shipping > 0 ? '₱' . number_format($shipping, 2) : 'FREE'; ?></span>
                                        </div>
                                        <div class="summary-row">
                                            <span>Tax:</span>
                                            <span id="summary-tax">₱<?php echo number_format($tax, 2); ?></span>
                                        </div>
                                        <hr>
                                        <div class="summary-total">
                                            <span>Total:</span>
                                            <span id="summary-total">₱<?php echo number_format($total, 2); ?></span>
                                        </div>
                                        
                                        <div class="checkout-actions">
                                            <button class="btn btn-dark btn-lg w-100 mb-3" onclick="proceedToCheckout()">
                                                <i class="fas fa-credit-card me-2"></i>Proceed to Checkout
                                            </button>
                                            <a href="men.php" class="btn btn-outline-dark w-100">
                                                <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                                            </a>
                                        </div>
                                        
                                        <div class="shipping-info">
                                            <p><i class="fas fa-truck me-2"></i>Free shipping on orders over ₱100</p>
                                            <p><i class="fas fa-shield-alt me-2"></i>Secure checkout guaranteed</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Size Edit Modal -->
    <div class="modal fade" id="sizeModal" tabindex="-1" aria-labelledby="sizeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sizeModalLabel">Edit Size</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="size-options">
                        <label class="form-label">Select Size:</label>
                        <div class="size-grid">
                            <button class="size-option" data-size="6">6</button>
                            <button class="size-option" data-size="6.5">6.5</button>
                            <button class="size-option" data-size="7">7</button>
                            <button class="size-option" data-size="7.5">7.5</button>
                            <button class="size-option" data-size="8">8</button>
                            <button class="size-option" data-size="8.5">8.5</button>
                            <button class="size-option" data-size="9">9</button>
                            <button class="size-option" data-size="9.5">9.5</button>
                            <button class="size-option" data-size="10">10</button>
                            <button class="size-option" data-size="10.5">10.5</button>
                            <button class="size-option" data-size="11">11</button>
                            <button class="size-option" data-size="12">12</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-dark" onclick="saveSize()">Save Size</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="assets/js/navbar.js"></script>
    <script src="assets/js/cart.js"></script>
</body>
</html>
