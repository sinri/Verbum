<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2018/3/23
 * Time: 10:10
 */

namespace sinri\Verbum\workshop;


class PaperConfig
{
    protected $width;
    protected $height;
    protected $marginRight;
    protected $marginLeft;
    protected $marginTop;
    protected $marginBottom;
    protected $titleFontSize;
    protected $titleFont;
    protected $inscriptionFontSize;
    protected $inscriptionFont;
    protected $contentFontSize;
    protected $contentFont;
    //private $charDistance;
    //private $lineDistance;
    protected $charDistanceRate;
    protected $lineDistanceRate;

    public function __construct()
    {
        $this->width=750;
        $this->height=1334;

        $this->marginRight=100;
        $this->marginLeft=100;
        $this->marginTop=50;
        $this->marginBottom=50;

        $this->titleFont=FontKit::instance()->getDefaultFont();
        $this->titleFontSize=50;
        $this->inscriptionFont=FontKit::instance()->getDefaultFont();
        $this->inscriptionFontSize=50;
        $this->contentFont=FontKit::instance()->getDefaultFont();
        $this->contentFontSize=50;

        //$this->charDistance=10;
        //$this->lineDistance=20;

        $this->charDistanceRate=0.2;
        $this->lineDistanceRate=0.5;
    }

    /**
     * @return float
     */
    public function getCharDistanceRate()
    {
        return $this->charDistanceRate;
    }

    /**
     * @param float $charDistanceRate
     */
    public function setCharDistanceRate($charDistanceRate)
    {
        $this->charDistanceRate = $charDistanceRate;
    }

    /**
     * @return float
     */
    public function getLineDistanceRate()
    {
        return $this->lineDistanceRate;
    }

    /**
     * @param float $lineDistanceRate
     */
    public function setLineDistanceRate($lineDistanceRate)
    {
        $this->lineDistanceRate = $lineDistanceRate;
    }

    public function __toString()
    {
        return json_encode([
            "width"=>$this->width,
            "height"=>$this->height,
            "marginRight"=>$this->marginRight,
            "marginLeft"=>$this->marginLeft,
            "marginTop"=>$this->marginTop,
            "marginBottom"=>$this->marginBottom,
            "titleFont"=>$this->titleFont,
            "titleFontSize"=>$this->titleFontSize,
            "inscriptionFont"=>$this->inscriptionFont,
            "inscriptionFontSize"=>$this->inscriptionFontSize,
            "contentFont"=>$this->contentFont,
            "contentFontSize"=>$this->contentFontSize,
            //"charDistance"=>$this->charDistance,
            //"lineDistance"=>$this->lineDistance,
            "charDistanceRate"=>$this->charDistanceRate,
            "lineDistanceRate"=>$this->lineDistanceRate,
        ],JSON_PRETTY_PRINT);
    }

    /**
     * @return bool
     */
    public function validate(){
        if($this->marginBottom+$this->marginTop>$this->height*0.6)return false;
        if($this->marginLeft+$this->marginRight>$this->width*0.6)return false;
        return true;
    }

    /**
     * 只能跑一次的转义。。。
     */
    public function translateUserFontSizeToGDSize(){
        $this->titleFontSize=FontKit::autoGetFontRealSize($this->titleFont,$this->titleFontSize);
        $this->contentFontSize=FontKit::autoGetFontRealSize($this->contentFont,$this->contentFontSize);
        $this->inscriptionFontSize=FontKit::autoGetFontRealSize($this->inscriptionFont,$this->inscriptionFontSize);
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param int $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param int $height
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * @return int
     */
    public function getMarginRight()
    {
        return $this->marginRight;
    }

    /**
     * @param int $marginRight
     */
    public function setMarginRight($marginRight)
    {
        $this->marginRight = $marginRight;
    }

    /**
     * @return int
     */
    public function getMarginLeft()
    {
        return $this->marginLeft;
    }

    /**
     * @param int $marginLeft
     */
    public function setMarginLeft($marginLeft)
    {
        $this->marginLeft = $marginLeft;
    }

    /**
     * @return int
     */
    public function getMarginTop()
    {
        return $this->marginTop;
    }

    /**
     * @param int $marginTop
     */
    public function setMarginTop($marginTop)
    {
        $this->marginTop = $marginTop;
    }

    /**
     * @return int
     */
    public function getMarginBottom()
    {
        return $this->marginBottom;
    }

    /**
     * @param int $marginBottom
     */
    public function setMarginBottom($marginBottom)
    {
        $this->marginBottom = $marginBottom;
    }

    /**
     * @return int
     */
    public function getTitleFontSize()
    {
        return $this->titleFontSize;
    }

    /**
     * @param int $titleFontSize
     */
    public function setTitleFontSize($titleFontSize)
    {
        $this->titleFontSize = $titleFontSize;
    }

    /**
     * @return string
     */
    public function getTitleFont()
    {
        return $this->titleFont;
    }

    /**
     * @param string $titleFont
     */
    public function setTitleFont($titleFont)
    {
        $this->titleFont = $titleFont;
    }

    /**
     * @return int
     */
    public function getInscriptionFontSize()
    {
        return $this->inscriptionFontSize;
    }

    /**
     * @param int $inscriptionFontSize
     */
    public function setInscriptionFontSize($inscriptionFontSize)
    {
        $this->inscriptionFontSize = $inscriptionFontSize;
    }

    /**
     * @return string
     */
    public function getInscriptionFont()
    {
        return $this->inscriptionFont;
    }

    /**
     * @param string $inscriptionFont
     */
    public function setInscriptionFont($inscriptionFont)
    {
        $this->inscriptionFont = $inscriptionFont;
    }

    /**
     * @return int
     */
    public function getContentFontSize()
    {
        return $this->contentFontSize;
    }

    /**
     * @param int $contentFontSize
     */
    public function setContentFontSize($contentFontSize)
    {
        $this->contentFontSize = $contentFontSize;
    }

    /**
     * @return string
     */
    public function getContentFont()
    {
        return $this->contentFont;
    }

    /**
     * @param string $contentFont
     */
    public function setContentFont($contentFont)
    {
        $this->contentFont = $contentFont;
    }

    /**
     * @param float $referenceCharSize
     * @return float
     */
    public function getCharDistance($referenceCharSize)
    {
        return $this->charDistanceRate*$referenceCharSize;
    }

    /**
     * @param float $referenceLineSize
     * @return float
     */
    public function getLineDistance($referenceLineSize)
    {
        return $this->lineDistanceRate*$referenceLineSize;
    }

}