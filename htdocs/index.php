<?php
use stz184\session\Session;

define('ENVIRONMENT', gethostname() === 'php' ? 'dev' : 'prod');
error_reporting(ENVIRONMENT == 'dev' ? E_ALL : 0);
ini_set('display_errors', ENVIRONMENT == 'dev' ? '1' : '0');

require_once __DIR__ . '/../config/' . ENVIRONMENT . '/config.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../helpers/general.php';

$requestStartTime = microtime(true);

Session::start();

$_POST  = sanitize($_POST);
$_GET   = sanitize($_GET);


Flight::before('start', function() use($requestStartTime) {
    Flight::view()->set('start', $requestStartTime);
    if (isset($_COOKIE[AUTH_COOKIE])) {
        if ($user = \models\UserModel::getUserByAuthKey($_COOKIE[AUTH_COOKIE])) {
            Session::setUserData('id', $user['id']);
            Session::setUserData('email', $user['email']);
            Session::setUserData('username', $user['username']);
        }
    }
});

/**
 * Hide the error details on production environment
 */
if (ENVIRONMENT == 'prod') {
    Flight::map('error', function (Exception $ex) {
        $msg = '<h1>500 Internal Server Error</h1>';
        try {
            $this->response(false)
                ->status(500)
                ->write($msg)
                ->send();
        }
        catch (\Exception $ex) {
            exit($msg);
        }
    });
}

Flight::set('flight.views.path', __DIR__ . '/../views');
Flight::register('db', 'stz184\database\MySQLDatabase', [
    DATABASE_HOST,
    DATABASE_USER,
    DATABASE_PASS,
    DATABASE_NAME
]);

Flight::register('db', '\PDO', ['mysql:host='.DATABASE_HOST.';dbname='.DATABASE_NAME, DATABASE_USER, DATABASE_PASS, [
    \PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
]]);

Flight::register('alerter', 'stz184\notifications\Alerter');

Flight::route('/', ['controllers\SiteController', 'actionIndex']);
Flight::route('/login', ['controllers\AuthController', 'actionLogin']);
Flight::route('/logout', ['controllers\AuthController', 'actionLogout']);
Flight::route('/signup', ['controllers\AuthController', 'actionSignUp']);
Flight::route('/change-password', ['controllers\AuthController', 'actionChangePassword']);
Flight::route('/captcha', ['controllers\SiteController', 'actionCaptcha']);
Flight::route('/phonebook', ['controllers\PhoneBookController', 'actionIndex']);
Flight::route('/phonebook/add', ['controllers\PhoneBookController', 'actionAdd']);
Flight::route('/phonebook/delete/@id:[0-9]{1,}', ['controllers\PhoneBookController', 'actionDelete']);
Flight::route('/phonebook/update/@id:[0-9]{1,}', ['controllers\PhoneBookController', 'actionUpdate']);

Flight::start();
