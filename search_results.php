<?php
require_once 'includes/session.php';
require_once 'db.php';

// Get search query and sanitize
$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$sort = $_GET['sort'] ?? 'relevance';
$category = $_GET['category'] ?? '';
$brand = $_GET['brand'] ?? '';
$color = $_GET['color'] ?? '';
$price_min = $_GET['price_min'] ?? '';
$price_max = $_GET['price_max'] ?? '';

// If no search query, redirect to homepage
if (empty($query)) {
    header("Location: index.php");
    exit();
}

try {
    // Enhanced smart search algorithm with duplicate prevention
    $searchTerm = strtolower($query);
    $searchWords = array_filter(explode(' ', $searchTerm));
    $searchLength = strlen($searchTerm);
    
    // Build comprehensive search conditions with improved relevance
    $searchConditions = [];
    $params = [];
    $paramCounter = 0;
    
    // For single character searches, be more specific
    if ($searchLength == 1) {
        // Single letter search - look for titles that start with that letter
        $searchConditions[] = "LOWER(SUBSTRING(title, 1, 1)) = :param_" . ++$paramCounter;
        $params["param_$paramCounter"] = $searchTerm;
        
        // Also check brand names that start with that letter
        $searchConditions[] = "LOWER(SUBSTRING(brand, 1, 1)) = :param_" . ++$paramCounter;
        $params["param_$paramCounter"] = $searchTerm;
        
        // Check color names that start with that letter
        $searchConditions[] = "LOWER(SUBSTRING(color, 1, 1)) = :param_" . ++$paramCounter;
        $params["param_$paramCounter"] = $searchTerm;
    } else {
        // Multi-character search with enhanced logic
        
        // TIER 1: Exact matches (highest priority)
        $searchConditions[] = "(
            LOWER(title) = :param_" . ++$paramCounter . " OR
            LOWER(brand) = :param_" . $paramCounter . " OR
            LOWER(category) = :param_" . $paramCounter . " OR
            LOWER(collection) = :param_" . $paramCounter . "
        )";
        $params["param_$paramCounter"] = $searchTerm;
        
        // TIER 2: Starts with query (high priority)
        $searchConditions[] = "(
            LOWER(title) LIKE :param_" . ++$paramCounter . " OR
            LOWER(brand) LIKE :param_" . $paramCounter . " OR
            LOWER(category) LIKE :param_" . $paramCounter . " OR
            LOWER(collection) LIKE :param_" . $paramCounter . "
        )";
        $params["param_$paramCounter"] = $searchTerm . '%';
        
        // TIER 3: Contains query (medium priority) - exclude description to avoid false color matches
        $searchConditions[] = "(
            LOWER(title) LIKE :param_" . ++$paramCounter . " OR
            LOWER(brand) LIKE :param_" . $paramCounter . " OR
            LOWER(category) LIKE :param_" . $paramCounter . " OR
            LOWER(collection) LIKE :param_" . $paramCounter . "
        )";
        $params["param_$paramCounter"] = '%' . $searchTerm . '%';
        
        // TIER 4: Individual word matches (for multi-word queries) - exclude description to avoid false color matches
        foreach ($searchWords as $index => $word) {
            if (strlen($word) > 1) { // Only words with 2+ characters
                $searchConditions[] = "(
                    LOWER(title) LIKE :param_" . ++$paramCounter . " OR
                    LOWER(brand) LIKE :param_" . $paramCounter . " OR
                    LOWER(category) LIKE :param_" . $paramCounter . " OR
                    LOWER(collection) LIKE :param_" . $paramCounter . "
                )";
                $params["param_$paramCounter"] = '%' . $word . '%';
            }
        }
        
        // TIER 5: Smart category detection
        $categoryKeywords = [
            'sneakers' => ['sneaker', 'sneakers', 'casual', 'everyday'],
            'running' => ['running', 'runner', 'jog', 'marathon', 'endurance'],
            'athletics' => ['athletic', 'athletics', 'sport', 'sports', 'gym', 'training'],
            'womenathletics' => ['women', 'female', 'lady'],
            'womenrunning' => ['women', 'female', 'lady'],
            'womensneakers' => ['women', 'female', 'lady'],
            'kidsathletics' => ['kids', 'kid', 'child', 'children', 'youth'],
            'kidsneakers' => ['kids', 'kid', 'child', 'children', 'youth'],
            'kidslipon' => ['kids', 'kid', 'child', 'children', 'youth', 'slip', 'slip-on']
        ];
        
        foreach ($categoryKeywords as $cat => $keywords) {
            foreach ($keywords as $keyword) {
                if (in_array($keyword, $searchWords)) {
                    $searchConditions[] = "LOWER(category) = :param_" . ++$paramCounter;
                    $params["param_$paramCounter"] = $cat;
                    break;
                }
            }
        }
        
        // TIER 6: Smart brand detection
        $brandKeywords = [
            'xrizz' => ['xrizz', 'x rizz', 'x-rizz'],
            'generic' => ['generic', 'brand', 'unbranded'],
            'nike' => ['nike', 'nikee'],
            'adidas' => ['adidas', 'adidaas'],
            'puma' => ['puma', 'pumaa']
        ];
        
        foreach ($brandKeywords as $br => $keywords) {
            foreach ($keywords as $keyword) {
                if (in_array($keyword, $searchWords)) {
                    $searchConditions[] = "LOWER(brand) LIKE :param_" . ++$paramCounter;
                    $params["param_$paramCounter"] = '%' . $br . '%';
                    break;
                }
            }
        }
        
        // TIER 7: Smart color detection
        $colorKeywords = [
            'Black' => ['black', 'blk', 'dark'],
            'White' => ['white', 'wht', 'light'],
            'Red' => ['red', 'rd'],
            'Blue' => ['blue', 'blu', 'navy'],
            'Green' => ['green', 'grn'],
            'Brown' => ['brown', 'brn', 'tan'],
            'Gray' => ['gray', 'grey', 'gry'],
            'Pink' => ['pink', 'pnk'],
            'Purple' => ['purple', 'prpl'],
            'Yellow' => ['yellow', 'ylw'],
            'Orange' => ['orange', 'org'],
            'Multi-Colour' => ['multi', 'multicolor', 'multicolour', 'rainbow']
        ];
        
        foreach ($colorKeywords as $col => $keywords) {
            foreach ($keywords as $keyword) {
                if (in_array($keyword, $searchWords)) {
                    $searchConditions[] = "color = :param_" . ++$paramCounter;
                    $params["param_$paramCounter"] = $col;
                    break;
                }
            }
        }
        
        // TIER 8: Smart size detection
        $sizeKeywords = [
            'high' => ['high', 'high top', 'hightop'],
            'low' => ['low', 'low top', 'lowtop'],
            'mid' => ['mid', 'mid top', 'midtop'],
            'wide' => ['wide', 'w'],
            'regular' => ['regular', 'reg', 'normal'],
            'extra' => ['extra', 'x']
        ];
        
        foreach ($sizeKeywords as $size => $keywords) {
            foreach ($keywords as $keyword) {
                if (in_array($keyword, $searchWords)) {
                    $searchConditions[] = "(LOWER(height) LIKE :param_" . ++$paramCounter . " OR LOWER(width) LIKE :param_" . $paramCounter . ")";
                    $params["param_$paramCounter"] = '%' . $size . '%';
                    break;
                }
            }
        }
    }
    
    // Add additional filters
    $additionalFilters = [];
    if (!empty($category)) {
        $additionalFilters[] = "LOWER(category) = :filter_category";
        $params['filter_category'] = strtolower($category);
    }
    if (!empty($brand)) {
        $additionalFilters[] = "LOWER(brand) = :filter_brand";
        $params['filter_brand'] = strtolower($brand);
    }
    if (!empty($color)) {
        $additionalFilters[] = "LOWER(color) = :filter_color";
        $params['filter_color'] = strtolower($color);
    }
    if (!empty($price_min) && is_numeric($price_min)) {
        $additionalFilters[] = "price >= :price_min";
        $params['price_min'] = $price_min;
    }
    if (!empty($price_max) && is_numeric($price_max)) {
        $additionalFilters[] = "price <= :price_max";
        $params['price_max'] = $price_max;
    }
    
    // Build enhanced relevance scoring
    $relevanceCase = $searchLength == 1 ? 
        "CASE 
            WHEN LOWER(SUBSTRING(title, 1, 1)) = '" . $searchTerm . "' THEN 1000
            WHEN LOWER(SUBSTRING(brand, 1, 1)) = '" . $searchTerm . "' THEN 900
            WHEN LOWER(SUBSTRING(color, 1, 1)) = '" . $searchTerm . "' THEN 800
            ELSE 100
        END" :
        "CASE 
            WHEN LOWER(title) = '" . $searchTerm . "' THEN 1000
            WHEN LOWER(brand) = '" . $searchTerm . "' THEN 950
            WHEN LOWER(category) = '" . $searchTerm . "' THEN 850
            WHEN LOWER(collection) = '" . $searchTerm . "' THEN 800
            WHEN LOWER(title) LIKE '" . $searchTerm . "%' THEN 700
            WHEN LOWER(brand) LIKE '" . $searchTerm . "%' THEN 650
            WHEN LOWER(category) LIKE '" . $searchTerm . "%' THEN 550
            WHEN LOWER(collection) LIKE '" . $searchTerm . "%' THEN 500
            WHEN LOWER(title) LIKE '%" . $searchTerm . "%' THEN 400
            WHEN LOWER(brand) LIKE '%" . $searchTerm . "%' THEN 350
            WHEN LOWER(category) LIKE '%" . $searchTerm . "%' THEN 250
            WHEN LOWER(collection) LIKE '%" . $searchTerm . "%' THEN 200
            ELSE 50
        END";
    
    // Build ORDER BY clause based on sort option
    $orderBy = "title ASC";
    switch ($sort) {
        case 'price_low':
            $orderBy = "price ASC, stock DESC, title ASC";
            break;
        case 'price_high':
            $orderBy = "price DESC, stock DESC, title ASC";
            break;
        case 'az':
            $orderBy = "title ASC, stock DESC, price ASC";
            break;
        case 'newness':
            $orderBy = "date_added DESC, stock DESC, price ASC";
            break;
        case 'relevance':
        default:
            $orderBy = "$relevanceCase DESC,
            CASE WHEN stock > 0 THEN 1 ELSE 0 END DESC,
            price ASC,
            title ASC";
            break;
    }
    
    // Build the main query with GROUP BY to eliminate duplicates
    $whereClause = "(" . implode(' OR ', $searchConditions) . ")";
    if (!empty($additionalFilters)) {
        $whereClause .= " AND " . implode(' AND ', $additionalFilters);
    }
    
    $sql = "SELECT 
                id, 
                title, 
                price, 
                image, 
                category, 
                brand, 
                color, 
                height,
                width,
                collection,
                description,
                stock,
                date_added,
                $relevanceCase as relevance_score
            FROM products 
            WHERE $whereClause
            GROUP BY id
            ORDER BY " . $orderBy;
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // If no results, try fuzzy search with individual words
    if (count($products) == 0 && count($searchWords) > 1) {
        $fuzzyConditions = [];
        $fuzzyParams = [];
        $fuzzyCounter = 0;
        
        foreach ($searchWords as $index => $word) {
            if (strlen($word) > 2) {
                $fuzzyConditions[] = "(
                    LOWER(title) LIKE :fuzzy_param_" . ++$fuzzyCounter . " OR
                    LOWER(brand) LIKE :fuzzy_param_" . $fuzzyCounter . " OR
                    LOWER(category) LIKE :fuzzy_param_" . $fuzzyCounter . "
                )";
                $fuzzyParams["fuzzy_param_$fuzzyCounter"] = '%' . $word . '%';
            }
        }
        
        if (!empty($fuzzyConditions)) {
            $fuzzyWhereClause = "(" . implode(' OR ', $fuzzyConditions) . ")";
            if (!empty($additionalFilters)) {
                $fuzzyWhereClause .= " AND " . implode(' AND ', $additionalFilters);
            }
            
            $fuzzySql = "SELECT 
                            id, 
                            title, 
                            price, 
                            image, 
                            category, 
                            brand, 
                            color, 
                            height,
                            width,
                            collection,
                            description,
                            stock,
                            date_added,
                            10 as relevance_score
                        FROM products 
                        WHERE $fuzzyWhereClause
                        GROUP BY id
                        ORDER BY 
                            CASE WHEN stock > 0 THEN 1 ELSE 0 END DESC,
                            price ASC,
                            title ASC";
            
            $fuzzyStmt = $pdo->prepare($fuzzySql);
            $fuzzyStmt->execute(array_merge($fuzzyParams, $params));
            $products = $fuzzyStmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    
    // Clean image paths and ensure unique products
    $uniqueProducts = [];
    $seenIds = [];
    foreach ($products as $product) {
        if (!in_array($product['id'], $seenIds)) {
            $product['image'] = trim($product['image']);
            $uniqueProducts[] = $product;
            $seenIds[] = $product['id'];
        }
    }
    $products = $uniqueProducts;
    
} catch (PDOException $e) {
    $products = [];
    error_log("Search error: " . $e->getMessage());
}

