<?php
// Turn on error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set JSON header
header('Content-Type: application/json');

try {
    // Include database connection
    require_once 'db.php';
    
    // Get search query from URL and sanitize
    $query = isset($_GET['q']) ? trim($_GET['q']) : '';
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 8;
    
    // If no query, return empty results
    if (empty($query)) {
        echo json_encode([
            'success' => true,
            'query' => '',
            'results' => [],
            'count' => 0,
            'suggestions' => []
        ]);
        exit;
    }
    
    // Test database connection first
    if (!isset($pdo)) {
        throw new Exception('Database connection not available');
    }
    
    // SMART SEARCH - Search in multiple fields including color
    $searchTerm = '%' . $query . '%';
    $results = [];
    
    // Search in title
    $sql = "SELECT id, title, price, image, category, brand, color, stock FROM products WHERE title LIKE ?";
    $params = [$searchTerm];
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $titleResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $results = array_merge($results, $titleResults);
    
    // Search in brand
    $sql = "SELECT id, title, price, image, category, brand, color, stock FROM products WHERE brand LIKE ?";
    $params = [$searchTerm];
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $brandResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $results = array_merge($results, $brandResults);
    
    // Search in category
    $sql = "SELECT id, title, price, image, category, brand, color, stock FROM products WHERE category LIKE ?";
    $params = [$searchTerm];
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $categoryResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $results = array_merge($results, $categoryResults);
    
    // Search in color field (exact match for colors)
    $colorMap = [
        'red' => 'Red',
        'black' => 'Black',
        'white' => 'White',
        'blue' => 'Blue',
        'green' => 'Green',
        'brown' => 'Brown',
        'gray' => 'Gray',
        'grey' => 'Gray',
        'pink' => 'Pink',
        'purple' => 'Purple',
        'yellow' => 'Yellow',
        'orange' => 'Orange',
        'multi' => 'Multi-Colour',
        'multicolor' => 'Multi-Colour',
        'multicolour' => 'Multi-Colour'
    ];
    
    $lowerQuery = strtolower($query);
    if (isset($colorMap[$lowerQuery])) {
        $colorValue = $colorMap[$lowerQuery];
        $sql = "SELECT id, title, price, image, category, brand, color, stock FROM products WHERE color = ?";
        $params = [$colorValue];
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $colorResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $results = array_merge($results, $colorResults);
    }
    
    // Remove duplicates based on ID
    $uniqueResults = [];
    $seenIds = [];
    foreach ($results as $result) {
        if (!in_array($result['id'], $seenIds)) {
            $uniqueResults[] = $result;
            $seenIds[] = $result['id'];
        }
    }
    $results = $uniqueResults;
    
    // Limit results in PHP instead of SQL
    $results = array_slice($results, 0, $limit);
    
    // Simple suggestions
    $suggestions = [];
    if (count($results) > 0) {
        $categories = array_unique(array_column($results, 'category'));
        $brands = array_unique(array_column($results, 'brand'));
        
        foreach (array_slice($categories, 0, 2) as $cat) {
            $suggestions[] = ucfirst($cat) . ' shoes';
        }
        
        foreach (array_slice($brands, 0, 2) as $brand) {
            $suggestions[] = ucfirst($brand) . ' sneakers';
        }
    }
    
    echo json_encode([
        'success' => true,
        'query' => $query,
        'results' => $results,
        'count' => count($results),
        'suggestions' => array_slice($suggestions, 0, 4)
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage(),
        'results' => [],
        'count' => 0,
        'suggestions' => [],
        'debug' => [
            'error_type' => 'PDOException',
            'error_message' => $e->getMessage(),
            'error_code' => $e->getCode(),
            'query' => $query ?? 'unknown'
        ]
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Search service temporarily unavailable: ' . $e->getMessage(),
        'results' => [],
        'count' => 0,
        'suggestions' => [],
        'debug' => [
            'error_type' => 'Exception',
            'error_message' => $e->getMessage(),
            'query' => $query ?? 'unknown'
        ]
    ]);
}
?>