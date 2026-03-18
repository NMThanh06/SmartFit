<?php
require_once 'includes/config.php';
$res = mysqli_query($conn, "SHOW TABLES");
while ($row = mysqli_fetch_array($res)) {
    echo $row[0] . "\n";
}
echo "----\n";

// check columns in outfits
$res2 = mysqli_query($conn, "SHOW COLUMNS FROM outfits");
while ($row2 = mysqli_fetch_array($res2)) {
    echo $row2[0] . "\n";
}
?>
