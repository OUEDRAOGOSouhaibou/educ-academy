<?php

$sourcePath = __DIR__ . '/../public/images/logo.jpeg';
$outputDir = __DIR__ . '/../public/images/icons';

if (!is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
}

$source = imagecreatefromjpeg($sourcePath);
if (!$source) {
    die("Impossible de charger le logo source.\n");
}

$srcWidth = imagesx($source);
$srcHeight = imagesy($source);

function generateIcon($source, $srcWidth, $srcHeight, $size, $outputPath, $paddingPercent = 0.05, $bgColor = [255, 255, 255]) {
    $target = imagecreatetruecolor($size, $size);
    imagealphablending($target, false);
    imagesavealpha($target, true);

    $bg = imagecolorallocate($target, $bgColor[0], $bgColor[1], $bgColor[2]);
    imagefilledrectangle($target, 0, 0, $size, $size, $bg);

    $innerSize = (int)($size * (1 - ($paddingPercent * 2)));
    $offset = (int)($size * $paddingPercent);

    // Centrage avec respect du ratio
    $ratio = min($innerSize / $srcWidth, $innerSize / $srcHeight);
    $newWidth = (int)($srcWidth * $ratio);
    $newHeight = (int)($srcHeight * $ratio);
    $destX = $offset + (int)(($innerSize - $newWidth) / 2);
    $destY = $offset + (int)(($innerSize - $newHeight) / 2);

    imagecopyresampled($target, $source, $destX, $destY, 0, 0, $newWidth, $newHeight, $srcWidth, $srcHeight);
    imagepng($target, $outputPath, 9);
    imagedestroy($target);
    echo "Généré : " . basename($outputPath) . " ({$size}x{$size})\n";
}

// 1. Icônes standard (avec léger padding)
generateIcon($source, $srcWidth, $srcHeight, 192, $outputDir . '/icon-192x192.png', 0.05);
generateIcon($source, $srcWidth, $srcHeight, 512, $outputDir . '/icon-512x512.png', 0.05);

// 2. Icônes maskable pour Android (safe zone 20% de marge, fond blanc ou bleu pétrole)
generateIcon($source, $srcWidth, $srcHeight, 192, $outputDir . '/icon-maskable-192x192.png', 0.15, [255, 255, 255]);
generateIcon($source, $srcWidth, $srcHeight, 512, $outputDir . '/icon-maskable-512x512.png', 0.15, [255, 255, 255]);

// 3. Apple Touch Icon (180x180)
generateIcon($source, $srcWidth, $srcHeight, 180, $outputDir . '/apple-touch-icon.png', 0.08);

// 4. Favicon PNG
generateIcon($source, $srcWidth, $srcHeight, 64, __DIR__ . '/../public/favicon.png', 0.02);

imagedestroy($source);
echo "Toutes les icônes PWA ont été créées avec succès !\n";
