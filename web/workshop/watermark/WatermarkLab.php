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

    public function mixStringIntoImage_v1($string)
    {
        $string = base64_encode($string);
        $bytes = unpack('C*', $string);
        $mask = [
            0b10000000,
            0b01000000,
            0b00100000,
            0b00010000,
            0b00001000,
            0b00000100,
            0b00000010,
            0b00000001,
        ];
        $bits = [];
        for ($i = 0; $i < 8 * 4; $i++) $bits[] = true;
        foreach ($bytes as $byte) {
            // byte is 0-255(2^8-1) 0b00000000
            for ($i = 0; $i < 8; $i++) {
                $nonZero = (($byte & $mask[$i]) !== 0);
                $bits[] = $nonZero ? true : false;
            }
        }
        for ($i = 0; $i < 8 * 4; $i++) $bits[] = true;

//        echo "generated bits length: ".count($bits).PHP_EOL;
//        echo "bits:".PHP_EOL;
//        foreach ($bits as $bit)echo ($bit?'1':'0');
//        echo PHP_EOL;

        $width = imagesx($this->image);
        $height = imagesy($this->image);

        $ptr = 0;
        $y = 0;
        $changedPixelCount = 0;
        while ($y < $height) {
            $x = 0;
            while ($x < $width) {
                $point = Point::byXY($x, $y);
                $original_color = $this->getColorAt($point);

                // if $bits[$ptr] then r+g+b should be odd or even
                if ($bits[$ptr]) {
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
                        $changedPixelCount++;
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
                        $changedPixelCount++;
                    }
                }

                $ptr = ($ptr + 1) % count($bits);
                $x++;
            }
            $y++;
        }
        //echo "changedPixelCount=".$changedPixelCount.PHP_EOL;
        return true;
    }

    public function extractStringFromImage_v1()
    {
        $width = imagesx($this->image);
        $height = imagesy($this->image);

        $bits = [];

        $y = 0;
        while ($y < $height) {
            $x = 0;
            while ($x < $width) {
                $point = Point::byXY($x, $y);
                $original_color = $this->getColorAt($point);

                $bits[] = (($original_color->r + $original_color->g + $original_color->b) % 2 === 0 ? false : true);

                $x++;
            }
            $y++;
        }

//        echo "read bits:".PHP_EOL;
//        foreach ($bits as $bit)echo ($bit?'1':'0');
//        echo PHP_EOL;

        // UTF-8 的编码 开头结尾都不能保证全0全1 但子集ascii可以保证开头都是0

        $linkedTrueSinceStart = -1;
        $linkedTrueCountStart = 0;
        for ($index = 0; $index < count($bits); $index++) {
            $bit = $bits[$index];
            if ($bit === true) {
                if ($linkedTrueSinceStart < 0) {
                    $linkedTrueSinceStart = $index;
                    $linkedTrueCountStart = 1;
                } else {
                    $linkedTrueCountStart += 1;
                }
                if ($linkedTrueCountStart >= 8 * 4) {
                    break;
                }
            } else {
                $linkedTrueSinceStart = -1;
                $linkedTrueCountStart = 0;
            }
        }

        if ($linkedTrueSinceStart < 0) {
            return false;
        }

        $linkedTrueSinceEnd = -1;
        $linkedTrueCountEnd = 0;
        for ($index = $linkedTrueSinceStart + 8 * 4; $index < count($bits); $index++) {
            $bit = $bits[$index];
            if ($bit === true) {
                if ($linkedTrueSinceEnd < 0) {
                    $linkedTrueSinceEnd = $index;
                    $linkedTrueCountEnd = 1;
                } else {
                    $linkedTrueCountEnd += 1;
                }
            } else {
                if ($linkedTrueCountEnd >= 8 * 4) {
                    $linkedTrueSinceEnd += ($linkedTrueCountEnd - 8 * 4);
                    break;
                }
                $linkedTrueSinceEnd = -1;
                $linkedTrueCountEnd = 0;
            }
        }

        if ($linkedTrueSinceEnd < 0) {
            return false;
        }

        echo __METHOD__ . '@' . __LINE__ . " since and end: " . json_encode([$linkedTrueSinceStart, $linkedTrueSinceEnd]) . PHP_EOL;

        //echo "generated bits length: ".count($bits).PHP_EOL;

        $bytes = [];
        $byte = 0;
        //echo "0b";
        for ($index = $linkedTrueSinceStart + 8 * 4; $index < $linkedTrueSinceEnd; $index++) {
            $realIndex = $index - ($linkedTrueSinceStart + 8 * 4);
            //echo ($bits[$index]?'1':'0');
            $byte = ($byte << 1) | $bits[$index];
            if ($realIndex % 8 === 7) {
                $bytes[] = $byte;
                $byte = 0;
                //echo PHP_EOL;
                //echo "0b";
            }
        }
        //echo "---".PHP_EOL;

        $string = [];
        foreach ($bytes as $byte) {
            $string[] = chr($byte);
        }
        $result = implode("", $string);
        $result = base64_decode($result);
        return $result;

        //return implode(",",$bytes);
        //return $bytes;
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