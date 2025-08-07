<?php
/**
 * BeyChoc API - Get Products
 * Returns all products or filtered products based on search/category
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        throw new Exception("Database connection failed");
    }

    // Get query parameters
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $category = isset($_GET['category']) ? trim($_GET['category']) : '';
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : null;

    // Build SQL query
    $sql = "SELECT * FROM products WHERE 1=1";
    $params = [];

    // Add search filter
    if (!empty($search)) {
        $sql .= " AND (name LIKE :search OR code LIKE :search)";
        $params[':search'] = '%' . $search . '%';
    }

    // Add category filter
    if (!empty($category)) {
        $sql .= " AND category = :category";
        $params[':category'] = $category;
    }

    // Add ordering
    $sql .= " ORDER BY created_at DESC";

    // Add limit if specified
    if ($limit && $limit > 0) {
        $sql .= " LIMIT :limit";
        $params[':limit'] = $limit;
    }

    $stmt = $db->prepare($sql);
    
    // Bind parameters
    foreach ($params as $key => $value) {
        if ($key === ':limit') {
            $stmt->bindValue($key, $value, PDO::PARAM_INT);
        } else {
            $stmt->bindValue($key, $value, PDO::PARAM_STR);
        }
    }
    
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format response
    $response = [
        'success' => true,
        'count' => count($products),
        'products' => $products
    ];

    echo json_encode($response, JSON_UNESCAPED_UNICODE);

} catch(Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Server error occurred',
        'message' => $e->getMessage()
    ]);
}
?>
