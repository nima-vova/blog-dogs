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

    // for rout editing or deleting post via id in url (without ayax)
    /**
     * @param $param
     * @return null|\stdClass
     */
    public function getTagById($param): ?\stdClass
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

        // use quote for data screening (such as apostrophes and the like)
        $stmt = $this->db->prepare("UPDATE $this->tableName set name = " . $this->db->quote($object->name) ." 
               WHERE id =" . $this->db->quote($object->id)."");
        //$stmt->execute();
        try {
            $stmt->execute();
            // check if there was an update to the database record, if it was rowCount () = 1, otherwise = 0
            $countUbdate = $stmt->rowCount();
            // if the update occurred, then we return the answer 'ok' (this answer then returns the controller to js
            // where in the script in the function of ayax this parameter is checked (if 'ok' then js does not make a line with changes, then it issues an error))
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
