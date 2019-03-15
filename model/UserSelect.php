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

    // for rout editing or deleting user by id in url (without ayax)
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

           // as we have external keys that connect the user with publications,
           // so before we delete the user, we must check if there are any posts in it
           // (in the binding table publications_users we are looking for the number of users if there is something we will send a message that is not possible
           // delete the user because there are posts in it)
           // Well, if it does not do the check, and does delete the user then
           // will issue an error "Can not delete or update a parent row: a foreign key constraint file"
           // in the publications_users subsidiary table, the connection of the external key is not a cascade deletion,
           // and restrict
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
               // check if there was an update to the database record, if it was rowCount () = 1, otherwise = 0
               $countUbdate = $stmt->rowCount();
               // if the update occurred, then we return the answer 'ok' (this answer then returns the controller to js
               // where in the script in the function of Ajax this parameter is checked (if 'ok' then js does not make a line with the changes, then it makes a error))
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
