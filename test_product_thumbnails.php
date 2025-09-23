<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Product Thumbnails</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/product.css">
</head>
<body>
    <?php
    require_once 'includes/session.php';
    require_once 'db.php';
    
    // Get a product with thumbnails for testing
    try {
        $stmt = $pdo->prepare("
            SELECT id, title, price, image, brand, color, stock, category, description, 
                   height, width, collection, thumbnail1, thumbnail2, thumbnail3 
            FROM products 
            WHERE thumbnail1 IS NOT NULL AND thumbnail1 != '' 
            LIMIT 1
        ");
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$product) {
            // Get any product if no thumbnails found
            $stmt = $pdo->prepare("
                SELECT id, title, price, image, brand, color, stock, category, description, 
                       height, width, collection, thumbnail1, thumbnail2, thumbnail3 
                FROM products 
                LIMIT 1
            ");
            $stmt->execute();
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        if ($product) {
            // Clean image paths
            $product['image'] = trim($product['image'] ?? '');
            $product['thumbnail1'] = trim($product['thumbnail1'] ?? '');
            $product['thumbnail2'] = trim($product['thumbnail2'] ?? '');
            $product['thumbnail3'] = trim($product['thumbnail3'] ?? '');
        }
        
    } catch (PDOException $e) {
        $product = null;
        $error = $e->getMessage();
    }
    ?>
    
    <div class="container mt-4">
        <h1>Product Thumbnail Test</h1>
        
        <?php if (isset($error)): ?>
        <div class="alert alert-danger">Database Error: <?php echo htmlspecialchars($error); ?></div>
        <?php elseif (!$product): ?>
        <div class="alert alert-warning">No products found in database.</div>
        <?php else: ?>
        
        <div class="alert alert-info">
            <h5>Testing Product: <?php echo htmlspecialchars($product['title']); ?></h5>
            <p><strong>Product ID:</strong> <?php echo $product['id']; ?></p>
            <p><strong>Main Image:</strong> <?php echo htmlspecialchars($product['image']); ?></p>
            <p><strong>Thumbnail 1:</strong> <?php echo htmlspecialchars($product['thumbnail1'] ?: 'None'); ?></p>
            <p><strong>Thumbnail 2:</strong> <?php echo htmlspecialchars($product['thumbnail2'] ?: 'None'); ?></p>
            <p><strong>Thumbnail 3:</strong> <?php echo htmlspecialchars($product['thumbnail3'] ?: 'None'); ?></p>
        </div>
        
        <!-- Product Gallery Test -->
        <div class="row">
            <div class="col-lg-6">
                <div class="product-gallery">
                    <!-- Main Image Container -->
                    <div class="main-image-container">
                        <div class="image-zoom-container">
                            <div class="image-loading-indicator" id="imageLoadingIndicator">
                                <i class="fas fa-spinner fa-spin"></i>
                                <span>Loading...</span>
                            </div>
                            <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($product['title']); ?>" 
                                 id="mainProductImage" class="main-product-image">
                        </div>
                        
                        <!-- Image Navigation Arrows -->
                        <button class="image-nav-btn prev-btn" onclick="previousImage()">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button class="image-nav-btn next-btn" onclick="nextImage()">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                    
                    <!-- Thumbnail Gallery -->
                    <div class="thumbnail-gallery">
                        <div class="thumbnail-container">
                            <?php 
                            // Create array of all available images
                            $thumbnails = [];
                            
                            // Add main image as first thumbnail
                            if (!empty($product['image'])) {
                                $thumbnails[] = $product['image'];
                            }
                            
                            // Add additional thumbnails if they exist and are different from main image
                            if (!empty($product['thumbnail1']) && $product['thumbnail1'] !== $product['image']) {
                                $thumbnails[] = $product['thumbnail1'];
                            }
                            if (!empty($product['thumbnail2']) && $product['thumbnail2'] !== $product['image']) {
                                $thumbnails[] = $product['thumbnail2'];
                            }
                            if (!empty($product['thumbnail3']) && $product['thumbnail3'] !== $product['image']) {
                                $thumbnails[] = $product['thumbnail3'];
                            }
                            
                            // Display thumbnails
                            foreach ($thumbnails as $index => $thumbnail): 
                                $cleanThumbnail = trim($thumbnail);
                                if (!empty($cleanThumbnail)):
                            ?>
                            <div class="thumbnail-item <?php echo $index === 0 ? 'active' : ''; ?>" 
                                 onclick="changeMainImage('<?php echo htmlspecialchars($cleanThumbnail); ?>', <?php echo $index; ?>)">
                                <img src="<?php echo htmlspecialchars($cleanThumbnail); ?>" 
                                     alt="Product Image <?php echo $index + 1; ?>" 
                                     class="thumbnail-img"
                                     onerror="this.style.display='none'">
                            </div>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3>Thumbnail Test Controls</h3>
                    </div>
                    <div class="card-body">
                        <button class="btn btn-primary" onclick="testThumbnailClicks()">
                            Test All Thumbnail Clicks
                        </button>
                        
                        <button class="btn btn-secondary" onclick="testImageNavigation()">
                            Test Image Navigation
                        </button>
                        
                        <button class="btn btn-info" onclick="debugThumbnails()">
                            Debug Thumbnail Info
                        </button>
                        
                        <div class="mt-3">
                            <h5>Debug Output:</h5>
                            <div id="debug-output" class="border p-2" style="height: 200px; overflow-y: auto; background: #f8f9fa;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <?php endif; ?>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/product.js"></script>
    
    <script>
        function log(message) {
            const debugOutput = document.getElementById('debug-output');
            const timestamp = new Date().toLocaleTimeString();
            debugOutput.innerHTML += `[${timestamp}] ${message}<br>`;
            debugOutput.scrollTop = debugOutput.scrollHeight;
            console.log(message);
        }
        
        function testThumbnailClicks() {
            log('=== Testing Thumbnail Clicks ===');
            const thumbnails = document.querySelectorAll('.thumbnail-item');
            log(`Found ${thumbnails.length} thumbnails`);
            
            thumbnails.forEach((thumbnail, index) => {
                const img = thumbnail.querySelector('img');
                if (img) {
                    log(`Thumbnail ${index}: ${img.src}`);
                    log(`Onclick attribute: ${thumbnail.getAttribute('onclick')}`);
                    
                    // Test click
                    setTimeout(() => {
                        log(`Clicking thumbnail ${index}...`);
                        thumbnail.click();
                    }, index * 1000);
                }
            });
        }
        
        function testImageNavigation() {
            log('=== Testing Image Navigation ===');
            log('Testing previous image...');
            if (typeof previousImage === 'function') {
                previousImage();
                setTimeout(() => {
                    log('Testing next image...');
                    if (typeof nextImage === 'function') {
                        nextImage();
                    } else {
                        log('ERROR: nextImage function not found');
                    }
                }, 1000);
            } else {
                log('ERROR: previousImage function not found');
            }
        }
        
        function debugThumbnails() {
            log('=== Debug Thumbnail Information ===');
            log(`Current image index: ${typeof currentImageIndex !== 'undefined' ? currentImageIndex : 'undefined'}`);
            log(`Product images array: ${typeof productImages !== 'undefined' ? JSON.stringify(productImages) : 'undefined'}`);
            
            const thumbnails = document.querySelectorAll('.thumbnail-item');
            log(`Total thumbnails found: ${thumbnails.length}`);
            
            thumbnails.forEach((thumbnail, index) => {
                const img = thumbnail.querySelector('img');
                const isActive = thumbnail.classList.contains('active');
                log(`Thumbnail ${index}: ${img ? img.src : 'no image'} (Active: ${isActive})`);
            });
            
            const mainImage = document.getElementById('mainProductImage');
            log(`Main image src: ${mainImage ? mainImage.src : 'not found'}`);
        }
        
        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', function() {
            log('Page loaded, initializing...');
            debugThumbnails();
        });
    </script>
</body>
</html>
