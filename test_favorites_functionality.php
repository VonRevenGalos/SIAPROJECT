<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favorites Functionality Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container mt-4">
        <h1>Favorites Functionality Test</h1>
        
        <?php
        require_once 'includes/session.php';
        require_once 'db.php';
        
        if (!isLoggedIn()) {
            echo '<div class="alert alert-warning">Please <a href="login.php">login</a> to test favorites functionality.</div>';
            echo '</body></html>';
            exit();
        }
        
        $user_id = getUserId();
        ?>
        
        <div class="alert alert-success">
            ✓ User is logged in (ID: <?php echo $user_id; ?>)
        </div>
        
        <!-- Test 1: Navbar Badge Check -->
        <div class="card mb-4">
            <div class="card-header">
                <h3>Test 1: Navbar Favorites Badge</h3>
            </div>
            <div class="card-body">
                <p>Current navbar favorites badge HTML:</p>
                <div class="bg-light p-3 rounded">
                    <code id="navbar-badge-html">Loading...</code>
                </div>
                <p class="mt-2">JavaScript should target: <code>a[href="favorites.php"] .favorites-count</code></p>
                <button class="btn btn-info" onclick="checkNavbarBadge()">Check Badge Selector</button>
                <div id="badge-test-result" class="mt-2"></div>
            </div>
        </div>
        
        <!-- Test 2: Current Favorites Count -->
        <div class="card mb-4">
            <div class="card-header">
                <h3>Test 2: Current Favorites</h3>
            </div>
            <div class="card-body">
                <?php
                try {
                    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM favorites WHERE user_id = ?");
                    $stmt->execute([$user_id]);
                    $favorites_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
                    
                    echo "<p><strong>Database count:</strong> {$favorites_count} favorites</p>";
                    
                    if ($favorites_count > 0) {
                        $stmt = $pdo->prepare("
                            SELECT p.id, p.title, p.price, f.created_at 
                            FROM favorites f 
                            JOIN products p ON f.product_id = p.id 
                            WHERE f.user_id = ? 
                            ORDER BY f.created_at DESC 
                            LIMIT 5
                        ");
                        $stmt->execute([$user_id]);
                        $favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        echo "<table class='table table-sm'>";
                        echo "<tr><th>Product ID</th><th>Title</th><th>Price</th><th>Added</th><th>Action</th></tr>";
                        foreach ($favorites as $fav) {
                            echo "<tr>";
                            echo "<td>{$fav['id']}</td>";
                            echo "<td>{$fav['title']}</td>";
                            echo "<td>₱" . number_format($fav['price'], 2) . "</td>";
                            echo "<td>" . date('M j, Y', strtotime($fav['created_at'])) . "</td>";
                            echo "<td><button class='btn btn-sm btn-danger' onclick='testRemoveFavorite({$fav['id']})'>Remove</button></td>";
                            echo "</tr>";
                        }
                        echo "</table>";
                    }
                } catch (PDOException $e) {
                    echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
                }
                ?>
                <button class="btn btn-info" onclick="location.reload()">Refresh</button>
            </div>
        </div>
        
        <!-- Test 3: Add to Favorites Test -->
        <div class="card mb-4">
            <div class="card-header">
                <h3>Test 3: Add to Favorites</h3>
            </div>
            <div class="card-body">
                <p>Test adding products to favorites:</p>
                <?php
                try {
                    $stmt = $pdo->prepare("SELECT id, title, price FROM products WHERE stock > 0 LIMIT 5");
                    $stmt->execute();
                    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    if (count($products) > 0) {
                        echo "<div class='row'>";
                        foreach ($products as $product) {
                            echo "<div class='col-md-4 mb-3'>";
                            echo "<div class='card'>";
                            echo "<div class='card-body'>";
                            echo "<h6 class='card-title'>{$product['title']}</h6>";
                            echo "<p class='card-text'>₱" . number_format($product['price'], 2) . "</p>";
                            echo "<button class='btn btn-outline-danger btn-sm' onclick='addToFavorites({$product['id']})'>";
                            echo "<i class='fas fa-heart'></i> Add to Favorites";
                            echo "</button>";
                            echo "</div>";
                            echo "</div>";
                            echo "</div>";
                        }
                        echo "</div>";
                    } else {
                        echo "<p><em>No products available for testing.</em></p>";
                    }
                } catch (PDOException $e) {
                    echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
                }
                ?>
            </div>
        </div>
        
        <!-- Test 4: JavaScript Functions Test -->
        <div class="card mb-4">
            <div class="card-header">
                <h3>Test 4: JavaScript Functions</h3>
            </div>
            <div class="card-body">
                <button class="btn btn-primary" onclick="testJavaScriptFunctions()">Test All Functions</button>
                <div id="js-test-results" class="mt-3"></div>
            </div>
        </div>
        
        <!-- Test Results -->
        <div class="card mb-4">
            <div class="card-header">
                <h3>Test Results</h3>
            </div>
            <div class="card-body">
                <div id="test-results"></div>
            </div>
        </div>
    </div>
    
    <!-- Include the navbar to get the favorites badge -->
    <?php include 'includes/navbar.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/navbar.js"></script>
    <script src="assets/js/global-cart.js"></script>
    <script src="assets/js/global-favorites.js"></script>
    
    <script>
        function checkNavbarBadge() {
            const navbarHtml = document.querySelector('a[href="favorites.php"]');
            const badgeElement = document.querySelector('a[href="favorites.php"] .favorites-count');
            const resultDiv = document.getElementById('badge-test-result');
            const htmlDiv = document.getElementById('navbar-badge-html');
            
            if (navbarHtml) {
                htmlDiv.textContent = navbarHtml.outerHTML;
            } else {
                htmlDiv.textContent = 'Favorites link not found in navbar';
            }
            
            if (badgeElement) {
                resultDiv.innerHTML = `
                    <div class="alert alert-success">
                        ✅ Badge element found!<br>
                        Current text: "${badgeElement.textContent}"<br>
                        Display style: "${badgeElement.style.display}"<br>
                        Computed display: "${window.getComputedStyle(badgeElement).display}"
                    </div>
                `;
            } else {
                resultDiv.innerHTML = `
                    <div class="alert alert-danger">
                        ❌ Badge element NOT found!<br>
                        Selector: a[href="favorites.php"] .favorites-count
                    </div>
                `;
            }
        }
        
        function testJavaScriptFunctions() {
            const resultsDiv = document.getElementById('js-test-results');
            let results = [];
            
            // Test if functions exist
            results.push(`addToFavorites function: ${typeof window.addToFavorites === 'function' ? '✅ Found' : '❌ Missing'}`);
            results.push(`updateFavoritesCountInNavbar function: ${typeof updateFavoritesCountInNavbar === 'function' ? '✅ Found' : '❌ Missing'}`);
            results.push(`isUserLoggedIn function: ${typeof isUserLoggedIn === 'function' ? '✅ Found' : '❌ Missing'}`);
            results.push(`showNotification function: ${typeof showNotification === 'function' ? '✅ Found' : '❌ Missing'}`);
            
            // Test login status
            if (typeof isUserLoggedIn === 'function') {
                results.push(`User logged in check: ${isUserLoggedIn() ? '✅ Logged in' : '❌ Not logged in'}`);
            }
            
            resultsDiv.innerHTML = `
                <div class="alert alert-info">
                    <h6>Function Availability:</h6>
                    ${results.map(result => `<div>${result}</div>`).join('')}
                </div>
            `;
        }
        
        function testRemoveFavorite(productId) {
            if (confirm('Remove this item from favorites?')) {
                fetch('favorites.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=remove&product_id=${productId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Removed from favorites!', 'success');
                        updateFavoritesCountInNavbar(data.favorites_count);
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showNotification(data.message || 'Failed to remove', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('An error occurred', 'error');
                });
            }
        }
        
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            checkNavbarBadge();
            testJavaScriptFunctions();
            
            // Load current favorites count
            if (typeof isUserLoggedIn === 'function' && isUserLoggedIn()) {
                fetch('get_favorites_count.php')
                    .then(response => response.json())
                    .then(data => {
                        updateFavoritesCountInNavbar(data.count);
                        console.log('Loaded favorites count:', data.count);
                    })
                    .catch(error => {
                        console.error('Error loading favorites count:', error);
                    });
            }
        });
    </script>
</body>
</html>
