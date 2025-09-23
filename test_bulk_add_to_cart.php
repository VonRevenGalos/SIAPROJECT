<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulk Add to Cart Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container mt-4">
        <h1>Bulk Add to Cart Functionality Test</h1>
        
        <?php
        require_once 'includes/session.php';
        require_once 'db.php';
        
        if (!isLoggedIn()) {
            echo '<div class="alert alert-warning">Please <a href="login.php">login</a> to test bulk add to cart functionality.</div>';
            echo '</body></html>';
            exit();
        }
        
        $user_id = getUserId();
        ?>
        
        <div class="alert alert-success">
            ✓ User is logged in (ID: <?php echo $user_id; ?>)
        </div>
        
        <!-- Test 1: Current Favorites -->
        <div class="card mb-4">
            <div class="card-header">
                <h3>Current Favorites</h3>
            </div>
            <div class="card-body">
                <?php
                try {
                    $stmt = $pdo->prepare("
                        SELECT p.*, f.created_at as favorited_at 
                        FROM favorites f 
                        JOIN products p ON f.product_id = p.id 
                        WHERE f.user_id = ? 
                        ORDER BY f.created_at DESC
                        LIMIT 5
                    ");
                    $stmt->execute([$user_id]);
                    $favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    if (count($favorites) > 0) {
                        echo "<p>Found " . count($favorites) . " favorites (showing first 5):</p>";
                        echo "<table class='table table-sm'>";
                        echo "<tr><th>Select</th><th>ID</th><th>Product</th><th>Price</th><th>Stock</th><th>Added</th></tr>";
                        foreach ($favorites as $product) {
                            echo "<tr>";
                            echo "<td><input type='checkbox' class='form-check-input test-select' value='{$product['id']}'></td>";
                            echo "<td>{$product['id']}</td>";
                            echo "<td>{$product['title']}</td>";
                            echo "<td>₱" . number_format($product['price'], 2) . "</td>";
                            echo "<td>" . ($product['stock'] > 0 ? $product['stock'] : '<span class="text-danger">Out of Stock</span>') . "</td>";
                            echo "<td>" . date('M j, Y', strtotime($product['favorited_at'])) . "</td>";
                            echo "</tr>";
                        }
                        echo "</table>";
                        
                        echo "<div class='mt-3'>";
                        echo "<button class='btn btn-primary me-2' onclick='selectAllTest()'>Select All</button>";
                        echo "<button class='btn btn-success me-2' onclick='testBulkAddToCart()' id='test-bulk-btn' disabled>Test Bulk Add to Cart</button>";
                        echo "<button class='btn btn-info' onclick='updateTestSelection()'>Update Selection</button>";
                        echo "</div>";
                        echo "<div id='test-selection-info' class='mt-2'></div>";
                    } else {
                        echo "<p><em>No favorites found. <a href='men.php'>Add some products to favorites first</a>.</em></p>";
                    }
                } catch (PDOException $e) {
                    echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
                }
                ?>
            </div>
        </div>
        
        <!-- Test 2: Current Cart -->
        <div class="card mb-4">
            <div class="card-header">
                <h3>Current Cart Contents</h3>
            </div>
            <div class="card-body">
                <?php
                try {
                    $stmt = $pdo->prepare("
                        SELECT c.*, p.title, p.price 
                        FROM cart c 
                        JOIN products p ON c.product_id = p.id 
                        WHERE c.user_id = ? 
                        ORDER BY c.date_added DESC
                    ");
                    $stmt->execute([$user_id]);
                    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    if (count($cart_items) > 0) {
                        echo "<p>Found " . count($cart_items) . " items in cart:</p>";
                        echo "<table class='table table-sm'>";
                        echo "<tr><th>Cart ID</th><th>Product</th><th>Quantity</th><th>Size</th><th>Price</th><th>Added</th></tr>";
                        foreach ($cart_items as $item) {
                            echo "<tr>";
                            echo "<td>{$item['cart_id']}</td>";
                            echo "<td>{$item['title']}</td>";
                            echo "<td>{$item['quantity']}</td>";
                            echo "<td>" . ($item['size'] ?: 'N/A') . "</td>";
                            echo "<td>₱" . number_format($item['price'], 2) . "</td>";
                            echo "<td>{$item['date_added']}</td>";
                            echo "</tr>";
                        }
                        echo "</table>";
                        
                        // Calculate total
                        $total_quantity = array_sum(array_column($cart_items, 'quantity'));
                        echo "<p><strong>Total items in cart: {$total_quantity}</strong></p>";
                    } else {
                        echo "<p><em>Cart is empty</em></p>";
                    }
                } catch (PDOException $e) {
                    echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
                }
                ?>
                <button class="btn btn-info" onclick="location.reload()">Refresh</button>
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
    
    <!-- Include the navbar to get the cart badge -->
    <?php include 'includes/navbar.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/navbar.js"></script>
    <script src="assets/js/global-cart.js"></script>
    <script src="assets/js/global-favorites.js"></script>
    
    <script>
        let selectedTestItems = [];
        
        function updateTestSelection() {
            const checkboxes = document.querySelectorAll('.test-select:checked');
            const bulkBtn = document.getElementById('test-bulk-btn');
            const infoDiv = document.getElementById('test-selection-info');
            
            selectedTestItems = Array.from(checkboxes).map(cb => cb.value);
            
            if (selectedTestItems.length > 0) {
                bulkBtn.disabled = false;
                bulkBtn.innerHTML = `Test Bulk Add to Cart (${selectedTestItems.length})`;
                infoDiv.innerHTML = `<div class="alert alert-info">Selected ${selectedTestItems.length} item(s): ${selectedTestItems.join(', ')}</div>`;
            } else {
                bulkBtn.disabled = true;
                bulkBtn.innerHTML = 'Test Bulk Add to Cart';
                infoDiv.innerHTML = '';
            }
        }
        
        function selectAllTest() {
            const checkboxes = document.querySelectorAll('.test-select');
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            
            checkboxes.forEach(cb => cb.checked = !allChecked);
            updateTestSelection();
        }
        
        function testBulkAddToCart() {
            if (selectedTestItems.length === 0) return;
            
            const resultsDiv = document.getElementById('test-results');
            resultsDiv.innerHTML = '<div class="alert alert-info">Testing bulk add to cart...</div>';
            
            const formData = new FormData();
            formData.append('action', 'add_selected_to_cart');
            selectedTestItems.forEach(id => {
                formData.append('product_ids[]', id);
            });
            
            fetch('favorites.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log('Response:', data);
                
                if (data.success) {
                    resultsDiv.innerHTML = `
                        <div class="alert alert-success">
                            <h5>✅ Success!</h5>
                            <p><strong>Message:</strong> ${data.message}</p>
                            <p><strong>Added to cart:</strong> ${data.added_count} item(s)</p>
                            <p><strong>Cart count:</strong> ${data.cart_count}</p>
                            <p><strong>Remaining favorites:</strong> ${data.remaining_favorites}</p>
                            ${data.out_of_stock_count > 0 ? `<p><strong>Out of stock:</strong> ${data.out_of_stock_items.join(', ')}</p>` : ''}
                        </div>
                    `;
                    
                    // Update cart badge
                    const cartIcon = document.querySelector('a[href="cart.php"] .cart-count');
                    if (cartIcon && data.cart_count > 0) {
                        cartIcon.textContent = data.cart_count;
                        cartIcon.style.display = 'flex';
                    }
                    
                    // Refresh page after 2 seconds
                    setTimeout(() => location.reload(), 2000);
                } else {
                    resultsDiv.innerHTML = `
                        <div class="alert alert-danger">
                            <h5>❌ Failed!</h5>
                            <p><strong>Error:</strong> ${data.message}</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                resultsDiv.innerHTML = `
                    <div class="alert alert-danger">
                        <h5>❌ Error!</h5>
                        <p><strong>Error:</strong> ${error.toString()}</p>
                    </div>
                `;
            });
        }
        
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            // Add event listeners to checkboxes
            document.querySelectorAll('.test-select').forEach(cb => {
                cb.addEventListener('change', updateTestSelection);
            });
            
            updateTestSelection();
        });
    </script>
</body>
</html>
