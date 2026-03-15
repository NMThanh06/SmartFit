<?php
require 'config.php';
$r = mysqli_query($conn, 'SELECT * FROM outfits WHERE id=10');
echo "--- OUTFIT 10 ---\n";
print_r(mysqli_fetch_assoc($r));
$r = mysqli_query($conn, 'SELECT * FROM outfit_sizes WHERE outfit_id=10');
echo "--- SIZES 10 ---\n";
while($row = mysqli_fetch_assoc($r)) print_r($row);
?>
