<?php
/**
 * Created by PhpStorm.
 * User: Nikolay
 * Date: 21.10.2019
 * Time: 11:39
 */

namespace core;

class baseModel
{
    public $DB;

    public function __construct()
    {
        $this->DB = new \db();
    }

    /**
     * insert data and return last insert id
     *
     * @param array  $ARR
     * @param string $table
     *
     * @return bool
     */
    public function insertReturnLastId($ARR, $table)
    {
        if ($this->DB->insert($table, $ARR) == true) {
            return $this->DB->lastInsertId;
        } else {
            return false;
        }
    }

    /**
     * @param array  $data
     * @param string $table
     *
     * @return bool
     */
    public function insertData($data, $table)
    {
        if ($this->DB->insert($table, $data) == true) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param array  $ARR
     * @param array  $where
     * @param string $table
     *
     * @return bool
     */
    public function updateData($ARR, $where, $table)
    {
        if ($this->DB->update($table, $ARR, $where)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param string $ColVal
     * @param string $colName
     * @param string $table
     *
     * @return int
     */
    public function deleteData($ColVal, $colName, $table)
    {
        try {
            $sql = "DELETE FROM $table WHERE $colName = :ColVal";

            $stmt = $this->DB->pdo->prepare($sql);

            $stmt->bindValue(":ColVal", $ColVal);
            $stmt->execute();

            return $stmt->rowCount();
        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * @param string|int|bool $ColVal
     * @param string          $ColName
     * @param string          $table
     *
     * @return mixed
     */
    public function getRow($ColVal, $ColName, $table)
    {
        $sql = "SELECT *  FROM $table WHERE `{$ColName}`=:ColVal";

        $stmt = $this->DB->pdo->prepare($sql);

        $stmt->bindValue(":ColVal", $ColVal);
        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Get data from db single condition
     *
     * @param string|int|bool $ColVal
     * @param string          $ColName
     * @param string          $table
     *
     * @return array
     */
    public function getDataSingleCon($ColVal, $ColName, $table)
    {
        $sql = "SELECT *  FROM $table WHERE `{$ColName}`=:ColVal";

        $stmt = $this->DB->pdo->prepare($sql);

        $stmt->bindValue(":ColVal", $ColVal);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function retrieveAll($table)
    {
        $sql  = "SELECT * FROM $table";
        $stmt = $this->DB->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}