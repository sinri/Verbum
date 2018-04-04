<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2018/4/4
 * Time: 16:16
 */

namespace sinri\Verbum\workshop\watermark;


class Point
{
    /**
     * @var int
     */
    public $x;
    /**
     * @var int
     */
    public $y;

    /**
     * Point constructor.
     * @param int $x
     * @param int $y
     */
    public function __construct($x = 0, $y = 0)
    {
        $this->x = $x;
        $this->y = $y;
    }

    /**
     * @param int $x
     * @param int $y
     * @return Point
     */
    public static function byXY($x, $y)
    {
        return new Point($x, $y);
    }

    /**
     * @param Point $point
     * @return Point
     */
    public static function copyPoint($point)
    {
        return new Point($point->x, $point->y);
    }

    public function __toString()
    {
        return "Point({$this->x},{$this->y})";
    }
}