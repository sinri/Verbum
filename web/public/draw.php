<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2018/3/22
 * Time: 23:31
 */

use sinri\Verbum\workshop\Drawer;
use sinri\Verbum\workshop\FontKit;
use sinri\Verbum\workshop\IOKit;
use sinri\Verbum\workshop\PaperConfig;

require_once __DIR__.'/../autoload.php';

$outputAsBase64= IOKit::read('output_base64',false);
if($outputAsBase64){
    $outputAsBase64=true;
}

$title= IOKit::read("title");
$content= IOKit::read("content");
$inscription= IOKit::read("inscription");

$titleFont=IOKit::read('title_font');
$contentFont=IOKit::read('content_font');
$inscriptionFont=IOKit::read('inscription_font');

$titleFontSize=IOKit::read('title_font_size');
$contentFontSize=IOKit::read('content_font_size');
$inscriptionFontSize=IOKit::read('inscription_font_size');

$colorHexOfTitle= IOKit::read("title_color",'#000000');
$colorHexOfContent= IOKit::read("content_color",'#000000');
$colorHexOfInscription= IOKit::read("inscription_color",'#000000');

$paperWidth=IOKit::read('paper_width');
$paperHeight=IOKit::read('paper_height');

$paperBackgroundColor=IOKit::read('paper_background_color','#FFFFFF');

$lineDistanceRate=IOKit::read('line_distance_rate');
$charDistanceRate=IOKit::read('char_distance_rate');

$marginTop=IOKit::read('margin_top');
$marginBottom=IOKit::read('margin_bottom');
$marginLeft=IOKit::read('margin_left');
$marginRight=IOKit::read('margin_right');

// debug
if(false){
    echo '$outputAsBase64:'.json_encode($outputAsBase64).PHP_EOL;
    echo '$title:'.json_encode($title).PHP_EOL;
    echo '$content:'.json_encode($content).PHP_EOL;
    echo '$inscription:'.json_encode($inscription).PHP_EOL;
    echo '$colorHexOfTitle:'.$colorHexOfTitle.PHP_EOL;
    echo '$colorHexOfContent:'.$colorHexOfContent.PHP_EOL;
    echo '$colorHexOfInscription:'.$colorHexOfInscription.PHP_EOL;
    exit;
}

$paperConfig=new PaperConfig();
if($paperWidth!==null)$paperConfig->setWidth($paperWidth);
if($paperHeight!==null)$paperConfig->setHeight($paperHeight);
if($lineDistanceRate!==null)$paperConfig->setLineDistanceRate($lineDistanceRate);
if($charDistanceRate!==null)$paperConfig->setCharDistanceRate($charDistanceRate);
if($titleFont!==null)$paperConfig->setTitleFont(FontKit::instance()->getFontByName($titleFont));
if($contentFont!==null)$paperConfig->setContentFont(FontKit::instance()->getFontByName($contentFont));
if($inscriptionFont!==null)$paperConfig->setInscriptionFont(FontKit::instance()->getFontByName($inscriptionFont));
if($titleFontSize!==null)$paperConfig->setTitleFontSize($titleFontSize);
if($contentFontSize!==null)$paperConfig->setContentFontSize($contentFontSize);
if($inscriptionFontSize!==null)$paperConfig->setInscriptionFontSize($inscriptionFontSize);
if($marginTop!==null)$paperConfig->setMarginTop($marginTop);
if($marginBottom!==null)$paperConfig->setMarginBottom($marginBottom);
if($marginLeft!==null)$paperConfig->setMarginLeft($marginLeft);
if($marginRight!==null)$paperConfig->setMarginRight($marginRight);

if(!$paperConfig->validate()){
    echo "PAPER CONFIG INVALID!".PHP_EOL;
    exit;
}
$paperConfig->translateUserFontSizeToGDSize();

if(false){
    echo $paperConfig.PHP_EOL;
    exit;
}

$drawer= Drawer::createDrawerForEmptyPaper($paperConfig);
$drawer->setBackgroundColor($drawer->getColorKit()->hex($paperBackgroundColor));


if(IOKit::isValidString($title)){
    $drawer->writeTitle($title,$drawer->getColorKit()->hex($colorHexOfTitle));
}
if(IOKit::isValidString($content)){
    $drawer->writeContent($content,$drawer->getColorKit()->hex($colorHexOfContent));
}
if(IOKit::isValidString($inscription)){
    $drawer->writeInscription($inscription,$drawer->getColorKit()->hex($colorHexOfInscription));
}

//exit;

if($outputAsBase64){
    ob_start();
    $drawer->output(IMAGETYPE_PNG);
    $raw = ob_get_contents();
    ob_end_clean();
    echo base64_encode($raw);
}else {
    $drawer->output(IMAGETYPE_PNG);
}