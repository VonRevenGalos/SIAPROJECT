<?php
require_once 'includes/session.php';
require_once 'db.php';

// Require login
requireLogin();

$user = getCurrentUser();
$user_id = getUserId();
$success = $_SESSION['login_success'] ?? '';
unset($_SESSION['login_success']);

// Get user stats
try {
    // Get cart count
    $stmt = $pdo->prepare("SELECT SUM(quantity) as count FROM cart WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $cart_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

    // Get favorites count
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM favorites WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $favorites_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

    // Get orders count
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM orders WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $orders_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

    // Get notifications count (if notifications table exists)
    $notifications_count = 0; // Placeholder for now

} catch (PDOException $e) {
    error_log("User stats error: " . $e->getMessage());
    $cart_count = $favorites_count = $orders_count = $notifications_count = 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - ShoeStore</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/navbar.css">
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="user-container">
        <div class="container">
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <!-- Welcome Section -->
            <div class="welcome-card">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="welcome-title">Welcome back, <?php echo htmlspecialchars($user['first_name']); ?>!</h1>
                        <p class="welcome-subtitle">Manage your account, track orders, and discover new products.</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="d-flex justify-content-end">
                            <a href="logout.php" class="logout-btn" id="logoutBtn">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Stats Section -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="stats-card">
                        <div class="stats-icon" style="background: #e3f2fd; color: #1976d2;">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="stats-number" id="cart-stats"><?php echo (int)$cart_count; ?></div>
                        <div class="stats-label">Items in Cart</div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stats-card">
                        <div class="stats-icon" style="background: #fce4ec; color: #c2185b;">
                            <i class="fas fa-heart"></i>
                        </div>
                        <div class="stats-number" id="favorites-stats"><?php echo (int)$favorites_count; ?></div>
                        <div class="stats-label">Favorites</div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="myorders.php" class="text-decoration-none">
                        <div class="stats-card">
                            <div class="stats-icon" style="background: #e8f5e8; color: #388e3c;">
                                <i class="fas fa-box"></i>
                            </div>
                            <div class="stats-number" id="orders-stats"><?php echo (int)$orders_count; ?></div>
                            <div class="stats-label">Total Orders</div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stats-card">
                        <div class="stats-icon" style="background: #fff3e0; color: #f57c00;">
                            <i class="fas fa-bell"></i>
                        </div>
                        <div class="stats-number" id="notifications-stats"><?php echo (int)$notifications_count; ?></div>
                        <div class="stats-label">Notifications</div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <!-- Quick Actions -->
                <div class="col-lg-4 mb-4">
                    <div class="quick-actions">
                        <h3 class="mb-4">Quick Actions</h3>
                        <div class="row g-3">
                            <div class="col-6">
                                <a href="cart.php" class="action-btn">
                                    <i class="fas fa-shopping-cart action-icon"></i>
                                    <div>My Cart</div>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="favorites.php" class="action-btn">
                                    <i class="fas fa-heart action-icon"></i>
                                    <div>Favorites</div>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="orders.php" class="action-btn">
                                    <i class="fas fa-box action-icon"></i>
                                    <div>My Orders</div>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="profile.php" class="action-btn">
                                    <i class="fas fa-user action-icon"></i>
                                    <div>Edit Profile</div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Orders -->
                <div class="col-lg-8">
                    <div class="recent-orders">
                        <h3 class="mb-4">Recent Orders</h3>
                        
                        <!-- Sample Order Items -->
                        <div class="order-item">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h6 class="mb-1">Order #12345</h6>
                                    <small class="text-muted">Placed on March 15, 2024</small>
                                </div>
                                <div class="col-md-3">
                                    <span class="order-status status-delivered">Delivered</span>
                                </div>
                                <div class="col-md-3 text-end">
                                    <strong>$129.99</strong>
                                </div>
                            </div>
                        </div>
                        
                        <div class="order-item">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h6 class="mb-1">Order #12344</h6>
                                    <small class="text-muted">Placed on March 10, 2024</small>
                                </div>
                                <div class="col-md-3">
                                    <span class="order-status status-shipped">Shipped</span>
                                </div>
                                <div class="col-md-3 text-end">
                                    <strong>$89.99</strong>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-center mt-4">
                            <a href="orders.php" class="btn btn-outline-primary">View All Orders</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="assets/js/navbar.js"></script>
    <script src="assets/js/auth.js"></script>
    <script src="assets/js/user-stats.js"></script>
</body>
</html>
