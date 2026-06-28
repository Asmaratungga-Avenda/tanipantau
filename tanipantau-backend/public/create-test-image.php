<?php
$destDir = __DIR__ . '/storage/kunjungan/2026/06';
if (!is_dir($destDir)) {
    mkdir($destDir, 0755, true);
}
$img = imagecreatetruecolor(400, 300);
$color = imagecolorallocate($img, 0, 128, 64);
imagefill($img, 0, 0, $color);
$textColor = imagecolorallocate($img, 255, 255, 255);
imagestring($img, 5, 100, 120, 'Test Foto Kunjungan', $textColor);
imagejpeg($img, $destDir . '/d44d775b-8389-44fa-9fce-a20d4cb2f522.jpg');
imagedestroy($img);
echo "Test image created successfully at " . $destDir . '/d44d775b-8389-44fa-9fce-a20d4cb2f522.jpg';
?>