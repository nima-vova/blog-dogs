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

    // для роута редагування або видалення публікації по id в url(без аякса)
    /**
     * @param $param
     * @return mixed
     */
    public function getPublicationById($param): mixed
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
        $stmt = $this->db->prepare("UPDATE $this->tableName set name = " . $this->db->quote($object->name) .", 
               full_text =" . $this->db->quote($object->full_text) ." , 
               dt_of_pub =" . $this->db->quote($object->dt_of_pub).", 
               image =" . $this->db->quote($object->image). " 
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
    // вибірка масива імен тегів, які відносяться до публікації заданої по id
    // (вибірка робиться за допомогою звязуючої таблиці  tags_publications між тегами і публікаціямми)
    /**
     * @param $id
     * @return array
     */
    public function getTagsNamesByIdPublication($id): array
    {
        $stmt = $this->db->prepare("SELECT t.name FROM tags t, 
            tags_publications t_p WHERE t.id = t_p.tag_id AND t_p.publication_id = $id ");
        $stmt->execute();
        // PDO::FETCH_COLUMN, 0 масив-вибірка всіх значень першого стовпчика (в нас один стопчик name,
        // тому будуть виводитись коротко тільки його значення "0 => 'value'" ....)
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    // вибірка публікацій які відностяь до тега який передається параметром (назва тега)
    // (вибірка робиться за допомогою звязуючої таблиці  tags_publications між тегами і публікаціямми)
    // використовуєм quote для екранірування даних (наприклад апострофи' і тому подібне)
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
