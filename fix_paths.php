<?php
require_once 'includes/config.php';

echo "Bắt đầu cập nhật Database...\n";

// 1. Cập nhật bảng outfit_colors
$sql1 = "UPDATE outfit_colors SET image = REPLACE(image, '/', '/') WHERE image LIKE '%/%'";
if (mysqli_query($conn, $sql1)) {
    echo "Đã cập nhật " . mysqli_affected_rows($conn) . " dòng trong outfit_colors\n";
} else {
    echo "Lỗi outfit_colors: " . mysqli_error($conn) . "\n";
}

// 2. Cập nhật các tệp tin trong mã nguồn
echo "\nBắt đầu quét và thay thế trong files...\n";

function processDir($dir) {
    $files = glob($dir . '/*');
    foreach ($files as $file) {
        if (is_dir($file)) {
            processDir($file);
        } else {
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            if (in_array($ext, ['php', 'js', 'html', 'json'])) {
                $content = file_get_contents($file);
                if (strpos($content, '/') !== false) {
                    $newContent = str_replace('/', '/', $content);
                    file_put_contents($file, $newContent);
                    echo "Đã sửa file: $file\n";
                }
            }
        }
    }
}

processDir(__DIR__);

echo "Hoàn tất!\n";
?>
