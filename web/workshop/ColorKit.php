<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2018/3/22
 * Time: 23:35
 */

namespace sinri\Verbum\workshop;


class ColorKit
{
    /**
     * @var resource
     */
    protected $img;

    /**
     * GDColorKit constructor.
     * @param resource $img
     */
    public function __construct($img)
    {
        $this->img = $img;
    }

    /**
     * @param int $r 0-255
     * @param int $g 0-255
     * @param int $b 0-255
     * @return int
     */
    public function rgb($r, $g, $b)
    {
        $imgColorId = imagecolorexact($this->img, $r, $g, $b);
        if ($imgColorId === -1) {
            $imgColorId = imagecolorallocate($this->img, $r, $g, $b);
        }
        return $imgColorId;
    }

    /**
     * @param int $r 0-255
     * @param int $g 0-255
     * @param int $b 0-255
     * @param int $a 0-127
     * @return int
     */
    public function rgba($r, $g, $b, $a)
    {
        $imgColorId = imagecolorexactalpha($this->img, $r, $g, $b, $a);
        if ($imgColorId === -1) {
            $imgColorId = imagecolorallocatealpha($this->img, $r, $g, $b, $a);
        }
        return $imgColorId;
    }

    /**
     * @param string $string #ABCDEF or ABCDEF
     * @return int
     */
    public function hex($string)
    {
        // #ABCDEF -> ABCDEF
        if (substr($string, 0, 1) === '#') $string = substr($string, 1);
        $d = [];
        for ($i = 0; $i < 6; $i++) {
            $x = substr($string, $i, 1);
            if ($x === false || !preg_match('/^[A-Fa-f0-9]$/', $x)) {
                $x = 0;
            }
            switch ($x) {
                case 'A':
                case 'a':
                    $x = 10;
                    break;
                case 'B':
                case 'b':
                    $x = 11;
                    break;
                case 'C':
                case 'c':
                    $x = 12;
                    break;
                case 'D':
                case 'd':
                    $x = 13;
                    break;
                case 'E':
                case 'e':
                    $x = 14;
                    break;
                case 'F':
                case 'f':
                    $x = 15;
                    break;
            }
            $d[] = $x;
        }
        return $this->rgb($d[0]*16+$d[1],$d[2]*16+$d[3],$d[4]*16+$d[5]);
    }

    // constants

    /**
     * @return int
     */
    public function black()
    {
        return $this->rgba(0x00, 0x00, 0x00, 0x00);
    }

    /**
     * @return int
     */
    public function white()
    {
        return $this->rgba(0xFF, 0xFF, 0xFF, 0x00);
    }
}