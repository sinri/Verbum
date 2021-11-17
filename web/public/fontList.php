<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2018/3/23
 * Time: 15:13
 */

use sinri\Verbum\workshop\FontKit;
use sinri\Verbum\workshop\IOKit;

require_once __DIR__ . '/../autoload.php';
$fontKit = FontKit::instance();

IOKit::sayOK([
    'list' => $fontKit->getFontNameList(),
    'real_name_dict' => $fontKit->getFontRealNameDict(),
]);