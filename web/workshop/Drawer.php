<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2018/3/22
 * Time: 23:32
 */

namespace sinri\Verbum\workshop;


class Drawer
{
    /**
     * @var resource
     */
    protected $img;

    /**
     * @var ColorKit
     */
    protected $colorKit;


    /**
     * @var PaperConfig
     */
    protected $paperConfig;

    /**
     * Drawer constructor.
     * @param $img
     */
    protected function __construct($img)
    {
        $this->img=$img;
        $this->colorKit=new ColorKit($img);
    }

    /**
     * @param PaperConfig $paperConfig
     * @return Drawer
     */
    public static function createDrawerForEmptyPaper($paperConfig){
        $img=imagecreatetruecolor($paperConfig->getWidth(), $paperConfig->getHeight());
        $drawer = new self($img);
        $drawer->paperConfig=$paperConfig;
        return $drawer;
    }

    /**
     * @return ColorKit
     */
    public function getColorKit()
    {
        return $this->colorKit;
    }

    /**
     * @param int $color
     */
    public function setBackgroundColor($color){
        imagefill($this->img, 0, 0, $color);
    }

    /**
     * 设置保存PNG时保留透明通道信息
     * @param bool $saveFlag
     * @return bool
     */
    public function keepAlphaForPng($saveFlag)
    {
        return imagesavealpha($this->img, $saveFlag);
    }

    const ROTATE_CHARS = 'ー()（）“”[]{}【】‘’？?！!\~～@＠#＃%﹪&＆-－=＝<﹤|︳……^`-∕¦‖︴－／±㏒㏑∑∏√±∫∮∧∨＝≈≡≠＜＞≤≥≦≧≮≯º¹²³½¾¼％‰';

    protected function charNeedRotate($char)
    {
        static $chars = null;
        if ($chars === null) {
            for ($i = 0; $i < mb_strlen(self::ROTATE_CHARS); $i++) {
                $chars[] = mb_substr(self::ROTATE_CHARS, $i, 1);
            }
        }
        return (
            in_array($char, $chars) || preg_match('/^[A-Za-z0-9\-=\\/]$/', $char)
        );
    }

    public function writeTitle($string,$color){
        FontKit::autoComputeEnoughBlockWithGDSize($this->paperConfig->getTitleFontSize(),$this->paperConfig->getTitleFont(),$charWidth,$charHeight);
        $baseLeftX=round($this->paperConfig->getWidth()-$this->paperConfig->getMarginRight()+($this->paperConfig->getMarginRight()-$charWidth)/2);
        $baseBottomY=$this->paperConfig->getMarginTop()+$charHeight;
        $maxCharCount=floor((($this->paperConfig->getHeight()-$this->paperConfig->getMarginTop()-$this->paperConfig->getMarginBottom()) + $this->paperConfig->getCharDistance($charHeight))/($charHeight+$this->paperConfig->getCharDistance($charHeight)));

        $i=0;
        while($i<mb_strlen($string)){
            if($i>$maxCharCount)break;
            $char=mb_substr($string,$i,1);

            if ($this->charNeedRotate($char)) {
                $this->writeChar($this->paperConfig->getTitleFontSize(), 270, $baseLeftX + $charWidth / 4, $baseBottomY - $charHeight, $color, $this->paperConfig->getTitleFont(), $char);
            }else{
                $this->writeChar($this->paperConfig->getTitleFontSize(), 0, $baseLeftX, $baseBottomY, $color, $this->paperConfig->getTitleFont(), $char);
            }

//            echo __METHOD__.' Y='.$baseBottomY." char distance ".$this->paperConfig->getCharDistance($charHeight).PHP_EOL;

            $baseBottomY+=$charHeight+$this->paperConfig->getCharDistance($charHeight);

            $i++;
        }
    }

    public function writeInscription($string,$color){
        FontKit::autoComputeEnoughBlockWithGDSize($this->paperConfig->getInscriptionFontSize(),$this->paperConfig->getInscriptionFont(),$charWidth,$charHeight);
        $baseLeftX=round(($this->paperConfig->getMarginLeft()-$charWidth)/2);
        $baseBottomY=$this->paperConfig->getMarginTop()+$charHeight;
        $maxCharCount=floor((($this->paperConfig->getHeight()-$this->paperConfig->getMarginTop()-$this->paperConfig->getMarginBottom()) + $this->paperConfig->getCharDistance($charHeight))/($charHeight+$this->paperConfig->getCharDistance($charHeight)));

        if(mb_strlen($string)<$maxCharCount){
            $amari=($maxCharCount-mb_strlen($string));
            $baseBottomY+=$amari*$charHeight+($amari-1)*$this->paperConfig->getCharDistance($charHeight);
        }

//        echo $this->paperConfig->getMarginLeft().PHP_EOL;
//        echo $charWidth.PHP_EOL;
//        die($baseLeftX);

        $i=0;
        while($i<mb_strlen($string)){
            if($i>$maxCharCount)break;
            $char=mb_substr($string,$i,1);

            if ($this->charNeedRotate($char)) {
                $this->writeChar($this->paperConfig->getInscriptionFontSize(), 270, $baseLeftX + $charWidth / 4, $baseBottomY - $charHeight, $color, $this->paperConfig->getInscriptionFont(), $char);
            }else {
                $this->writeChar($this->paperConfig->getInscriptionFontSize(), 0, $baseLeftX, $baseBottomY, $color, $this->paperConfig->getInscriptionFont(), $char);
            }

            $baseBottomY+=$charHeight+$this->paperConfig->getCharDistance($charHeight);

            $i++;
        }
    }

