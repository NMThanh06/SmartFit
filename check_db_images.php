<?php
require 'includes/config.php';
$res = mysqli_query($conn, "SELECT id, color_name, image FROM outfit_colors");
while($r = mysqli_fetch_assoc($res)) {
    echo $r['id'] . ': ' . $r['image'] . "\n";
}
?>
