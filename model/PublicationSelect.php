<?php
/**
 * Created by PhpStorm.
 * User: nima
 * Date: 28.04.18
 * Time: 15:13
 */

namespace Model;

use PDO;

//class PublicationSelect extends BaseCRUDModel

/**
 * Class PublicationSelect
 * @package Model
 */
class PublicationSelect extends OperationsDb
{
    /**
     * @var string
     */
    private $tableName = "publications";
    /**
     * @var array
     */
    private $filedsNmae = ["id", "name", "full_text", "image", "dt_of_pub"];
    /**
     * @var string
     */
    private $id = "id";
    /**
     * @var string
     */
    private $name = "name";
    /**
     * @var string
     */
    private $fullText = "full_text";
    /**
     * @var string
     */
    private $image = "image";
    /**
     * @var string
     */
    private $dtOfPub = "dt_of_pub";

    /**
     * @return array
     */
    public function getPublications(): array
    {
        return
            parent::selectElements($this->tableName, $this->filedsNmae);
    }

    // for rout editing or deleting post via id in url (without ayax)
    /**
     * @param $param
     * @return null|\stdClass
     */
    public function getPublicationById($param): ?\stdClass
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
        $stmt = $this->db->prepare("UPDATE $this->tableName set name = " . $this->db->quote($object->name) .", 
               full_text =" . $this->db->quote($object->full_text) ." , 
               dt_of_pub =" . $this->db->quote($object->dt_of_pub).", 
               image =" . $this->db->quote($object->image). " 
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

   // sample the array of name tags that refer to the publication specified by id
   // (sampling is done using the tag_publications link table between tags and posts)
    /**
     * @param $id
     * @return array
     */
    public function getTagsNamesByIdPublication($id): array
    {
        $stmt = $this->db->prepare("SELECT t.name FROM tags t, 
            tags_publications t_p WHERE t.id = t_p.tag_id AND t_p.publication_id = $id ");
        $stmt->execute();
        // PDO :: FETCH_COLUMN, 0 array-selection of all values of the first column (we have one stop name,
        // will therefore only briefly display its value "0 => 'value'" ....)
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    // the selection of publications related to the tag transmitted by the parameter (name of the tag)
    // (sampling is done using the tag_publications link table between tags and posts)
    // use quote for data screening (such as apostrophes and the like)
    /**
     * @param string $tagName
     * @return array
     */
    public function getPublicationsByTagName($tagName): array
    {
//        $stmt = $this->db->prepare("SELECT p.* FROM tags t,
//            tags_publications t_p, publications p WHERE t.id = t_p.tag_id
//            AND t.name = ".$this->db->quote($tagName)." AND p.id = t_p.publication_id");
//        $stmt->execute();
        $stmt = $this->db->prepare("SELECT p.* FROM tags t, tags_publications t_p, publications p 
            where t.name = ".$this->db->quote($tagName)." AND t.id =t_p.tag_id AND p.id = t_p.publication_id");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * @param int $id
     * @return string
     */
    public function deletePublication($id): string 
    {
        return
            parent::deleteElement($id, $this->tableName);
    }
}