    public function writeContent($string,$color){
        FontKit::autoComputeEnoughBlockWithGDSize($this->paperConfig->getContentFontSize(),$this->paperConfig->getContentFont(),$charWidth,$charHeight);
        $baseLeftX=round($this->paperConfig->getWidth()-$this->paperConfig->getMarginRight());
        $baseBottomY=$this->paperConfig->getMarginTop();

        $maxLineCount=floor(($this->paperConfig->getWidth()-$this->paperConfig->getMarginLeft()-$this->paperConfig->getMarginRight()+$this->paperConfig->getLineDistance($charWidth))/($charWidth+$this->paperConfig->getLineDistance($charWidth)));
        $maxCharCountInLine=floor((($this->paperConfig->getHeight()-$this->paperConfig->getMarginTop()-$this->paperConfig->getMarginBottom()) + $this->paperConfig->getCharDistance($charHeight))/($charHeight+$this->paperConfig->getCharDistance($charHeight)));

        $i=0;
        $lineIndex=0;
        $charIndex=0;
        while($i<mb_strlen($string)) {
            $char = mb_substr($string, $i++, 1);
            if($char===PHP_EOL){
                $lineIndex+=1;
                $charIndex=0;
                continue;
            }else{
                $charIndex+=1;
                if($charIndex>$maxCharCountInLine){
                    $lineIndex+=1;
                    $charIndex=1;
                    if($lineIndex>$maxLineCount){
                        break;
                    }
                }
            }
            $x=$baseLeftX-(($lineIndex-0)*$this->paperConfig->getLineDistance($charWidth)+($lineIndex+1)*$charWidth);
            $y=$baseBottomY+($charIndex-1)*$this->paperConfig->getCharDistance($charHeight)+$charIndex*$charHeight;

            //居中补正的原理就是这样子 但是每个字体的着墨重心并不一致
            /*
            $guessBoxWidth=FontKit::computeWidthForGuessSize($char,$this->paperConfig->getContentFont(),$this->paperConfig->getContentFontSize());
            if($guessBoxWidth<$charWidth*0.9){
                $x+=($charWidth-$guessBoxWidth)/2;
            }
            */
            if ($this->charNeedRotate($char)) {
                $this->writeChar($this->paperConfig->getContentFontSize(), 270, $x + $charWidth / 4, $y - $charHeight, $color, $this->paperConfig->getContentFont(), $char);
            }else {
                $this->writeChar($this->paperConfig->getContentFontSize(), 0, $x, $y, $color, $this->paperConfig->getContentFont(), $char);
            }

//            echo __METHOD__.' Y='.$y." [{$char}] char distance ".$this->paperConfig->getCharDistance($charHeight).PHP_EOL;


        }
    }

    protected function writeChar($size, $angle, $x, $y, $color, $font, $text){
        imagettftext($this->img,$size, $angle, $x, $y, $color, $font, $text);
        //debug: zuo xia ~ you shang
//        imagerectangle($this->img,$x,$y,$x+$size,$y-$size,$color);
    }

    /**
     * @param int $imageType IMAGETYPE_*
     */
    protected function setWebOutputHeader($imageType){
        header("Content-Type: ".image_type_to_mime_type($imageType));
    }

    /**
     * @param int $imageType IMAGETYPE_*
     * @param null|string $target null for web and string for path
     * @param array $optionalParameters
     */
    public function output($imageType,$target=null,$optionalParameters=[]){
        if($target===null && php_sapi_name()!=="cli"){
            $this->setWebOutputHeader($imageType);
        }
        $parameters=[$this->img,$target];
        $parameters=array_merge($parameters,$optionalParameters);
        switch ($imageType){
            case IMAGETYPE_GIF:
                call_user_func_array('imagegif',$parameters);
                break;
            case IMAGETYPE_JPEG:
                call_user_func_array('imagejpeg',$parameters);
                break;
            case IMAGETYPE_PNG:
                call_user_func_array('imagepng',$parameters);
                break;
            case IMAGETYPE_BMP:
                if (version_compare(PHP_VERSION, '7.2.0') >= 0) {
                    // (PHP 7 >= 7.2.0)
                    call_user_func_array('imagebmp',$parameters);
                }
                break;
            case IMAGETYPE_WBMP:
                call_user_func_array('imagewbmp',$parameters);
                break;
            case IMAGETYPE_XBM:
                call_user_func_array('imagexbm',$parameters);
                break;
            case IMAGETYPE_WEBP:
                call_user_func_array('imagewebp',$parameters);
                break;
        }

        // bool imagepng ( resource $image [, mixed $to [, int $quality [, int $filters ]]] )
        // bool imagejpeg ( resource $image [, mixed $to [, int $quality ]] )
        // bool imagebmp ( resource $image [, mixed $to = NULL [, bool $compressed = TRUE ]] )
        // bool imagewbmp ( resource $image [, mixed $to [, int $foreground ]] )
        // bool imagewebp ( resource $image [, mixed $to = NULL [, int $quality = 80 ]] )
        // bool imagegif ( resource $image [, mixed $to ] )
        // bool imagexbm ( resource $image , string $filename [, int $foreground ] )
    }

    public function __destruct()
    {
        imagedestroy($this->img);
    }
}