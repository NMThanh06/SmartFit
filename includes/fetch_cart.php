<?php
session_start();
require_once 'config.php';
$userId = $_SESSION['user_id'] ?? 0;

$sql = "SELECT c.*, o.name, o.price, o.image 
        FROM shopping_cart c 
        JOIN outfits o ON c.outfit_id = o.id 
        WHERE c.user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$items = [];
$total_qty = 0;
while ($row = mysqli_fetch_assoc($result)) {
    $items[] = $row;
    $total_qty += $row['quantity'];
}

echo json_encode(['items' => $items, 'total_quantity' => $total_qty]);