// Get current user
$currentUser = getCurrentUser();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results for "<?php echo htmlspecialchars($query); ?>" - ShoeARizz</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/navbar.css">
    <link rel="stylesheet" href="assets/css/men.css">
    <link rel="stylesheet" href="assets/css/search.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="men-container">
        <div class="container-fluid">
            <!-- Search Header -->
            <div class="page-header">
                <h1 class="page-title">
                    <i class="fas fa-search me-3"></i>Search Results
                </h1>
                <p class="page-subtitle">
                    <?php if (count($products) > 0): ?>
                        Found <strong><?php echo number_format(count($products)); ?></strong> result<?php echo count($products) !== 1 ? 's' : ''; ?> for 
                        <span class="search-query-highlight">"<?php echo htmlspecialchars($query); ?>"</span>
                        <?php if (isset($_GET['debug'])): ?>
                            <br><small class="text-muted">Debug: Colors found: <?php echo implode(', ', array_unique(array_filter(array_column($products, 'color')))); ?></small>
                        <?php endif; ?>
                    <?php else: ?>
                        No results found for <span class="search-query-highlight">"<?php echo htmlspecialchars($query); ?>"</span>
                    <?php endif; ?>
                </p>
            </div>
            
            <!-- Advanced Filters and Products Header -->
            <?php if (count($products) > 0): ?>
            <div class="row">
                <!-- Filters Sidebar -->
                <div class="col-lg-3 col-md-4" id="sidebarContainer">
                    <div class="filters-sidebar">
                        <div class="filters-header">
                            <h3 class="filters-title">Filters</h3>
                            <div class="filter-actions">
                                <button class="btn btn-outline-secondary btn-sm" onclick="clearFilters()">
                                    <i class="fas fa-times"></i> Clear
                                </button>
                            </div>
                        </div>
                        
                        <!-- Category Filter -->
                        <div class="filter-group">
                            <h4 class="filter-title">Category</h4>
                            <div class="filter-options">
                                <?php
                                // Get unique categories from search results only
                                $categories = array_unique(array_filter(array_column($products, 'category')));
                                if (!empty($categories)): 
                                    foreach ($categories as $cat): 
                                        if (!empty($cat)): ?>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                       id="cat_<?php echo htmlspecialchars($cat); ?>" 
                                                       value="<?php echo htmlspecialchars($cat); ?>"
                                                       <?php echo ($category === $cat) ? 'checked' : ''; ?>
                                                       onchange="applyFilters()">
                                                <label class="form-check-label" for="cat_<?php echo htmlspecialchars($cat); ?>">
                                                    <?php echo ucfirst(str_replace(['women', 'men', 'kids'], ['Women', 'Men', 'Kids'], htmlspecialchars($cat))); ?>
                                                </label>
                                            </div>
                                        <?php endif;
                                    endforeach;
                                else: ?>
                                    <div class="text-muted">No categories available</div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Brand Filter -->
                        <div class="filter-group">
                            <h4 class="filter-title">Brand</h4>
                            <div class="filter-options">
                                <?php
                                // Get unique brands from search results only
                                $brands = array_unique(array_filter(array_column($products, 'brand')));
                                if (!empty($brands)): 
                                    foreach ($brands as $br): 
                                        if (!empty($br)): ?>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                       id="brand_<?php echo htmlspecialchars($br); ?>" 
                                                       value="<?php echo htmlspecialchars($br); ?>"
                                                       <?php echo ($brand === $br) ? 'checked' : ''; ?>
                                                       onchange="applyFilters()">
                                                <label class="form-check-label" for="brand_<?php echo htmlspecialchars($br); ?>">
                                                    <?php echo ucfirst(htmlspecialchars($br)); ?>
                                                </label>
                                            </div>
                                        <?php endif;
                                    endforeach;
                                else: ?>
                                    <div class="text-muted">No brands available</div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Color Filter -->
                        <div class="filter-group">
                            <h4 class="filter-title">Color</h4>
                            <div class="filter-options">
                                <?php
                                // Get unique colors from search results only
                                $colors = array_unique(array_filter(array_column($products, 'color')));
                                if (!empty($colors)): 
                                    foreach ($colors as $col): 
                                        if (!empty($col)): ?>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                       id="color_<?php echo htmlspecialchars($col); ?>" 
                                                       value="<?php echo htmlspecialchars($col); ?>"
                                                       <?php echo ($color === $col) ? 'checked' : ''; ?>
                                                       onchange="applyFilters()">
                                                <label class="form-check-label" for="color_<?php echo htmlspecialchars($col); ?>">
                                                    <span class="color-indicator" style="background-color: <?php echo strtolower(htmlspecialchars($col)); ?>;"></span>
                                                    <?php echo ucfirst(htmlspecialchars($col)); ?>
                                                </label>
                                            </div>
                                        <?php endif;
                                    endforeach;
                                else: ?>
                                    <div class="text-muted">No colors available</div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Price Range Filter -->
                        <div class="filter-group">
                            <h4 class="filter-title">Price Range</h4>
                            <div class="price-range-container">
                                <?php
                                // Calculate dynamic price range from search results
                                $prices = array_column($products, 'price');
                                $minPrice = !empty($prices) ? min($prices) : 0;
                                $maxPrice = !empty($prices) ? max($prices) : 10000;
                                $currentMin = $price_min ?: $minPrice;
                                $currentMax = $price_max ?: $maxPrice;
                                ?>
                                <div class="price-display">
                                    <span>₱<span id="priceMinDisplay"><?php echo number_format($currentMin); ?></span></span>
                                    <span>₱<span id="priceMaxDisplay"><?php echo number_format($currentMax); ?></span></span>
                                </div>
                                <div class="dual-range-slider">
                                    <input type="range" min="<?php echo $minPrice; ?>" max="<?php echo $maxPrice; ?>" step="50" 
                                           value="<?php echo $currentMin; ?>" 
                                           id="priceMin" oninput="updatePriceRange()">
                                    <input type="range" min="<?php echo $minPrice; ?>" max="<?php echo $maxPrice; ?>" step="50" 
                                           value="<?php echo $currentMax; ?>" 
                                           id="priceMax" oninput="updatePriceRange()">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Products Container -->
                <div class="col-lg-9 col-md-8" id="productsContainer">
                    <div class="products-header">
                        <div class="results-info">
                            <span class="results-count"><?php echo count($products); ?> products found</span>
                        </div>
                        <div class="sort-controls">
                            <button class="btn btn-outline-secondary d-lg-none me-2" onclick="toggleFilters()">
                                <i class="fas fa-filter"></i> Filters
                            </button>
                            <select class="form-select" id="sortSelect" onchange="applySort()">
                                <option value="relevance" <?php echo ($sort === 'relevance') ? 'selected' : ''; ?>>Most Relevant</option>
                                <option value="price_low" <?php echo ($sort === 'price_low') ? 'selected' : ''; ?>>Price: Low to High</option>
                                <option value="price_high" <?php echo ($sort === 'price_high') ? 'selected' : ''; ?>>Price: High to Low</option>
                                <option value="az" <?php echo ($sort === 'az') ? 'selected' : ''; ?>>A-Z</option>
                                <option value="newness" <?php echo ($sort === 'newness') ? 'selected' : ''; ?>>Newest First</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Products Grid -->
                    <div class="products-grid">
                        <div class="row">
                            <?php foreach ($products as $product): ?>
                                <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
                                    <div class="product-card h-100">
                                        <div class="product-image">
                                            <a href="product.php?id=<?php echo $product['id']; ?>">
                                                <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                                                     alt="<?php echo htmlspecialchars($product['title']); ?>" 
                                                     class="img-fluid"
                                                     onerror="this.src='assets/img/placeholder.jpg'">
                                            </a>
                                            <div class="product-overlay">
                                                <button class="btn btn-outline-light btn-sm" onclick="addToFavorites(<?php echo $product['id']; ?>)">
                                                    <i class="fas fa-heart"></i>
                                                </button>
                                                <button class="btn btn-outline-light btn-sm" onclick="quickView(<?php echo $product['id']; ?>)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="product-info">
                                            <div class="product-brand"><?php echo htmlspecialchars($product['brand'] ?? 'Generic'); ?></div>
                                            <h3 class="product-title">
                                                <a href="product.php?id=<?php echo $product['id']; ?>" class="text-decoration-none">
                                                    <?php echo htmlspecialchars($product['title']); ?>
                                                </a>
                                            </h3>
                                            <div class="product-details">
                                                <span class="product-color"><?php echo htmlspecialchars($product['color'] ?? 'N/A'); ?></span>
                                                <span class="product-height"><?php echo ucfirst(htmlspecialchars($product['height'] ?? 'N/A')); ?></span>
                                            </div>
                                            <div class="product-details">
                                                <span class="product-width"><?php echo ucfirst(htmlspecialchars($product['width'] ?? 'N/A')); ?></span>
                                                <span class="product-collection"><?php echo htmlspecialchars($product['collection'] ?? 'N/A'); ?></span>
                                            </div>
                                            <div class="product-price">₱<?php echo number_format($product['price'], 2); ?></div>
                                            <div class="product-stock">
                                                <?php if ($product['stock'] > 0): ?>
                                                    <span class="text-success">
                                                        <i class="fas fa-check-circle me-1"></i>In Stock
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-danger">
                                                        <i class="fas fa-times-circle me-1"></i>Out of Stock
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            <?php if (isLoggedIn()): ?>
                                                <button class="btn btn-primary w-100 mt-2"
                                                        onclick="addToCart(<?php echo $product['id']; ?>, 1, null, this)"
                                                        <?php echo ($product['stock'] <= 0) ? 'disabled' : ''; ?>>
                                                    <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                                                </button>
                                            <?php else: ?>
                                                <a href="signup.php" class="btn btn-primary w-100 mt-2">
                                                    <i class="fas fa-user-plus me-2"></i>Sign Up to Add to Cart
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- No Results Message -->
            <?php if (empty($products)): ?>
                <div class="col-12">
                    <div class="no-products text-center py-5">
                        <i class="fas fa-search fa-3x mb-3 text-muted"></i>
                        <h3>No products found</h3>
                        <p class="text-muted">Try adjusting your search terms or filters.</p>
                        <div class="mt-4">
                            <a href="index.php" class="btn btn-primary">
                                <i class="fas fa-home me-2"></i>Back to Home
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="assets/js/navbar.js"></script>
    <!-- Search JS is already included in navbar.php -->
    <?php if (isLoggedIn()): ?>
    <script src="assets/js/global-cart.js"></script>
    <script src="assets/js/global-favorites.js"></script>
    <?php endif; ?>
    <script src="assets/js/men.js"></script>
    
    <script>
        // Sort functionality
        function applySort() {
            const sortSelect = document.getElementById('sortSelect');
            const currentUrl = new URL(window.location);
            currentUrl.searchParams.set('sort', sortSelect.value);
            window.location.href = currentUrl.toString();
        }
        
        // Filter functionality
        function applyFilters() {
            const currentUrl = new URL(window.location);
            
            // Get selected filters
            const selectedCategories = Array.from(document.querySelectorAll('input[id^="cat_"]:checked')).map(cb => cb.value);
            const selectedBrands = Array.from(document.querySelectorAll('input[id^="brand_"]:checked')).map(cb => cb.value);
            const selectedColors = Array.from(document.querySelectorAll('input[id^="color_"]:checked')).map(cb => cb.value);
            const priceMin = document.getElementById('priceMin').value;
            const priceMax = document.getElementById('priceMax').value;
            
            // Update URL parameters
            if (selectedCategories.length > 0) {
                currentUrl.searchParams.set('category', selectedCategories[0]); // Take first selected
            } else {
                currentUrl.searchParams.delete('category');
            }
            
            if (selectedBrands.length > 0) {
                currentUrl.searchParams.set('brand', selectedBrands[0]); // Take first selected
            } else {
                currentUrl.searchParams.delete('brand');
            }
            
            if (selectedColors.length > 0) {
                currentUrl.searchParams.set('color', selectedColors[0]); // Take first selected
            } else {
                currentUrl.searchParams.delete('color');
            }
            
            // Get the min/max values from the slider attributes
            const priceMinSlider = document.getElementById('priceMin');
            const priceMaxSlider = document.getElementById('priceMax');
            const minRange = priceMinSlider.getAttribute('min');
            const maxRange = priceMaxSlider.getAttribute('max');
            
            if (priceMin && priceMin !== minRange) {
                currentUrl.searchParams.set('price_min', priceMin);
            } else {
                currentUrl.searchParams.delete('price_min');
            }
            
            if (priceMax && priceMax !== maxRange) {
                currentUrl.searchParams.set('price_max', priceMax);
            } else {
                currentUrl.searchParams.delete('price_max');
            }
            
            window.location.href = currentUrl.toString();
        }
        
        // Clear all filters
        function clearFilters() {
            const currentUrl = new URL(window.location);
            currentUrl.searchParams.delete('category');
            currentUrl.searchParams.delete('brand');
            currentUrl.searchParams.delete('color');
            currentUrl.searchParams.delete('price_min');
            currentUrl.searchParams.delete('price_max');
            window.location.href = currentUrl.toString();
        }
        
        // Update price range display
        function updatePriceRange() {
            const priceMin = document.getElementById('priceMin').value;
            const priceMax = document.getElementById('priceMax').value;
            
            // Format numbers with commas
            document.getElementById('priceMinDisplay').textContent = parseInt(priceMin).toLocaleString();
            document.getElementById('priceMaxDisplay').textContent = parseInt(priceMax).toLocaleString();
            
            // Apply filters after a short delay
            clearTimeout(window.priceFilterTimeout);
            window.priceFilterTimeout = setTimeout(applyFilters, 500);
        }
        
        // Toggle filters on mobile
        function toggleFilters() {
            const sidebar = document.getElementById('sidebarContainer');
            const productsContainer = document.getElementById('productsContainer');
            
            if (sidebar.style.display === 'none' || sidebar.style.display === '') {
                sidebar.style.display = 'block';
                productsContainer.classList.remove('col-lg-9', 'col-md-8');
                productsContainer.classList.add('col-12');
            } else {
                sidebar.style.display = 'none';
                productsContainer.classList.remove('col-12');
                productsContainer.classList.add('col-lg-9', 'col-md-8');
            }
        }
        
        // Cart functionality is handled by global-cart.js
        
        // Add to favorites functionality is handled by global-favorites.js
        
        // Quick view functionality
        function quickView(productId) {
            // Simple quick view functionality - you can enhance this
            window.location.href = 'product.php?id=' + productId;
        }
        
        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            // Add search analytics tracking
            if (typeof gtag !== 'undefined') {
                gtag('event', 'search', {
                    'search_term': '<?php echo addslashes($query); ?>',
                    'result_count': <?php echo count($products); ?>
                });
            }
            
            // Hide filters on mobile by default
            if (window.innerWidth < 992) {
                const sidebar = document.getElementById('sidebarContainer');
                if (sidebar) {
                    sidebar.style.display = 'none';
                }
            }
            
            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 992) {
                    const sidebar = document.getElementById('sidebarContainer');
                    const productsContainer = document.getElementById('productsContainer');
                    if (sidebar && productsContainer) {
                        sidebar.style.display = 'block';
                        productsContainer.classList.remove('col-12');
                        productsContainer.classList.add('col-lg-9', 'col-md-8');
                    }
                }
            });
        });
    </script>
</body>
</html>
