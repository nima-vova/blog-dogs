<?php
/**
 * Created by PhpStorm.
 * User: nima
 * Date: 07.12.18
 * Time: 23:10
 */

namespace Model;
/**
 * Interface CrudDb
 * @package Model
 */
interface CrudDb
{
    public function createElement();
    public function selectElement($fieldName, $tableName, $param);
    //public function updateElement($login, $token, $userAgent);
    public function updateElement(\stdClass $object);
    public function deleteElement($id, $tableName);
    public function selectElements($tableName, $fieldsName);
}
