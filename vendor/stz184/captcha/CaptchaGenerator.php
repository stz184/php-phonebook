<?php
/**
 * Created by PhpStorm.
 * User: dev1
 * Date: 3.6.2015 г.
 * Time: 00:24 ч.
 */

namespace stz184\captcha;


class CaptchaGenerator
{
    protected $width;
    protected $height;
    protected $code;

    public function __construct($width = 146, $height = 30, $code = null)
    {
        $this->width    = $width;
        $this->height   = $height;
        $this->code     = $code ? $code : mb_substr(uniqid(mt_rand(), true), 0, 6);
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getImage()
    {
        header("Content-type: image/png");

        $im = @imagecreate($this->width, $this->height) or die("Cannot Initialize new GD image stream");

        imagecolorallocate($im, 255, 250, 255);
        $noise_color = imagecolorallocate($im, 207, 239, 250);
        for ($i = 0; $i < ($this->width * $this->height) / 3; $i++) {
            imagefilledellipse($im, mt_rand(0, $this->width), mt_rand(0, $this->height), 1, 1, $noise_color);
        }

        /* generate random lines in background */
        for ($i = 0; $i < ($this->width * $this->height) / 150; $i++) {
            imageline($im, mt_rand(0, $this->width), mt_rand(0, $this->height), mt_rand(0, $this->width), mt_rand(0, $this->height), $noise_color);
        }

        $text_color[0] = imagecolorallocate($im, 255, 0, 0);
        $text_color[1] = imagecolorallocate($im, 51, 166, 207);

        for ($j = 0; $j < mb_strlen($this->code); $j++) {
            imagettftext($im, 20, 0, 5 + ($j * 23), 24, $text_color[$j % 2], __DIR__ . '/fonts/offshore.ttf', $this->code[$j]);
        }

        imagepng($im);
        imagedestroy($im);
    }
}