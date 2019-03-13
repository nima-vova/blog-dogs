<?php
/**
 * Created by PhpStorm.
 * User: nima
 * Date: 08.12.18
 * Time: 17:43
 */

namespace Model;

use PDO;
use PDOException;
use Config\DbParametersConfig;

/**
 * Class OperationsDb
 * @package Model
 */
abstract class OperationsDb implements CrudDb
{
    private $dbParameters;
    protected $host;
    protected $dbName;
    protected $user;
    protected $password;

    protected $db;
    public $nameObject;
    public $NewParamsObject;
    public $paramsObject;
    public $nameValues;
    public $values;

    /**
     * OperationsDb constructor.
     * @throws PDOException
     */
    public function __construct()
    {
        $this->dbParameters = new DbParametersConfig();
        $this->host  = $this->dbParameters->host;
        $this->dbName = $this->dbParameters->dbName;
        $this->user = $this->dbParameters->user;
        $this->password = $this->dbParameters->password;

        try {
            $this->db = new PDO("mysql:host=$this->host;dbname=$this->dbName",
                $this->user, $this->password);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->db->exec("set names utf8");
        } catch (PDOException $e) {
            echo $e->getMessage();
            // $this->db = $db;
            //return $this->db;
        }
    }

    /**
     * return void
     */
    public function createElement(): void
    {
        $stmt = $this->db->prepare("INSERT INTO $this->nameObject($this->nameValues) VALUES ($this->values)");
        $stmt -> execute($this->NewParamsObject);
    }

    /**
     * @param $fieldName
     * @param $tableName
     * @param $param
     * @return null|\stdClass
     */
    public function selectElement($fieldName, $tableName, $param): \stdClass
    {
        $stmt = $this->db->prepare("SELECT * FROM $tableName WHERE $fieldName = '$param'");
        $stmt->execute();
        $result = $stmt->fetchObject();
        //return var_dump($result);
        if ($result == false) {
           return null;
          } 
        return $result;
    }

    /**
     * @param $object
     */
    abstract public function updateElement(\stdClass $object);

    // видаляєм елементи по переданому id але лише в тих таблицях де id не є зовнішнім ключем,
    // або якщо в дочірньої таблиці стоїть cascade
    /**
     * @param $id
     * @param $tableName
     * @return string
     * 
     * @throws PDOException
     */
    public function deleteElement($id, $tableName): string
    {
        $stmt = $this->db->prepare("DELETE FROM $tableName WHERE id = '$id'");
        //return $stmt->execute();
        try {
            $stmt->execute();
            // перевіряєм чи відбулось оновлення запису БД, якщо було то rowCount() =1, інакше =0
            $countUbdate = $stmt->rowCount();
            //return $countUbdate;

            if ($countUbdate == 1) {
                return 'ok';
            } else {
                return 'not delete';
            }
        } catch (PDOException $e) {
            return $e->getMessage();
            //return 'not updated';
        }
    }

    /**
     * @param $tableName
     * @param $fieldsName
     * @return array
     */
    public function selectElements($tableName, $fieldsName): array
    {
        // перетворюємо масив із іменами полів в строку де імена полів
        // розіделні комою (як в запиті перелічується поля через кому)
        $strFieldsName = join(", ", $fieldsName);
        $stmt = $this->db->prepare("SELECT $strFieldsName FROM $tableName");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
