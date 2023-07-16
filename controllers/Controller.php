<?php
/**
 * Created by PhpStorm.
 * User: dev1
 * Date: 5.6.2015 г.
 * Time: 11:46 ч.
 */

namespace controllers;


use stz184\session\Session;

class Controller {
    protected static function requireLogin()
    {
        if (!Session::isLoggedIn()) {
            Session::setFlashData('waring', 'You have to login in order to complete this action');
            Session::writeClose();
            \Flight::redirect('/login');
            exit;
        }
    }

    protected static function requireGuest()
    {
        if (Session::isLoggedIn()) {
            \Flight::redirect('/');
            exit;
        }
    }
}