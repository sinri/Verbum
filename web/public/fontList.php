<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2018/3/23
 * Time: 15:13
 */

require_once __DIR__.'/../autoload.php';

$fonts=\sinri\Verbum\workshop\FontKit::instance()->getFontNameList();

\sinri\Verbum\workshop\IOKit::sayOK(['list'=>$fonts]);