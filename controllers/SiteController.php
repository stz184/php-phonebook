<?php
/**
 * Created by PhpStorm.
 * User: dev1
 * Date: 2.6.2015 г.
 * Time: 22:10 ч.
 */

namespace controllers;

use stz184\captcha\CaptchaGenerator;
use stz184\session\Session;

class SiteController {
    public static function actionIndex()
    {
        \Flight::render('index', array(), 'content');
        \Flight::render('layout');
    }

    public static function actionCaptcha () {
        $captcha = new CaptchaGenerator();
        Session::set('captcha', $captcha->getCode());
        $captcha->getImage();
    }
}