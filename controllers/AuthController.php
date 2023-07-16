<?php
/**
 * Created by PhpStorm.
 * User: dev1
 * Date: 2.6.2015 г.
 * Time: 19:37 ч.
 */

namespace controllers;

use models\UserModel;
use stz184\FormValidator\Validator;
use stz184\session\Session;

class AuthController extends Controller
{

    public static function actionLogin()
    {
        self::requireGuest();

        $userData = \Flight::request()->data->getData();
        $validator = new Validator($userData);
        $validator->registerValidator('token', function (Validator $validator, $field) {
            if ($validator->getValue($field) != Session::getCRSFToken()) {
                $validator->setError($field, 'Session expired. Please reload the page and try again');
                return false;
            }
            return true;
        });

        $validator->addRule('username', 'Username', ['trim', 'required']);
        $validator->addRule('password', 'Password', ['required']);
        $validator->addRule('token', 'Token', ['token']);

        if (count($userData)) {
            if ($validator->validate()) {
                $user = UserModel::getUserByUsernameAndPassword($userData['username'], $userData['password']);
                if ($user) {
                    Session::setUserData('id', $user['id']);
                    Session::setUserData('email', $user['email']);
                    Session::setUserData('username', $user['username']);
                    Session::setFlashData('info', 'You have been successfully logged in.');

                    if (isset($userData['remember_me'])) {
                        setcookie(AUTH_COOKIE, $user['auth_key'], time() + 30*24*3600);
                    }

                    Session::writeClose();
                    \Flight::redirect('/');
                    return;
                } else {
                    Session::setFlashData('danger', 'Wrong username or password');
                }
            } else {
                Session::setFlashData('danger', implode("<br />", $validator->getErrors()));
            }
        }


        \Flight::render('login', [
            'token'     => Session::getCRSFToken(),
            'errors'    => $validator->getErrors()
        ], 'content');
        \Flight::render('layout', ['title' => 'Login']);
    }

    public static function actionSignUp()
    {
        self::requireGuest();

        $userData = \Flight::request()->data->getData();
        $validator = new Validator($userData);
        $validator->registerValidator('captcha', function (Validator $validator, $field) {
            $value = $validator->getValue($field);
            if ($value != Session::get('captcha')) {
                $validator->setError($field, 'Invalid security code');
                return false;
            }
            return true;
        });

        $validator->registerValidator('username', function (Validator $validator, $field) {
            $value = $validator->getValue($field);
            if (!$value) {
                return true;
            }

            if (!preg_match('/^\w+$/', $value)) {
                $validator->setError($field, 'The username should contains only word character (alphanumeric & underscore).');
                return false;
            }

            if (UserModel::isUsernameExists($value)) {
                $validator->setError($field, 'The username is already in use');
                return false;
            }

            return true;
        });

        $validator->registerValidator('token', function (Validator $validator, $field) {
            if ($validator->getValue($field) != Session::getCRSFToken()) {
                $validator->setError($field, 'Session expired. Please reload the page and try again');
                return false;
            }
            return true;
        });

        $validator->addRule('username', 'Username', ['trim', 'strip_tags', 'required', 'minlength' => 5, 'username']);
        $validator->addRule('email', 'E-mail', ['trim', 'strip_tags', 'required', 'email']);
        $validator->addRule('password', 'Password', ['required', 'minlength' => 6]);
        $validator->addRule('password_repeat', 'Repeat password', ['required', 'matches' => 'password']);
        $validator->addRule('captcha', 'captcha', ['captcha']);
        $validator->addRule('token', 'token', ['token']);

        if (count($userData)) {
            if ($validator->validate()) {
                UserModel::addUser($userData);
                Session::setFlashData('info', 'Your account is successfully created. You can login right now!');
                /** Regenerate the CRSF token in order to prevent form resubmition */
                Session::getCRSFToken(true);
                Session::writeClose();
                \Flight::redirect('/login');
                return;
            } else {
                array_walk($userData, function (&$item, $key) {
                    $item = is_string($item) ? htmlspecialchars($item) : $item;
                });
                Session::setFlashData('danger', implode("<br />", $validator->getErrors()));
            }
        }

        \Flight::render('signup', [
            'token'     => Session::getCRSFToken(),
            'userData'  => $userData,
            'errors'    => $validator->getErrors()
        ], 'content');
        \Flight::render('layout', ['title' => 'Register new account']);
    }

    public static function actionChangePassword()
    {
        self::requireLogin();

        $userData = \Flight::request()->data->getData();
        $validator = new Validator($userData);
        $validator->registerValidator('token', function (Validator $validator, $field) {
            if ($validator->getValue($field) != Session::getCRSFToken()) {
                $validator->setError($field, 'Session expired. Please reload the page and try again');
                return false;
            }
            return true;
        });

        $validator->registerValidator('password', function (Validator $validator, $field) {
            $val = $validator->getValue($field);
            if (!$val) {
                return true;
            }

            if (!UserModel::getUserByUsernameAndPassword(Session::getUserData('username'), $val)) {
                $validator->setError($field, 'Invalid current password');
                return false;
            }
            return true;
        });

        $validator->addRule('password', 'Current Password', ['required', 'password']);
        $validator->addRule('new_password', 'Password', ['required']);
        $validator->addRule('new_password_repeat', 'Repeat Password', ['required']);
        $validator->addRule('token', 'Token', ['token']);

        if (count($userData)) {
            if ($validator->validate()) {
                if (UserModel::updatePassword(Session::getUserData('id'), $userData['new_password_repeat'])) {
                    Session::setFlashData('info', 'Your password has been successfully changed.');
                }
                Session::getCRSFToken(true);
            }
        }

        \Flight::render('change-password', [
            'token'     => Session::getCRSFToken(),
            'errors'    => $validator->getErrors()
        ], 'content');
        \Flight::render('layout', ['title' => 'Change Password']);
    }

    public static function actionLogout()
    {
        self::requireLogin();

        Session::destroy();
        if (isset($_COOKIE[AUTH_COOKIE])) {
            setcookie(AUTH_COOKIE, null, time() - 3600);
        }

        \Flight::redirect('/');
    }
}