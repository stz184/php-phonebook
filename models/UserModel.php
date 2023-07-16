<?php
/**
 * Created by PhpStorm.
 * User: dev1
 * Date: 3.6.2015 г.
 * Time: 19:56 ч.
 */

namespace models;

class UserModel {

    /**
     * @param string $username
     * @return bool
     */
    public static function isUsernameExists($username)
    {
        /** @var \PDO $db */
        $db = \Flight::db();
        $stmt = $db->prepare("SELECT 1 FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $stmt->bindColumn(1, $check, \PDO::PARAM_INT);
        $stmt->fetch(\PDO::FETCH_BOUND);
        return $check === 1;
    }

    /**
     * @param array $user (Required keys username, email and password)
     * @return bool
     */
    public static function addUser(array $user)
    {
        /** @var \PDO $db */
        $db     = \Flight::db();
        $stmt   = $db->prepare("
            INSERT
              INTO users
                (username, auth_key, password_hash, password_reset_token, email, status)
              VALUE
                (:username, :auth_key, :password_hash, :password_reset_token, :email, :status)
        ");

        $password_hash          = password_hash($user['password'], PASSWORD_BCRYPT);
        $auth_key               = sha1(uniqid('ak_', true));
        $password_reset_token   = sha1(uniqid('prt_', true));
        $status                 = 'ACTIVE';
        $stmt->bindParam(':username', $user['username']);
        $stmt->bindParam(':auth_key', $auth_key);
        $stmt->bindParam(':password_hash', $password_hash);
        $stmt->bindParam(':password_reset_token', $password_reset_token);
        $stmt->bindParam(':email', $user['email']);
        $stmt->bindParam(':status', $status);
        return $stmt->execute();
    }

    /**
     * @param string $username
     * @param string $password non-hashed plain password
     * @return bool|array Array with the user data on success or boolean false on failure
     */
    public static function getUserByUsernameAndPassword($username, $password)
    {
        /** @var \PDO $db */
        $db = \Flight::db();
        $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        if ($user = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            if (password_verify($password, $user['password_hash'])) {
                return $user;
            }
        }
        return false;
    }

    /**
     * @param string $authKey Authentication cookie key
     * @return bool|array Array with the user data on success or boolean false on failure
     */
    public static function getUserByAuthKey($authKey)
    {
        /** @var \PDO $db */
        $db = \Flight::db();
        $stmt = $db->prepare("SELECT * FROM users WHERE auth_key = :auth_key");
        $stmt->bindParam(':auth_key', $authKey);
        $stmt->execute();
        if ($user = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            return $user;
        }
        return false;
    }

    /**
     * @param int $userID
     * @param str $password Plain-text non hashed password
     * @return bool
     */
    public static function updatePassword($userID, $password)
    {
        /** @var \PDO $db */
        $db = \Flight::db();
        $stmt = $db->prepare("UPDATE users SET password_hash = :password_hash WHERE id = :id");
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt->bindParam(':id', $userID, \PDO::PARAM_INT);
        $stmt->bindParam(':password_hash', $password_hash);
        return $stmt->execute();
    }
}
