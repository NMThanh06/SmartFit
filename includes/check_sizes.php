<?php
require 'config.php';
$r = mysqli_query($conn, "SELECT id, name, type FROM outfits WHERE name LIKE '%Kính%'");
while($row = mysqli_fetch_assoc($r)) {
    echo "ID: " . $row['id'] . " | NAME: " . $row['name'] . " | TYPE: " . $row['type'] . "\n";
    $s = mysqli_query($conn, "SELECT size_name FROM outfit_sizes WHERE outfit_id=" . $row['id']);
    while($sr = mysqli_fetch_assoc($s)) {
        echo "  - SIZE: " . $sr['size_name'] . "\n";
    }
}
?>
