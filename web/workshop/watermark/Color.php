<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2018/4/4
 * Time: 16:18
 */

namespace sinri\Verbum\workshop\watermark;


class Color
{
    /**
     * @var int
     */
    public $r;
    /**
     * @var int
     */
    public $g;
    /**
     * @var int
     */
    public $b;
    /**
     * @var int
     */
    public $a;

    /**
     * Color constructor.
     * @param int $r
     * @param int $g
     * @param int $b
     * @param int $a
     */
    public function __construct($r = 0, $g = 0, $b = 0, $a = 255)
    {
        $this->r = $r;
        $this->g = $g;
        $this->b = $b;
        $this->a = $a;
    }

    /**
     * @param int $r
     * @param int $g
     * @param int $b
     * @param int $a
     * @return Color
     */
    public static function byRGBA($r = 0, $g = 0, $b = 0, $a = 255)
    {
        return new Color($r, $g, $b, $a);
    }

    /**
     * @param int $r
     * @param int $g
     * @param int $b
     * @return Color
     */
    public static function byRGB($r = 0, $g = 0, $b = 0)
    {
        return new Color($r, $g, $b);
    }

    /**
     * @param Color $color
     * @return Color
     */
    public static function copyColor($color)
    {
        return new Color($color->r, $color->g, $color->b, $color->a);
    }

    /**
     * @param resource $image
     * @return int
     */
    public function getColorIndexForImage($image)
    {
        $imgColorId = imagecolorexactalpha($image, $this->r, $this->g, $this->b, $this->a);
        if ($imgColorId === -1) {
            $imgColorId = imagecolorallocatealpha($image, $this->r, $this->g, $this->b, $this->a);
        }
        return $imgColorId;
    }

    public function __toString()
    {
        return "Color({$this->r},{$this->g},{$this->b},{$this->a})";
    }
}