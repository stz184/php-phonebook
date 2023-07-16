<?php

namespace stz184\session;

class Session
{
    private static $sessionID = null;

    const FLASH_DATA    = 'FLASH_DATA';
    const USER_DATA     = 'USER_DATA';
    const CRSF_TOKEN    = 'CRSF_TOKEN';

    /**
     * Initialize session data
     * @return bool
     */
    public static function start()
    {
        if (!headers_sent() && self::$sessionID == null) {
            if (session_start()) {
                self::$sessionID = session_id();
            }
        }

        return self::$sessionID !== null;
    }

    /**
     * Destroys all data registered to a session,
     * update the current session id with a newly generated one
     * and delete all old session data
     *
     * @return bool
     */
    public static function destroy()
    {
        if (self::$sessionID !== null) {
            if (session_destroy() && session_regenerate_id(true)) {
                $_SESSION = array();
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key = null, $default = null)
    {
        if (is_null($key)) {
            return isset($_SESSION) ? $_SESSION : $default;
        } else {
            return isset($_SESSION) && is_array($_SESSION) && array_key_exists($key, $_SESSION) ? $_SESSION[$key] : $default;
        }
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public static function setUserData($key, $value)
    {
        $_SESSION[self::USER_DATA][$key] = $value;
    }

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getUserData($key, $default = null)
    {
        return (
            isset($_SESSION[self::USER_DATA])
            && is_array($_SESSION[self::USER_DATA])
            && array_key_exists($key, $_SESSION[self::USER_DATA])
        ) ? $_SESSION[self::USER_DATA][$key] : $default;
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public static function setFlashData($key, $value)
    {
        $_SESSION[self::FLASH_DATA][$key] = $value;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public static function flashDataExists($key)
    {
        return (
            isset($_SESSION[self::FLASH_DATA])
            && is_array($_SESSION[self::FLASH_DATA])
            && array_key_exists($key, $_SESSION[self::FLASH_DATA])
        );
    }

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getFlashData($key = null, $default = null)
    {
        if (is_null($key)) {
            if (array_key_exists(self::FLASH_DATA, $_SESSION)) {
                $flashData = $_SESSION[self::FLASH_DATA];
                unset($_SESSION[self::FLASH_DATA]);
                return $flashData;
            }
            return $default;
        } elseif (self::flashDataExists($key)) {
            $flashData = $_SESSION[self::FLASH_DATA][$key];
            unset($_SESSION[self::FLASH_DATA][$key]);
            return $flashData;
        } else {
            return $default;
        }
    }

    public static function writeClose()
    {
        session_write_close();
    }

    /**
     * Check if session user data exists
     * @return bool
     */
    public static function isLoggedIn()
    {
        return (
            isset($_SESSION[self::USER_DATA])
            && is_array($_SESSION[self::USER_DATA])
            && count($_SESSION[self::USER_DATA]) > 0
        );
    }

    /**
     * @param bool $regenerate
     * @return string
     */
    public static function getCRSFToken($regenerate = false) {
        if (is_null($token = self::get(self::CRSF_TOKEN)) || $regenerate) {
            $token = sha1(uniqid('', true));
            self::set(self::CRSF_TOKEN, $token);
        }
        return $token;
    }
}