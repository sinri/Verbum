<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2018/3/23
 * Time: 09:52
 */

namespace sinri\Verbum\workshop;


class FontKit
{
    const SAMPLE_KANJI='草';

    /**
     * @var string[] name -> path
     */
    protected $fonts;

    /**
     * @var string
     */
    protected $defaultFont;

    /**
     * @return string
     */
    public function getDefaultFont()
    {
        return $this->defaultFont;
    }

    /**
     * FontKit constructor.
     */
    protected function __construct()
    {
        $this->initialize();
    }

    protected static $instance;

    /**
     * @return FontKit
     */
    public static function instance(){
        if(!self::$instance){
            self::$instance=new FontKit();
        }
        return self::$instance;
    }

    /**
     * Load
     */
    public function initialize(){
        $fonts=[];
        $defaultFontName='';
        if(file_exists(__DIR__.'/../config/fonts.php')){
            require __DIR__.'/../config/fonts.php';
        }
        foreach ($fonts as $key => $value){
            if(!file_exists($value)){
                unset($fonts[$key]);
            }
        }
        //$fonts['CNS11643中文標準交換碼全字庫']=__DIR__.'/../../assets/fonts/TW-Kai-98_1.ttf';

        $this->fonts=$fonts;
        $this->defaultFont=$fonts[$defaultFontName];//__DIR__.'/../../assets/fonts/TW-Kai-98_1.ttf';
    }

    /**
     * @param $name
     * @return string
     */
    public function getFontByName($name){
        if(!isset($this->fonts[$name]))return $this->defaultFont;
        return $this->fonts[$name];
    }

    /**
     * @return string[]
     */
    public function getFontNameList(){
        return array_keys($this->fonts);
    }

    public static function autoGetFontRealSize($font,$userWantSize){
        return self::getFontRealSize(self::SAMPLE_KANJI,$font,$userWantSize);
    }

    /**
     * @param string $char
     * @param string $font
     * @param float $userWantSize
     * @return float
     */
    protected static function getFontRealSize($char,$font,$userWantSize){
        $char=mb_substr($char,0,1);

        $w=self::computeWidthForGuessSize($char,$font,$userWantSize);
//        echo __METHOD__.'@'.__LINE__.' by computeWidthForGuessSize ユーザが指定したボックスのサイズ('.$userWantSize.')に対するGDのサイズ = '.$w.PHP_EOL;
        if(abs($w-$userWantSize)<1){
            return $w;
        }

        // find max
        $max=$userWantSize*2;
        while(true) {
            $aBigSize=self::computeWidthForGuessSize($char, $font, $max);
//            echo __METHOD__.'@'.__LINE__.' GDのサイズ ('.$max.') leads to box size = '.$aBigSize.' >>> '.$userWantSize.PHP_EOL;
            if($aBigSize>$userWantSize){
                break;
            }
            $max*=2;
        }

        if($w<$userWantSize){
            // 用户想要的尺寸 比 画出来的要大： 调大真实size
            return self::binarySeek($char,$font,$userWantSize,$max,$userWantSize);
        }else{
            return self::binarySeek($char,$font,1,$userWantSize,$userWantSize);
        }
    }

    protected static function binarySeek($char,$font,$minRealSize,$maxRealSize,$userWantSize){
        $middleGDSize=($minRealSize+$maxRealSize)/2;

        if(($maxRealSize-$minRealSize)<1){
//            echo date('H:i:s').'|'.__METHOD__." max<min, result = $middleGDSize".PHP_EOL;
            return $middleGDSize;
        }

        $middleW=self::computeWidthForGuessSize($char,$font,$middleGDSize);

//        $minW=self::computeWidthForGuessSize($char,$font,($minRealSize));
//        $maxW=self::computeWidthForGuessSize($char,$font,($maxRealSize));
//        echo date('H:i:s').'|'.__METHOD__." (".$minRealSize.",".$maxRealSize.") real ({$minW},{$maxW}) middle {$middleW} against requirement ".$userWantSize.PHP_EOL;

//        if(($maxRealSize-$minRealSize)<1){
//            return ($maxRealSize-$minRealSize)/2;
//        }

        if(abs($userWantSize-$middleW)<1){
//            echo date('H:i:s').'|'.__METHOD__." abs<1, result = $middleGDSize".PHP_EOL;
            return $middleGDSize;
        }

        if($middleW<$userWantSize){
            // 用户想要的尺寸 比 画出来的要大： 调大真实size
            return self::binarySeek($char,$font,$middleGDSize,$maxRealSize,$userWantSize);
        }else{
            return self::binarySeek($char,$font,$minRealSize,$middleGDSize,$userWantSize);
        }
    }

    public static function computeWidthForGuessSize($char, $font, $guessSize){
        self::computeEnoughBlockWithGDSize($char,$guessSize,$font,$w,$h);
//        echo __METHOD__." guess ".$guessSize." get width ".$w." (".$char.",".$guessSize.",".$font.")".PHP_EOL;
        return $w;
    }

    public static function autoComputeWidthForGuessSize($font, $guessSize){
        return self::computeWidthForGuessSize(self::SAMPLE_KANJI,$font,$guessSize);
    }

    /**
     * @param string $string
     * @param float $size
     * @param string $font
     * @param float $w
     * @param float $h
     */
    public static function computeEnoughBlockWithGDSize($string,$size,$font,&$w,&$h){
        $w=1;
        $h=1;
        for($i=0;$i<mb_strlen($string);$i++) {
            $char=mb_substr($string,$i,1);
            $box = imagettfbbox($size, 0, $font, $char);
            $charW = abs($box[2] - $box[0]);
            $charH = abs($box[1] - $box[7]);
            if($charH>$h)$h=$charH;
            if($charW>$w)$w=$charW;
        }
    }

    public static function autoComputeEnoughBlockWithGDSize($size,$font,&$w,&$h){
        self::computeEnoughBlockWithGDSize(self::SAMPLE_KANJI,$size,$font,$w,$h);
    }
}