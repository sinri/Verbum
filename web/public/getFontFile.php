<?php

use sinri\Verbum\workshop\FontKit;
use sinri\Verbum\workshop\IOKit;

require_once __DIR__ . '/../autoload.php';
$fontName = IOKit::read('font_name', false);
if (!empty($fontName)) {
    $fontPath = FontKit::instance()->getFontByName($fontName);

    $size = filesize($fontPath);
    header('Content-Length: ' . $size);

    $handle = fopen($fontPath, "rb");
    if (FALSE === $handle) {
        throw new RuntimeException('cannot open font file');
    }

    while (!feof($handle)) {
        echo fread($handle, 8192);
    }
    fclose($handle);
} else {
    throw new InvalidArgumentException("font_name is not found");
}