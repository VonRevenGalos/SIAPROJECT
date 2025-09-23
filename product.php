<?php
require_once 'includes/session.php';
require_once 'db.php';

// Check if product ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$product_id = (int)$_GET['id'];

try {
    // Fetch product details - including thumbnail fields for image gallery
    $stmt = $pdo->prepare("SELECT id, title, price, image, brand, color, stock, category, description, height, width, collection, thumbnail1, thumbnail2, thumbnail3 FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$product) {
        header("Location: index.php");
        exit();
    }
    
    // Clean image paths by removing carriage returns and newlines
    $product['image'] = trim($product['image'] ?? '');
    $product['thumbnail1'] = trim($product['thumbnail1'] ?? '');
    $product['thumbnail2'] = trim($product['thumbnail2'] ?? '');
    $product['thumbnail3'] = trim($product['thumbnail3'] ?? '');
    
    // Get current user
    $currentUser = getCurrentUser();
    
    // Determine category page for breadcrumb
    $categoryPage = 'index.php';
    $categoryName = 'Products';
    
    // Check for women products first (more specific)
    if (strpos($product['category'], 'women') !== false || in_array($product['category'], ['womenathletics', 'womenrunning', 'womensneakers'])) {
        $categoryPage = 'women.php';
        $categoryName = 'Women';
    } 
    // Check for kids products
    elseif (strpos($product['category'], 'kids') !== false || in_array($product['category'], ['kidsathletics', 'kidsneakers', 'kidslipon'])) {
        $categoryPage = 'kids.php';
        $categoryName = 'Kids';
    }
    // Check for men products (default for sneakers, running, athletics)
    elseif (strpos($product['category'], 'men') !== false || in_array($product['category'], ['sneakers', 'running', 'athletics'])) {
        $categoryPage = 'men.php';
        $categoryName = 'Men';
    }
    
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['title']); ?> - ShoeARizz</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/product.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="product-hero">
        <div class="container-fluid">
            <div class="row g-0">
                <!-- Product Images Section -->
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
                
                <!-- Product Details Section -->
                <div class="col-lg-6">
                    <div class="product-details-section">
                        <div class="product-details-content">
                            <!-- Breadcrumb -->
                            <nav aria-label="breadcrumb" class="breadcrumb-nav">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                    <li class="breadcrumb-item"><a href="<?php echo $categoryPage; ?>"><?php echo $categoryName; ?></a></li>
                                    <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($product['title']); ?></li>
                                </ol>
                            </nav>
                            
                            <!-- Product Info -->
                            <div class="product-info">
                                <div class="product-brand"><?php echo htmlspecialchars($product['brand'] ?? 'Generic'); ?></div>
                                <h1 class="product-title"><?php echo htmlspecialchars($product['title']); ?></h1>
                                
                                <!-- Product Specifications -->
                                <div class="product-specs">
                                    <div class="spec-grid">
                                        <div class="spec-item">
                                            <span class="spec-label">Color</span>
                                            <span class="spec-value"><?php echo htmlspecialchars($product['color'] ?? 'N/A'); ?></span>
                                        </div>
                                        <div class="spec-item">
                                            <span class="spec-label">Height</span>
                                            <span class="spec-value"><?php echo htmlspecialchars($product['height'] ?? 'N/A'); ?></span>
                                        </div>
                                        <div class="spec-item">
                                            <span class="spec-label">Width</span>
                                            <span class="spec-value"><?php echo htmlspecialchars($product['width'] ?? 'N/A'); ?></span>
                                        </div>
                                        <div class="spec-item">
                                            <span class="spec-label">Collection</span>
                                            <span class="spec-value"><?php echo htmlspecialchars($product['collection'] ?? 'N/A'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Price -->
                                <div class="product-price">
                                    <span class="price-currency">₱</span>
                                    <span class="price-amount"><?php echo number_format($product['price'], 2); ?></span>
                                </div>
                                
                                <!-- Stock Status -->
                                <div class="product-stock">
                                    <?php if ($product['stock'] > 0): ?>
                                        <span class="stock-status in-stock">
                                            <i class="fas fa-check-circle"></i>
                                            In Stock (<?php echo $product['stock']; ?> available)
                                        </span>
                                    <?php else: ?>
                                        <span class="stock-status out-of-stock">
                                            <i class="fas fa-times-circle"></i>
                                            Out of Stock
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Size Selection -->
                                <div class="size-selection">
                                    <h4>Select Size</h4>
                                    <div class="size-options">
                                        <label class="size-option">
                                            <input type="radio" name="size" value="7" required>
                                            <span class="size-label">7</span>
                                        </label>
                                        <label class="size-option">
                                            <input type="radio" name="size" value="7.5" required>
                                            <span class="size-label">7.5</span>
                                        </label>
                                        <label class="size-option">
                                            <input type="radio" name="size" value="8" required>
                                            <span class="size-label">8</span>
                                        </label>
                                        <label class="size-option">
                                            <input type="radio" name="size" value="8.5" required>
                                            <span class="size-label">8.5</span>
                                        </label>
                                        <label class="size-option">
                                            <input type="radio" name="size" value="9" required>
                                            <span class="size-label">9</span>
                                        </label>
                                        <label class="size-option">
                                            <input type="radio" name="size" value="9.5" required>
                                            <span class="size-label">9.5</span>
                                        </label>
                                        <label class="size-option">
                                            <input type="radio" name="size" value="10" required>
                                            <span class="size-label">10</span>
                                        </label>
                                        <label class="size-option">
                                            <input type="radio" name="size" value="10.5" required>
                                            <span class="size-label">10.5</span>
                                        </label>
                                        <label class="size-option">
                                            <input type="radio" name="size" value="11" required>
                                            <span class="size-label">11</span>
                                        </label>
                                        <label class="size-option">
                                            <input type="radio" name="size" value="11.5" required>
                                            <span class="size-label">11.5</span>
                                        </label>
                                        <label class="size-option">
                                            <input type="radio" name="size" value="12" required>
                                            <span class="size-label">12</span>
                                        </label>
                                    </div>
                                </div>
                                
                                <!-- Action Buttons -->
                                <div class="action-buttons">
                                    <?php if (isLoggedIn()): ?>
                                        <button class="btn-add-cart" onclick="addToCartWithSize(<?php echo $product['id']; ?>, 1, this)"
                                                <?php echo ($product['stock'] <= 0) ? 'disabled' : ''; ?>>
                                            <i class="fas fa-shopping-cart"></i>
                                            Add to Cart
                                        </button>
                                    <?php else: ?>
                                        <a href="signup.php" class="btn-add-cart">
                                            <i class="fas fa-user-plus"></i>
                                            Sign Up to Add to Cart
                                        </a>
                                    <?php endif; ?>
                                    <button class="btn-favorite" onclick="addToFavorites(<?php echo $product['id']; ?>)">
                                        <i class="fas fa-heart"></i>
                                        Add to Favorites
                                    </button>
                                </div>
                                
                                <!-- Product Description -->
                                <div class="product-description">
                                    <h4>Description</h4>
                                    <p><?php echo nl2br(htmlspecialchars($product['description'] ?? 'No description available.')); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Product Features Section -->
    <div class="product-features">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h3 class="features-title">Product Features</h3>
                    <div class="features-grid">
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-shipping-fast"></i>
                            </div>
                            <div class="feature-content">
                                <h5>Free Shipping</h5>
                                <p>Free shipping on orders over ₱2,000</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-undo"></i>
                            </div>
                            <div class="feature-content">
                                <h5>Easy Returns</h5>
                                <p>30-day return policy</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div class="feature-content">
                                <h5>Quality Guarantee</h5>
                                <p>Premium quality materials</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-headset"></i>
                            </div>
                            <div class="feature-content">
                                <h5>24/7 Support</h5>
                                <p>Customer support available</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Reviews and Feedback Section -->
    <div class="reviews-section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="reviews-header">
                        <h3 class="reviews-title">Customer Reviews & Feedback</h3>
                        <div class="reviews-summary">
                            <div class="rating-overview">
                                <div class="average-rating">
                                    <span class="rating-number">4.8</span>
                                    <div class="stars">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <span class="rating-count">Based on 127 reviews</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Review Filters -->
                    <div class="review-filters">
                        <div class="filter-buttons">
                            <button class="filter-btn active" data-filter="all">All Reviews</button>
                            <button class="filter-btn" data-filter="5">5 Stars</button>
                            <button class="filter-btn" data-filter="4">4 Stars</button>
                            <button class="filter-btn" data-filter="3">3 Stars</button>
                            <button class="filter-btn" data-filter="2">2 Stars</button>
                            <button class="filter-btn" data-filter="1">1 Star</button>
                        </div>
                        <div class="sort-options">
                            <select class="sort-select" id="reviewSort">
                                <option value="newest">Newest First</option>
                                <option value="oldest">Oldest First</option>
                                <option value="highest">Highest Rating</option>
                                <option value="lowest">Lowest Rating</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Reviews List -->
                    <div class="reviews-list" id="reviewsList">
                        <!-- Review Item 1 -->
                        <div class="review-item" data-rating="5" data-date="2025-01-15">
                            <div class="review-header">
                                <div class="reviewer-info">
                                    <div class="reviewer-avatar">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="reviewer-details">
                                        <div class="reviewer-name">Sarah M.</div>
                                        <div class="review-date">Verified Purchase • 2 days ago</div>
                                    </div>
                                </div>
                                <div class="review-rating">
                                    <div class="stars">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="review-content">
                                <h5 class="review-title">Absolutely love these shoes!</h5>
                                <p class="review-text">The quality is exceptional and they're incredibly comfortable. Perfect fit and the design is exactly what I was looking for. Will definitely buy again!</p>
                                <div class="review-helpful">
                                    <button class="helpful-btn" onclick="markHelpful(this)">
                                        <i class="fas fa-thumbs-up"></i>
                                        <span>Helpful (12)</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Review Item 2 -->
                        <div class="review-item" data-rating="4" data-date="2025-01-12">
                            <div class="review-header">
                                <div class="reviewer-info">
                                    <div class="reviewer-avatar">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="reviewer-details">
                                        <div class="reviewer-name">Michael R.</div>
                                        <div class="review-date">Verified Purchase • 5 days ago</div>
                                    </div>
                                </div>
                                <div class="review-rating">
                                    <div class="stars">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="far fa-star"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="review-content">
                                <h5 class="review-title">Great shoes, minor sizing issue</h5>
                                <p class="review-text">Overall very satisfied with the purchase. The shoes are comfortable and well-made. Only issue is they run slightly small, so I'd recommend ordering half a size up.</p>
                                <div class="review-helpful">
                                    <button class="helpful-btn" onclick="markHelpful(this)">
                                        <i class="fas fa-thumbs-up"></i>
                                        <span>Helpful (8)</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Review Item 3 -->
                        <div class="review-item" data-rating="5" data-date="2025-01-10">
                            <div class="review-header">
                                <div class="reviewer-info">
                                    <div class="reviewer-avatar">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="reviewer-details">
                                        <div class="reviewer-name">Jennifer L.</div>
                                        <div class="review-date">Verified Purchase • 1 week ago</div>
                                    </div>
                                </div>
                                <div class="review-rating">
                                    <div class="stars">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="review-content">
                                <h5 class="review-title">Perfect for daily wear</h5>
                                <p class="review-text">These shoes have become my go-to for everyday activities. The cushioning is excellent and they look great with any outfit. Highly recommend!</p>
                                <div class="review-helpful">
                                    <button class="helpful-btn" onclick="markHelpful(this)">
                                        <i class="fas fa-thumbs-up"></i>
                                        <span>Helpful (15)</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Review Item 4 -->
                        <div class="review-item" data-rating="3" data-date="2025-01-08">
                            <div class="review-header">
                                <div class="reviewer-info">
                                    <div class="reviewer-avatar">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="reviewer-details">
                                        <div class="reviewer-name">David K.</div>
                                        <div class="review-date">Verified Purchase • 1 week ago</div>
                                    </div>
                                </div>
                                <div class="review-rating">
                                    <div class="stars">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="far fa-star"></i>
                                        <i class="far fa-star"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="review-content">
                                <h5 class="review-title">Decent quality, could be better</h5>
                                <p class="review-text">The shoes are okay for the price, but I expected a bit more comfort. The design is nice but the sole could be more supportive for long walks.</p>
                                <div class="review-helpful">
                                    <button class="helpful-btn" onclick="markHelpful(this)">
                                        <i class="fas fa-thumbs-up"></i>
                                        <span>Helpful (3)</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Load More Reviews -->
                    <div class="load-more-section">
                        <button class="btn btn-load-more" onclick="loadMoreReviews()">
                            <i class="fas fa-plus"></i>
                            Load More Reviews
                        </button>
                    </div>
                    
                    <!-- Write Review Section -->
                    <div class="write-review-section">
                        <h4>Write a Review</h4>
                        <p>Share your experience with this product</p>
                        <button class="btn btn-write-review" onclick="openReviewModal()">
                            <i class="fas fa-edit"></i>
                            Write a Review
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="assets/js/navbar.js"></script>
    <?php if (isLoggedIn()): ?>
    <script src="assets/js/global-cart.js"></script>
    <script src="assets/js/global-favorites.js"></script>
    <?php endif; ?>
    <script src="assets/js/product.js"></script>
</body>
</html>
