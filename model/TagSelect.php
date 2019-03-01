<?php
/**
 * Created by PhpStorm.
 * User: nima
 * Date: 28.04.18
 * Time: 15:13
 */

namespace Model;

use PDO;

/**
 * Class TagSelect
 * @package Model
 */
class TagSelect extends OperationsDb
{
    /**
     * @var string
     */
    private $id = 'id';
    /**
     * @var string
     */
    private $name = 'name';
    /**
     * @var array
     */
    private $filedsName = ["id" , "name"];
    /**
     * @var string
     */
    private $tableName = 'tags';

    /**
     * @return array
     */
    public function showTags(): array 
    {
        $stmt = $this->db->prepare("SELECT id, name FROM tags");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @return array
     */
    public function getTags(): array 
    {
        return
           parent::selectElements($this->tableName, $this->filedsName);
    }

    // для роута редагування або видалення публікації по id в url(без аякса)
    /**
     * @param $param
     * @return \stdClass
     */
    public function getTagById($param): \stdClass
    {
        return
            parent::selectElement($this->id, $this->tableName, $param);
    }

    /**
     * @param $object
     * @return string
     * @throws \PDOException
     */
    public function updateElement(\stdClass $object): string
    {

        // використовуєм quote для екранірування даних (наприклад апострофи' і тому подібне)
        $stmt = $this->db->prepare("UPDATE $this->tableName set name = " . $this->db->quote($object->name) ." 
               WHERE id =" . $this->db->quote($object->id)."");
        //$stmt->execute();
        try {
            $stmt->execute();
            // перевіряєм чи відбулось оновлення запису БД, якщо було то rowCount() =1, інакше =0
            $countUbdate = $stmt->rowCount();
            // якщо оновлення відбулось знач вертаємо відповідь 'ok' (цю відповідь потім контролер вертає js
            // де в скрипту в функції аякса перевіряється цей парамтр (якщо 'ok' то js оноляє строчуку із змінами ні то видає помилку))
            if ($countUbdate == 1) {
                return 'ok';
            } else {
                return 'not updated';
            }
        } catch (PDOException $e) {
            return $e->getMessage();
            //return 'not updated';
        }
    }

    /**
     * @param $id
     * @return string
     */
    public function deleteTag($id): string
    {
        return
           parent::deleteElement($id, $this->tableName);
    }
}
