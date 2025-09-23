<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Favorites Add to Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container mt-4">
        <h1>Test Favorites Add to Cart Button</h1>
        
        <?php
        require_once 'includes/session.php';
        require_once 'db.php';
        
        if (!isLoggedIn()) {
            echo '<div class="alert alert-warning">Please <a href="login.php">login</a> to test.</div>';
            echo '</body></html>';
            exit();
        }
        
        $user_id = getUserId();
        ?>
        
        <div class="alert alert-success">
            ✓ User is logged in (ID: <?php echo $user_id; ?>)
        </div>
        
        <!-- Test Button -->
        <div class="card mb-4">
            <div class="card-header">
                <h3>Test Add to Cart from Favorites</h3>
            </div>
            <div class="card-body">
                <p>This simulates the exact button from favorites.php:</p>
                
                <?php
                // Get a sample product
                try {
                    $stmt = $pdo->prepare("SELECT id, title, price, stock FROM products WHERE stock > 0 LIMIT 1");
                    $stmt->execute();
                    $product = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($product) {
                        echo "<div class='mb-3'>";
                        echo "<strong>Test Product:</strong> {$product['title']} (ID: {$product['id']})";
                        echo "<br><strong>Price:</strong> ₱" . number_format($product['price'], 2);
                        echo "<br><strong>Stock:</strong> {$product['stock']}";
                        echo "</div>";
                        
                        echo '<button class="btn btn-outline-dark btn-sm" ';
                        echo 'onclick="console.log(\'Button clicked!\'); addToCart(' . $product['id'] . ', 1, null, this, true)" ';
                        echo ($product['stock'] <= 0) ? 'disabled' : '';
                        echo '>';
                        echo '<i class="fas fa-shopping-cart me-1"></i>Add to Cart (From Favorites)';
                        echo '</button>';

                        echo '<br><br>';
                        echo '<button class="btn btn-success btn-sm" onclick="alert(\'Simple test works!\')">Simple Test Button</button>';
                        echo '<br><br>';
                        echo '<button class="btn btn-warning btn-sm" onclick="testDirectCall(' . $product['id'] . ')">Direct Function Call Test</button>';
                        
                        echo '<div class="mt-3">';
                        echo '<button class="btn btn-info" onclick="testFunctions()">Test Function Availability</button>';
                        echo '</div>';
                        
                        echo '<div id="test-results" class="mt-3"></div>';
                    } else {
                        echo '<div class="alert alert-warning">No products available for testing.</div>';
                    }
                } catch (PDOException $e) {
                    echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                }
                ?>
            </div>
        </div>
        
        <!-- Console Log -->
        <div class="card">
            <div class="card-header">
                <h3>Console Log</h3>
            </div>
            <div class="card-body">
                <div id="console-log" style="background: #f8f9fa; padding: 10px; border-radius: 5px; font-family: monospace; max-height: 300px; overflow-y: auto;">
                    <div>Console output will appear here...</div>
                </div>
                <button class="btn btn-secondary btn-sm mt-2" onclick="clearConsoleLog()">Clear Log</button>
            </div>
        </div>
    </div>
    
    <!-- Include navbar to get the favorites badge -->
    <?php include 'includes/navbar.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/navbar.js"></script>
    <script src="assets/js/global-cart.js"></script>
    <script src="assets/js/global-favorites.js"></script>
    <script src="assets/js/favorites.js"></script>
    
    <script>
        // Override console.log to capture output
        const originalConsoleLog = console.log;
        const originalConsoleError = console.error;
        const consoleLogDiv = document.getElementById('console-log');
        
        function addToConsoleLog(message, type = 'log') {
            const timestamp = new Date().toLocaleTimeString();
            const logEntry = document.createElement('div');
            logEntry.style.color = type === 'error' ? 'red' : (type === 'warn' ? 'orange' : 'black');
            logEntry.textContent = `[${timestamp}] ${message}`;
            consoleLogDiv.appendChild(logEntry);
            consoleLogDiv.scrollTop = consoleLogDiv.scrollHeight;
        }
        
        console.log = function(...args) {
            originalConsoleLog.apply(console, args);
            addToConsoleLog(args.join(' '), 'log');
        };
        
        console.error = function(...args) {
            originalConsoleError.apply(console, args);
            addToConsoleLog(args.join(' '), 'error');
        };
        
        function clearConsoleLog() {
            consoleLogDiv.innerHTML = '<div>Console output will appear here...</div>';
        }
        
        function testDirectCall(productId) {
            console.log('Testing direct function call...');
            try {
                if (typeof window.addToCart === 'function') {
                    console.log('Calling addToCart directly...');
                    window.addToCart(productId, 1, null, null, true);
                } else {
                    alert('addToCart function not found!');
                }
            } catch (error) {
                console.error('Error calling addToCart:', error);
                alert('Error: ' + error.message);
            }
        }

        function testFunctions() {
            const resultsDiv = document.getElementById('test-results');
            let results = [];

            // Test function availability
            results.push(`addToCart function: ${typeof window.addToCart === 'function' ? '✅ Available' : '❌ Missing'}`);
            results.push(`updateFavoritesCountInNavbar function: ${typeof updateFavoritesCountInNavbar === 'function' ? '✅ Available' : '❌ Missing'}`);
            results.push(`updateCartCountInNavbar function: ${typeof updateCartCountInNavbar === 'function' ? '✅ Available' : '❌ Missing'}`);
            results.push(`showNotification function: ${typeof showNotification === 'function' ? '✅ Available' : '❌ Missing'}`);
            results.push(`isUserLoggedIn function: ${typeof isUserLoggedIn === 'function' ? '✅ Available' : '❌ Missing'}`);

            // Test login status
            if (typeof isUserLoggedIn === 'function') {
                results.push(`User logged in: ${isUserLoggedIn() ? '✅ Yes' : '❌ No'}`);
            }

            // Test navbar elements
            const favoritesLink = document.querySelector('a[href="favorites.php"]');
            const favoritesCount = document.querySelector('a[href="favorites.php"] .favorites-count');
            const cartLink = document.querySelector('a[href="cart.php"]');
            const cartCount = document.querySelector('a[href="cart.php"] .cart-count');

            results.push(`Favorites link in navbar: ${favoritesLink ? '✅ Found' : '❌ Missing'}`);
            results.push(`Favorites count element: ${favoritesCount ? '✅ Found' : '❌ Missing'}`);
            results.push(`Cart link in navbar: ${cartLink ? '✅ Found' : '❌ Missing'}`);
            results.push(`Cart count element: ${cartCount ? '✅ Found' : '❌ Missing'}`);

            resultsDiv.innerHTML = `
                <div class="alert alert-info">
                    <h6>Function and Element Tests:</h6>
                    ${results.map(result => `<div>${result}</div>`).join('')}
                </div>
            `;

            console.log('Function test completed');
        }
        
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Page loaded, testing functions...');
            testFunctions();
        });
    </script>
</body>
</html>
