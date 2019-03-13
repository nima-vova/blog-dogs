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
 * Class UserSelect
 * @package Model
 */   
class UserSelect extends OperationsDb
   {
    /**
     * @var string
     */
       private $tableName = "users";
    /**
     * @var string
     */
       private $id = "id";
    /**
     * @var string
     */
       private $firstName = "first_name";
    /**
     * @var string
     */
       private $lastName = "last_name";
    /**
     * @var string
     */
       private $login = "login";
    /**
     * @var string
     */
       private $dtOfRegist = "dt_of_regist";
    /**
     * @var string
     */
       private $roleId = "role_id";
    /**
     * @var string
     */
       private $token = "token";
    /**
     * @var array
     */
       private $filedsName = ["id", "first_name", "last_name", "login", "dt_of_regist", "role_id"];

    /**
     * @param $param
     * @return null|\stdClass
     */
       public function getUserByToken($param): ?\stdClass
       {
           return
               parent::selectElement($this->token, $this->tableName, $param);
       }

    /**
     * @param $param
     * @return null|\stdClass
     */
       public function getUserByLogin($param): ?\stdClass
       {
           return
               parent::selectElement($this->login, $this->tableName, $param);
       }
       // для роута редагування або видалення користувача по id в url(без аякса)
    /**
     * @param $param
     * @return null|\stdClass
     */
       public function getUserById($param): ?\stdClass 
       {
           return
               parent::selectElement($this->id, $this->tableName, $param);
       }

    /**
     * @param $id
     * @return string
     */
       public function deleteUser($id): string
       {

           // так як в нас є зовнішні ключі, які звязують користувача з публікаціями,
           // тому перед тим як видалять користувача ми повинні перевірити чи є в нього публікації
           //(в звязуючій таблиці publications_users шукаєм кількість користувачів якщо є то видамо повідомлення що не можливо
           //видалять користувача бо є в нього публікації)
           // Навідь якщо не робить перевірку, і робить видалення користувача то
           // видасть помилку "Cannot delete or update a parent row: a foreign key constraint fails"
           // адже в дочірній таблиці publications_users ти звязку зовнішнього ключа не каскадне видалення,
           // а restrict
           $stmt = $this->db->prepare("SELECT COUNT(*) AS count FROM publications_users WHERE user_id = '$id'");
           //return $stmt->execute();
           $stmt->execute();
           $result = $stmt->fetchObject();
           if ($result->count==0) {
               return parent::deleteElement($id, $this->tableName);
           } else {
               return 'not delete, because this user has posts';
           }
       }
       // використовуєм quote для екранірування даних (наприклад апострофи' і тому подібне)
    /**
     * @param $object
     * @return string
     * @throws \PDOException
     */
       public function updateElement(\stdClass $object): string
       {
           $stmt = $this->db->prepare("UPDATE $this->tableName set first_name =" . $this->db->quote($object->first_name) .", 
               last_name =" . $this->db->quote($object->last_name) .", 
               login =" . $this->db->quote($object->login) .", 
               dt_of_regist =" . $this->db->quote($object->dt_of_regist) .",
               role_id =" . $this->db->quote($object->role_id) ." 
               WHERE id = '$object->id'");
           // $stmt->execute();
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
     * @param string $login
     * @param string $token
     * @param string $userAgent
     * @return void
     */ 
       public function alterTokenForUser(string $login, string $token, string $userAgent): void
       {
           $stmt = $this->db->prepare("UPDATE $this->tableName set token = '$token', user_agent = '$userAgent' 
            WHERE login = '$login'");
           $stmt->execute();

//        $stmt->execute(array('token' => $token, 'user_agent' => $userAgent,
//            'login' => $login));
       }

    /**
     * @return array
     */    
       public function getUsers(): array 
       {
           return
                parent::selectElements($this->tableName, $this->filedsName);
       }
//    public function showUsers(){
//
//        $stmt = $this->db->prepare("SELECT id, first_name, last_name, login, dt_of_regist, role_id FROM users");
//        $stmt->execute();
//        return $stmt->fetchAll(PDO::FETCH_ASSOC);

 //}
   }
