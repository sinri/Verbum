<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2018/4/4
 * Time: 15:03
 */

namespace sinri\Verbum\workshop\watermark;


class WatermarkLab
{
    protected $image;

    /**
     * WatermarkLab constructor.
     * @param resource $image
     */
    public function __construct($image)
    {
        $this->image = $image;
    }

    /**
     * @param Point $point
     * @return Color
     */
    public function getColorAt($point)
    {
        //echo __METHOD__."@".__LINE__." ".$point.PHP_EOL;
        $rgba = imagecolorat($this->image, $point->x, $point->y);
        if (imageistruecolor($this->image)) {
            $r = ($rgba >> 16) & 0xFF;
            $g = ($rgba >> 8) & 0xFF;
            $b = $rgba & 0xFF;
            $a = ($rgba & 0x7F000000) >> 24;
        } else {
            $group = imagecolorsforindex($this->image, $rgba);
            $r = $group['red'];
            $g = $group['green'];
            $b = $group['blue'];
            $a = $group['alpha'];
        }
        $color = new Color($r, $g, $b, $a);
        return $color;
    }

    /**
     * @param Color $color
     * @param Point $point
     */
    public function setColorAt($color, $point)
    {
        imagesetpixel($this->image, $point->x, $point->y, $color->getColorIndexForImage($this->image));
    }

    /**
     * @param Point $point
     * @return int 0x1 or 0x0
     */
    public function readColorBitAt($point)
    {
        $color = $this->getColorAt($point);
        $bit = (($color->r + $color->g + $color->b) % 2 === 0 ? 0 : 1);
        return $bit;
    }

    /**
     * @param int|bool $bit
     * @param Point $point
     */
    public function modifyColorForBitAt($bit, $point)
    {
        $original_color = $this->getColorAt($point);
        if ($bit) {
            if (($original_color->r + $original_color->g + $original_color->b) % 2 === 0) {
                $new_color = Color::copyColor($original_color);
                if ($original_color->r < 255) {
                    $new_color->r += 1;
                } elseif ($original_color->g < 255) {
                    $new_color->g += 1;
                } elseif ($original_color->b < 255) {
                    $new_color->b += 1;
                }
                $this->setColorAt($new_color, $point);
            }
        } else {
            if (($original_color->r + $original_color->g + $original_color->b) % 2 === 1) {
                $new_color = Color::copyColor($original_color);
                if ($original_color->r > 0) {
                    $new_color->r -= 1;
                } elseif ($original_color->g > 255) {
                    $new_color->g -= 1;
                } elseif ($original_color->b > 255) {
                    $new_color->b -= 1;
                }
                $this->setColorAt($new_color, $point);
            }
        }
    }


    /**
     * @param null|string $output
     * @return bool
     */
    public function outputPNG($output = null)
    {
        return imagepng($this->image, $output);
    }
}