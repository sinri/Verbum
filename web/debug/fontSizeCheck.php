<?php

use sinri\Verbum\workshop\FontKit;

require_once __DIR__.'/../autoload.php';

$userWantedSize=50;

$font_name_list=FontKit::instance()->getFontNameList();
foreach ($font_name_list as $font_name){
    $font=FontKit::instance()->getFontByName($font_name);
    echo "FOR FONT ".$font_name.PHP_EOL;

    $real_size = FontKit::autoGetFontRealSize($font,$userWantedSize);

//    echo "User Wanted Size: ".$userWantedSize.PHP_EOL;
//    echo "Computed GD Size: ".$real_size.PHP_EOL;
//    echo "Char Block with size {$userWantedSize}: ".FontKit::computeWidthForGuessSize($char,FontKit::instance()->getDefaultFont(),$userWantedSize).PHP_EOL;
//    echo "Char Block with size {$real_size}: ".FontKit::computeWidthForGuessSize($char,FontKit::instance()->getDefaultFont(),$real_size).PHP_EOL;

    echo "GD-Size (System View) | Box-Size (User View)".PHP_EOL;
    echo "{$real_size} | {$userWantedSize}".PHP_EOL;
    echo "{$userWantedSize} | ".FontKit::computeWidthForGuessSize(FontKit::SAMPLE_KANJI,$font,$userWantedSize). " by computeWidthForGuessSize".PHP_EOL;
    echo "{$real_size} | ".FontKit::computeWidthForGuessSize(FontKit::SAMPLE_KANJI,$font,$real_size)." by computeWidthForGuessSize".PHP_EOL;

    echo PHP_EOL;
}
