<?php
ob_start();
session_start();
require_once 'config.php';
$userId = $_SESSION['user_id'] ?? 0;

$sql = "SELECT c.*, o.name, o.price, o.owner_id, u.fullname as vendor_name,
               COALESCE(col.image, (SELECT image FROM outfit_colors WHERE outfit_id = o.id LIMIT 1), 'assets/img/default-placeholder.jpg') as image
        FROM shopping_cart c 
        JOIN outfits o ON c.outfit_id = o.id 
        LEFT JOIN outfit_colors col ON (c.outfit_id = col.outfit_id AND c.color_name COLLATE utf8mb4_unicode_ci = col.color_name COLLATE utf8mb4_unicode_ci)
        LEFT JOIN users u ON o.owner_id = u.id
        WHERE c.user_id = ?";
header('Content-Type: application/json');
try {
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        throw new Exception(mysqli_error($conn));
    }
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (!$result) {
        throw new Exception(mysqli_error($conn));
    }

    $items = [];
    $total_qty = 0;
    while ($row = mysqli_fetch_assoc($result)) {
        $items[] = $row;
        $total_qty += $row['quantity'];
    }

    ob_clean();
    echo json_encode(['status' => 'success', 'items' => $items, 'total_quantity' => $total_qty]);
} catch (Exception $e) {
    ob_clean();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}