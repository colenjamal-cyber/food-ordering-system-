<?php
header('Content-Type: application/json');
include 'config.php';

// Get search and filter parameters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category_id = isset($_GET['category']) ? $_GET['category'] : '';

// Build the SQL query
$sql = "SELECT f.*, c.category_name 
        FROM food_items f
        LEFT JOIN categories c ON f.category_id = c.category_id
        WHERE 1=1";

// Add search condition if provided
if (!empty($search)) {
    $search_term = "%" . $conn->real_escape_string($search) . "%";
    $sql .= " AND (f.food_name LIKE '$search_term' OR f.description LIKE '$search_term')";
}

// Add category filter if provided
if (!empty($category_id) && is_numeric($category_id)) {
    $sql .= " AND f.category_id = $category_id";
}

// Add order by
$sql .= " ORDER BY f.food_name";

// Execute query
$result = $conn->query($sql);

$food_items = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $food_items[] = $row;
    }
}

// Return JSON response
echo json_encode($food_items);
?>