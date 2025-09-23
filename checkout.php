<?php
require_once 'includes/session.php';
require_once 'db.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

$user_id = getUserId();

// Get cart items
try {
    $stmt = $pdo->prepare("
        SELECT c.cart_id, c.product_id, c.quantity, c.size,
               p.title, p.price, p.image, p.brand, p.color, p.stock
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = ?
        ORDER BY c.date_added DESC
    ");
    $stmt->execute([$user_id]);
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($cart_items)) {
        header("Location: cart.php");
        exit();
    }
    
} catch (PDOException $e) {
    error_log("Error fetching cart items: " . $e->getMessage());
    $cart_items = [];
}

// Get user addresses
try {
    $stmt = $pdo->prepare("
        SELECT * FROM user_addresses 
        WHERE user_id = ? 
        ORDER BY is_default DESC, created_at DESC
    ");
    $stmt->execute([$user_id]);
    $addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching addresses: " . $e->getMessage());
    $addresses = [];
}

// Calculate totals
$subtotal = 0;
$items_without_size = [];
foreach ($cart_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
    if (empty($item['size'])) {
        $items_without_size[] = $item['title'];
    }
}

$shipping_fee = 150; // Fixed shipping fee
$tax_rate = 0.12; // 12% VAT
$tax = $subtotal * $tax_rate;
$total = $subtotal + $shipping_fee + $tax;

$currentUser = getCurrentUser();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - ShoeARizz</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/navbar.css">
    <link rel="stylesheet" href="assets/css/cart.css">
    
    <style>
        .checkout-container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }
        
        .checkout-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .checkout-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .step-indicator {
            display: flex;
            justify-content: center;
            margin: 2rem 0;
        }
        
        .step {
            display: flex;
            align-items: center;
            margin: 0 1rem;
        }
        
        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 0.5rem;
        }
        
        .step.active .step-number {
            background: #667eea;
            color: white;
        }
        
        .step.completed .step-number {
            background: #28a745;
            color: white;
        }
        
        .section-card {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }
        
        .section-card:hover {
            border-color: #667eea;
            transform: translateY(-2px);
        }
        
        .payment-method {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .payment-method:hover {
            border-color: #667eea;
            background: #f8f9fa;
        }
        
        .payment-method.selected {
            border-color: #667eea;
            background: #e7f3ff;
        }
        
        .order-summary {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .btn-checkout {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 25px;
            padding: 1rem 2rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }
        
        .btn-checkout:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        
        .size-warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        
        .item-card {
            background: white;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="checkout-container">
        <div class="container">
            <div class="checkout-card">
                <div class="checkout-header">
                    <h1><i class="fas fa-shopping-bag me-2"></i>Checkout</h1>
                    <p class="mb-0">Complete your order securely</p>
                </div>
                
                <div class="step-indicator">
                    <div class="step active">
                        <div class="step-number">1</div>
                        <span>Review</span>
                    </div>
                    <div class="step">
                        <div class="step-number">2</div>
                        <span>Payment</span>
                    </div>
                    <div class="step">
                        <div class="step-number">3</div>
                        <span>Complete</span>
                    </div>
                </div>
                
                <div class="row p-4">
                    <div class="col-lg-8">
                        <!-- Size Validation Warning -->
                        <?php if (!empty($items_without_size)): ?>
                        <div class="size-warning">
                            <h5><i class="fas fa-exclamation-triangle text-warning me-2"></i>Size Selection Required</h5>
                            <p class="mb-2">Please select sizes for the following items before proceeding:</p>
                            <ul class="mb-0">
                                <?php foreach ($items_without_size as $item_title): ?>
                                <li><?php echo htmlspecialchars($item_title); ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <a href="cart.php" class="btn btn-warning mt-2">
                                <i class="fas fa-arrow-left me-2"></i>Back to Cart
                            </a>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Order Items -->
                        <div class="section-card">
                            <h4><i class="fas fa-box me-2"></i>Order Items</h4>
                            <?php foreach ($cart_items as $item): ?>
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
                                            <?php if ($item['size']): ?>
                                                <span class="badge bg-primary"><?php echo htmlspecialchars($item['size']); ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">Not Selected</span>
                                            <?php endif; ?>
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
                        
                        <!-- Shipping Address -->
                        <div class="section-card">
                            <h4><i class="fas fa-map-marker-alt me-2"></i>Shipping Address</h4>
                            <?php if (empty($addresses)): ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                No shipping address found. Please add an address first.
                                <a href="user.php" class="btn btn-warning btn-sm ms-2">Add Address</a>
                            </div>
                            <?php else: ?>
                            <div class="row">
                                <?php foreach ($addresses as $address): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="payment-method" onclick="selectAddress(<?php echo $address['id']; ?>)">
                                        <input type="radio" name="shipping_address" value="<?php echo $address['id']; ?>" 
                                               <?php echo $address['is_default'] ? 'checked' : ''; ?> class="form-check-input me-2">
                                        <div>
                                            <strong><?php echo htmlspecialchars($address['full_name']); ?></strong>
                                            <?php if ($address['is_default']): ?>
                                                <span class="badge bg-primary ms-2">Default</span>
                                            <?php endif; ?>
                                            <p class="mb-1 text-muted">
                                                <?php echo htmlspecialchars($address['address_line1']); ?>
                                                <?php if ($address['address_line2']): ?>
                                                    <br><?php echo htmlspecialchars($address['address_line2']); ?>
                                                <?php endif; ?>
                                            </p>
                                            <p class="mb-0 text-muted">
                                                <?php echo htmlspecialchars($address['city']); ?>, 
                                                <?php echo htmlspecialchars($address['state']); ?> 
                                                <?php echo htmlspecialchars($address['postal_code']); ?>
                                            </p>
                                            <?php if ($address['phone']): ?>
                                                <p class="mb-0 text-muted">
                                                    <i class="fas fa-phone me-1"></i><?php echo htmlspecialchars($address['phone']); ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Payment Method -->
                        <div class="section-card">
                            <h4><i class="fas fa-credit-card me-2"></i>Payment Method</h4>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="payment-method" onclick="selectPayment('cod')">
                                        <input type="radio" name="payment_method" value="cod" checked class="form-check-input me-2">
                                        <div>
                                            <i class="fas fa-money-bill-wave text-success me-2"></i>
                                            <strong>Cash on Delivery (COD)</strong>
                                            <p class="mb-0 text-muted">Pay when you receive your order</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="payment-method" onclick="selectPayment('bank_transfer')">
                                        <input type="radio" name="payment_method" value="bank_transfer" class="form-check-input me-2">
                                        <div>
                                            <i class="fas fa-university text-primary me-2"></i>
                                            <strong>Bank Transfer</strong>
                                            <p class="mb-0 text-muted">Transfer to our bank account</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="payment-method" onclick="selectPayment('card')">
                                        <input type="radio" name="payment_method" value="card" class="form-check-input me-2">
                                        <div>
                                            <i class="fas fa-credit-card text-info me-2"></i>
                                            <strong>Credit/Debit Card</strong>
                                            <p class="mb-0 text-muted">Visa, Mastercard, etc.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="payment-method" onclick="selectPayment('gcash')">
                                        <input type="radio" name="payment_method" value="gcash" class="form-check-input me-2">
                                        <div>
                                            <i class="fas fa-mobile-alt text-warning me-2"></i>
                                            <strong>GCash</strong>
                                            <p class="mb-0 text-muted">Pay via GCash mobile wallet</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="order-summary">
                            <h4><i class="fas fa-receipt me-2"></i>Order Summary</h4>
                            <hr>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal (<?php echo count($cart_items); ?> items)</span>
                                <span>₱<?php echo number_format($subtotal, 2); ?></span>
                            </div>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span>Shipping Fee</span>
                                <span>₱<?php echo number_format($shipping_fee, 2); ?></span>
                            </div>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tax (12% VAT)</span>
                                <span>₱<?php echo number_format($tax, 2); ?></span>
                            </div>
                            
                            <hr>
                            
                            <div class="d-flex justify-content-between mb-3">
                                <strong>Total</strong>
                                <strong class="text-primary">₱<?php echo number_format($total, 2); ?></strong>
                            </div>
                            
                            <button type="button" class="btn btn-checkout btn-primary w-100" 
                                    onclick="processCheckout()" 
                                    <?php echo (!empty($items_without_size) || empty($addresses)) ? 'disabled' : ''; ?>>
                                <i class="fas fa-lock me-2"></i>Place Order Securely
                            </button>
                            
                            <div class="text-center mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-shield-alt me-1"></i>
                                    Your payment information is secure
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function selectAddress(addressId) {
            document.querySelector(`input[value="${addressId}"]`).checked = true;
            
            // Update visual selection
            document.querySelectorAll('.payment-method').forEach(el => {
                if (el.querySelector('input[name="shipping_address"]')) {
                    el.classList.remove('selected');
                }
            });
            event.currentTarget.classList.add('selected');
        }
        
        function selectPayment(method) {
            document.querySelector(`input[value="${method}"]`).checked = true;
            
            // Update visual selection
            document.querySelectorAll('.payment-method').forEach(el => {
                if (el.querySelector('input[name="payment_method"]')) {
                    el.classList.remove('selected');
                }
            });
            event.currentTarget.classList.add('selected');
        }
        
        function processCheckout() {
            const addressId = document.querySelector('input[name="shipping_address"]:checked')?.value;
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked')?.value;
            
            if (!addressId) {
                alert('Please select a shipping address');
                return;
            }
            
            if (!paymentMethod) {
                alert('Please select a payment method');
                return;
            }
            
            // Show loading state
            const button = document.querySelector('.btn-checkout');
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
            button.disabled = true;
            
            // Submit the order
            const formData = new FormData();
            formData.append('action', 'place_order');
            formData.append('shipping_address_id', addressId);
            formData.append('payment_method', paymentMethod);
            
            fetch('process_checkout.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (paymentMethod === 'bank_transfer') {
                        window.location.href = `bank_transfer.php?order_id=${data.order_id}`;
                    } else {
                        window.location.href = `order_success.php?order_id=${data.order_id}`;
                    }
                } else {
                    alert(data.message || 'An error occurred while processing your order');
                    button.innerHTML = originalText;
                    button.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while processing your order');
                button.innerHTML = originalText;
                button.disabled = false;
            });
        }
        
        // Initialize visual selection
        document.addEventListener('DOMContentLoaded', function() {
            // Set default address selection
            const defaultAddress = document.querySelector('input[name="shipping_address"]:checked');
            if (defaultAddress) {
                defaultAddress.closest('.payment-method').classList.add('selected');
            }
            
            // Set default payment selection
            const defaultPayment = document.querySelector('input[name="payment_method"]:checked');
            if (defaultPayment) {
                defaultPayment.closest('.payment-method').classList.add('selected');
            }
        });
    </script>
</body>
</html>
