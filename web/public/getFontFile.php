<?php

use sinri\Verbum\workshop\FontKit;

require_once __DIR__ . '/../autoload.php';
if (!empty($_REQUEST['font_name'])) {
    $fontName = $_REQUEST['font_name'];
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