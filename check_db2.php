<?php
require_once 'includes/config.php';
$res2 = mysqli_query($conn, "SHOW COLUMNS FROM reviews");
while ($row2 = mysqli_fetch_array($res2)) {
    echo $row2[0] . "\n";
}
?>
