<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Button Click Issue</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/navbar.css">
    <link rel="stylesheet" href="assets/css/favorites.css">
</head>
<body>
    <?php
    require_once 'includes/session.php';
    require_once 'db.php';
    
    if (!isLoggedIn()) {
        echo '<div class="container mt-4"><div class="alert alert-warning">Please <a href="login.php">login</a> to test.</div></div></body></html>';
        exit();
    }
    
    $user_id = getUserId();
    
    // Get a sample product
    try {
        $stmt = $pdo->prepare("SELECT id, title, price, stock, image FROM products WHERE stock > 0 LIMIT 1");
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $product = null;
    }
    ?>
    
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container mt-4">
        <h1>Test Button Click Issue</h1>
        
        <div class="alert alert-info">
            This page replicates the exact structure from favorites.php to test button clicking.
        </div>
        
        <?php if ($product): ?>
        <!-- Replicate exact structure from favorites.php -->
        <div class="favorites-content">
            <div class="container">
                <div class="favorites-grid">
                    <div class="row" id="favorites-grid">
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-4" data-product-id="<?php echo $product['id']; ?>">
                            <div class="favorite-card">
                                <div class="favorite-checkbox">
                                    <input type="checkbox" class="form-check-input favorite-select" value="<?php echo $product['id']; ?>" onchange="updateSelectedCount()">
                                </div>
                                <div class="favorite-image">
                                    <a href="product.php?id=<?php echo $product['id']; ?>">
                                        <img src="<?php echo htmlspecialchars(trim($product['image'])); ?>" 
                                             alt="<?php echo htmlspecialchars($product['title']); ?>" 
                                             class="img-fluid"
                                             onerror="this.src='assets/img/placeholder.jpg'">
                                    </a>
                                    <button class="btn-remove-favorite" onclick="removeFromFavorites(<?php echo $product['id']; ?>)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <div class="favorite-info">
                                    <div class="product-brand">Test Brand</div>
                                    <h3 class="product-title">
                                        <a href="product.php?id=<?php echo $product['id']; ?>">
                                            <?php echo htmlspecialchars($product['title']); ?>
                                        </a>
                                    </h3>
                                    <div class="product-details">
                                        <span class="product-color">Test Color</span>
                                        <span class="product-price">₱<?php echo number_format($product['price'], 2); ?></span>
                                    </div>
                                    <div class="product-stock">
                                        <span class="text-success">
                                            <i class="fas fa-check-circle me-1"></i>In Stock
                                        </span>
                                    </div>
                                    <div class="favorite-date">
                                        Added Today
                                    </div>
                                    <div class="favorite-actions">
                                        <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-dark btn-sm">
                                            <i class="fas fa-eye me-1"></i>View
                                        </a>
                                        <button class="btn btn-outline-dark btn-sm"
                                                onclick="console.log('Button clicked!'); addToCart(<?php echo $product['id']; ?>, 1, null, this, true)"
                                                <?php echo ($product['stock'] <= 0) ? 'disabled' : ''; ?>>
                                            <i class="fas fa-shopping-cart me-1"></i>Add to Cart
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Test buttons outside the card structure -->
        <div class="card mt-4">
            <div class="card-header">
                <h3>Test Buttons (Outside Card Structure)</h3>
            </div>
            <div class="card-body">
                <button class="btn btn-primary" onclick="alert('Simple button works!')">
                    Simple Test Button
                </button>
                
                <button class="btn btn-success" onclick="console.log('Console test'); alert('Console + Alert test')">
                    Console + Alert Test
                </button>
                
                <button class="btn btn-warning" onclick="testAddToCart(<?php echo $product['id']; ?>)">
                    Test addToCart Function
                </button>
                
                <button class="btn btn-info" onclick="checkClickability()">
                    Check Button Clickability
                </button>
            </div>
        </div>
        
        <!-- Results -->
        <div class="card mt-4">
            <div class="card-header">
                <h3>Test Results</h3>
            </div>
            <div class="card-body">
                <div id="test-results"></div>
            </div>
        </div>
        
        <?php else: ?>
        <div class="alert alert-danger">No products available for testing.</div>
        <?php endif; ?>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/navbar.js"></script>
    <script src="assets/js/global-cart.js"></script>
    <script src="assets/js/global-favorites.js"></script>
    <script src="assets/js/favorites.js"></script>
    
    <script>
        function testAddToCart(productId) {
            console.log('testAddToCart called with productId:', productId);
            
            if (typeof window.addToCart === 'function') {
                console.log('addToCart function exists, calling it...');
                try {
                    window.addToCart(productId, 1, null, null, true);
                    console.log('addToCart function called successfully');
                } catch (error) {
                    console.error('Error calling addToCart:', error);
                    alert('Error: ' + error.message);
                }
            } else {
                console.error('addToCart function does not exist');
                alert('addToCart function not found!');
            }
        }
        
        function checkClickability() {
            const resultsDiv = document.getElementById('test-results');
            const button = document.querySelector('.favorite-actions button[onclick*="addToCart"]');
            
            let results = [];
            
            if (button) {
                results.push('✅ Button found in DOM');
                results.push(`Button text: "${button.textContent.trim()}"`);
                results.push(`Button disabled: ${button.disabled}`);
                results.push(`Button onclick: ${button.getAttribute('onclick')}`);
                results.push(`Button computed style display: ${window.getComputedStyle(button).display}`);
                results.push(`Button computed style visibility: ${window.getComputedStyle(button).visibility}`);
                results.push(`Button computed style pointer-events: ${window.getComputedStyle(button).pointerEvents}`);
                results.push(`Button computed style z-index: ${window.getComputedStyle(button).zIndex}`);
                
                // Check if button is covered by another element
                const rect = button.getBoundingClientRect();
                const centerX = rect.left + rect.width / 2;
                const centerY = rect.top + rect.height / 2;
                const elementAtPoint = document.elementFromPoint(centerX, centerY);
                
                if (elementAtPoint === button) {
                    results.push('✅ Button is clickable (not covered by other elements)');
                } else {
                    results.push(`❌ Button is covered by: ${elementAtPoint ? elementAtPoint.tagName + '.' + elementAtPoint.className : 'unknown element'}`);
                }
                
                // Test manual click
                results.push('🔄 Testing manual click...');
                try {
                    button.click();
                    results.push('✅ Manual click executed');
                } catch (error) {
                    results.push(`❌ Manual click failed: ${error.message}`);
                }
                
            } else {
                results.push('❌ Button not found in DOM');
            }
            
            // Check function availability
            results.push(`addToCart function: ${typeof window.addToCart === 'function' ? '✅ Available' : '❌ Missing'}`);
            results.push(`showNotification function: ${typeof showNotification === 'function' ? '✅ Available' : '❌ Missing'}`);
            results.push(`isUserLoggedIn function: ${typeof isUserLoggedIn === 'function' ? '✅ Available' : '❌ Missing'}`);
            
            if (typeof isUserLoggedIn === 'function') {
                results.push(`User logged in: ${isUserLoggedIn() ? '✅ Yes' : '❌ No'}`);
            }
            
            resultsDiv.innerHTML = `
                <div class="alert alert-info">
                    <h6>Clickability Test Results:</h6>
                    ${results.map(result => `<div>${result}</div>`).join('')}
                </div>
            `;
        }
        
        // Mock functions if they don't exist
        if (typeof updateSelectedCount !== 'function') {
            window.updateSelectedCount = function() {
                console.log('updateSelectedCount called (mock)');
            };
        }
        
        if (typeof removeFromFavorites !== 'function') {
            window.removeFromFavorites = function(productId) {
                console.log('removeFromFavorites called with:', productId);
                alert('removeFromFavorites called with product ID: ' + productId);
            };
        }
        
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Page loaded, running initial tests...');
            setTimeout(checkClickability, 1000); // Wait for all scripts to load
        });
    </script>
</body>
</html>
