<?php
/**
 * Created by PhpStorm.
 * User: dev1
 * Date: 5.6.2015 г.
 * Time: 11:56 ч.
 */

namespace models;


class PhoneBookModel {
    /**
     * @param int $userID
     * @param string $fullName
     * @return bool
     */
    public static function isContactExists($userID, $fullName)
    {
        /** @var \PDO $db */
        $db = \Flight::db();
        $stmt = $db->prepare("SELECT 1 FROM phonebook WHERE user_id = :user_id AND full_name = :full_name");
        $stmt->bindParam(':user_id', $userID, \PDO::PARAM_INT);
        $stmt->bindParam(':full_name', $fullName);
        $stmt->execute();
        $stmt->bindColumn(1, $check, \PDO::PARAM_INT);
        $stmt->fetch(\PDO::FETCH_BOUND);
        return $check === 1;
    }

    /**
     * @param array $data Required keys user_id, full_name, email, phone
     * @return bool
     */
    public static function addContact(array $data)
    {
        /** @var \PDO $db */
        $db     = \Flight::db();
        $stmt   = $db->prepare("
            INSERT
              INTO phonebook
                (user_id, full_name, email, phone)
              VALUE
                (:user_id, :full_name, :email, :phone)
        ");

        $stmt->bindParam(':user_id', $data['user_id'], \PDO::PARAM_INT);
        $stmt->bindParam(':full_name', $data['full_name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':phone', $data['phone']);

        return $stmt->execute();
    }

    /**
     * @param int $contactID
     * @param array $data Required keys full_name, email, phone
     * @return bool
     */
    public static function updateContact($contactID, array $data)
    {
        /** @var \PDO $db */
        $db     = \Flight::db();
        $stmt   = $db->prepare("
            UPDATE phonebook
            SET
              full_name = :full_name,
              email = :email,
              phone = :phone
            WHERE
              id = :id
        ");

        $stmt->bindParam(':id', $contactID, \PDO::PARAM_INT);
        $stmt->bindParam(':full_name', $data['full_name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':phone', $data['phone']);

        return $stmt->execute();
    }

    /**
     * @param integer $contactID
     * @param integer $userID
     * @return mixed
     */
    public static function getContact($contactID, $userID = null)
    {
        /** @var \PDO $db */
        $db = \Flight::db();
        $stmt = $db->prepare("SELECT * FROM phonebook WHERE id = :contact_id" . (is_numeric($userID) ? " AND user_id = :user_id" : ""));

        $stmt->bindParam(':contact_id', $contactID, \PDO::PARAM_INT);
        if (is_numeric($userID)) {
            $stmt->bindParam(':user_id', $userID, \PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * @param integer $userID
     * @param array $search
     * @return mixed
     */
    public static function countContacts($userID, $search = [])
    {
        /** @var \PDO $db */
        $db     = \Flight::db();
        $sql    = 'SELECT COUNT(id) AS num FROM phonebook WHERE user_id = :user_id';
        $searchClause = [];
        if (is_array($search)) {
            foreach ($search as $column => $query) {
                if (in_array($column, ['full_name', 'email', 'phone']) && trim($query) != '') {
                    $searchClause[$column] = $column . ' LIKE :'.$column;
                }
            }
            if (count($searchClause)) {
                $sql .= " AND " . implode(' AND ', $searchClause);
            }
        }

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':user_id', $userID, \PDO::PARAM_INT);
        foreach (array_keys($searchClause) as $column) {
            $query = '%' . $search[$column] . '%';
            $stmt->bindParam(':'.$column, $query);
        }
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return isset($row['num']) ? $row['num'] : false;
    }
    /**
     * @param integer $userID
     * @param array $search
     * @param string $orderBy
     * @param string $orderType
     * @param integer $offset
     * @param integer $limit
     * @return array
     */
    public static function getContacts($userID, $search = [], $orderBy = 'id', $orderType = 'desc', $offset = 0, $limit = 20)
    {
        /** @var \PDO $db */
        $db = \Flight::db();
        $sql = 'SELECT * FROM phonebook WHERE user_id = :user_id';
        $searchClause = [];

        if (is_array($search)) {
            foreach ($search as $column => $query) {
                if (in_array($column, ['full_name', 'email', 'phone']) && trim($query) != '') {
                    $searchClause[$column] = $column . ' LIKE :'.$column;
                }
            }
            if (count($searchClause)) {
                $sql .= " AND " . implode(' AND ', $searchClause);
            }
        }

        $orderBy    = in_array($orderBy, ['id', 'full_name', 'email', 'phone', 'created_at', 'updated_at']) ? $orderBy : 'id';
        $orderType  = in_array($orderType, ['asc', 'desc']) ? $orderType : 'asc';
        $sql .= ' ORDER BY ' . $orderBy . ' ' . $orderType . ' LIMIT ' . $offset . ', ' . $limit;

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':user_id', $userID, \PDO::PARAM_INT);
        foreach (array_keys($searchClause) as $column) {
            $query = '%' . $search[$column] . '%';
            $stmt->bindParam(':'.$column, $query);
        }
        $stmt->execute();
        $result = [];
        while($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $result[] = $row;
        }
        return $result;
    }

    /**
     * @param $contactID
     * @param int $userID
     * @return int
     */
    public static function deleteContact($contactID, $userID = null)
    {
        /** @var \PDO $db */
        $db = \Flight::db();

        $sql = "DELETE FROM phonebook WHERE id = :id";
        if (is_numeric($userID)) {
            $sql .= " AND user_id = :user_id";
        }

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $contactID, \PDO::PARAM_INT);
        if (is_numeric($userID)) {
            $stmt->bindParam(':user_id', $userID, \PDO::PARAM_INT);
        }

        return ($stmt->execute());
    }
}