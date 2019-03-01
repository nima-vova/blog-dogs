<?php
namespace Tests;

/**
     * Created by PhpStorm.
     * User: nima
     * Date: 01.08.18
     * Time: 15:53
     */
use Faker;
use Model\OperationsDb;

/**
 * Class LoadFakeData
 * @package Tests
 */
class LoadFakeData extends OperationsDb
{
    const COUNT_USERS = 10;
    const COUNT_PUBLICATIONS = 15;
    const TAGS = array('boxer', 'bulldog', 'bull terrier',
            'dalmatian', 'doberman', 'poodle', 'disease',
            'advice');

    /**
     * LoadFakeData constructor.
     */
    public function __construct()
    {
        $this->faker = Faker\Factory::create();
        parent::__construct();
    }

    /**
     * @param $object
     */
    public function updateElement(\stdClass $object)
    {
        // TODO: Implement updateElement() method.
    }

    /**
     * @return void
     */
    public function clearTablesFromData()
    {

        //$stmt = $this->db->query("TRUNCATE TABLE roles");
        //return $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt = $this->db->prepare('SET FOREIGN_KEY_CHECKS = 0');
        $stmt->execute();

        $stmt = $this->db->prepare("TRUNCATE TABLE users");
        $stmt->execute();
        $stmt = $this->db->prepare("TRUNCATE TABLE roles");
        $stmt->execute();
        $stmt = $this->db->prepare("TRUNCATE TABLE comments");
        $stmt->execute();
        $stmt = $this->db->prepare("TRUNCATE TABLE publications");
        $stmt->execute();
        $stmt = $this->db->prepare("TRUNCATE TABLE tags");
        $stmt->execute();
        $stmt = $this->db->prepare("TRUNCATE TABLE tags_publications");
        $stmt->execute();
        $stmt = $this->db->prepare("TRUNCATE TABLE publications_users");
        $stmt->execute();
        $stmt = $this->db->prepare("TRUNCATE TABLE contacts");
        $stmt->execute();
        $stmt = $this->db->prepare('SET FOREIGN_KEY_CHECKS = 1');
        $stmt->execute();
    }

    /**
     *  @return void
     */
    public function insertFakeUsers()
    {
        //insert users
        for ($i = 0; $i < self::COUNT_USERS; $i++) {
            $stmt = $this->db->prepare("INSERT INTO users (login, password, token, user_agent,
                first_name, last_name, role_id, dt_of_regist) VALUES(?,?,?,?,?,?,?,?);");

            $stmt->execute(array($this->faker->userName, $this->faker->password,
                $this->faker->uuid, $this->faker->userAgent, $this->faker->firstName,
                $this->faker->lastName, random_int(1, 3), $this->faker->dateTimeBetween('-1 years', 'now')->format('Y-m-d')));
        }
    }

    /**
     * @return void
     */
    public function insertTags()
    {
        //insert tags

        for ($i = 0; $i < count(self::TAGS); $i++) {
            $stmt = $this->db->prepare("INSERT INTO tags (name) VALUES(?);");
            $stmt->execute(array(self::TAGS[$i]));
        }
    }

    /**
     * @return void
     */
    public function insertPublications()
    {
//    name
        //full_text
        //image
        //dt_of_pub
        for ($i = 1; $i <= self::COUNT_PUBLICATIONS; $i++) {
            $stmt = $this->db->prepare("INSERT INTO publications (name, full_text, image, dt_of_pub) VALUES(?,?,?,?);");

            $stmt->execute(array($this->faker->sentence, $this->faker->realText(rand(50, 800)),
                $this->faker->imageUrl($width = 340, $height = 280, 'animals'),
                $this->faker->dateTimeBetween('-1 years', 'now')->format('Y-m-d')));

            //втавляємо значення в звязуючу таблицю між публікацією і юзерами які є її авторами/автором
            $this->insertPublicationsUsers($i, rand(1, self::COUNT_USERS));
            //$this->insertPublicationsUsers($this->db->lastInsertId(), rand(1, self::COUNT_USERS));
            //return $this->db->lastInsertId();

            //втавляємо значення в звязуючу таблицю між публікацією і тегами яким вона відповідає
            $this->insertPublicationsTags($i, rand(1, count(self::TAGS)));
            //$this->insertPublicationsTags($this->db->lastInsertId(), rand(1, count(self::TAGS)));
        }
    }

    // publications_users
    // user_id
    // publication_id

    /**
     * @param int $idPublications
     * @param int $countAuthors
     * @return void
     */
    public function insertPublicationsUsers($idPublications, $countAuthors)
    {
        //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        //НАДА РОЗІБРАТИСЬ ЯК ДОБАЛЯТЬ ДАНІ В ЗВЯЗУЮЧІ ТАБЛИЦІ ЯКШО ПО МАЙСКУЕЛЮ ВКАЗАНІ КЛЮЧІ (ТОМУ ПОКИЩО МИ КЛЮЧІ ВІДКЛЮЧАЄМ
        // ПЕРЕД ВСТАВКОЮ, А ТОДІ ЗНОВ ВКЛЮЧАЄМ)
        // А ТАКОЖ В ДАНОМУ ВИПАДКУ В ОДНОЇ СТАТТІ МОЖИТЬ БУТЬ ДЕКІЛЬКА АВТОРІВ І НАВПАКИ(МНОГІ ДО МНОГИХ). НАДА
        // КОРЕКТОНО ВСАТВКУ ПРИДУМАТЬ В ЗВЯЗУЮЧУ ТАБЛИЦЮ
        
//        $stmt = $this->db->prepare('SET FOREIGN_KEY_CHECKS = 0');
//        $stmt->execute();
        for ($i = 1; $i <= $countAuthors; $i++) {
            $stmt = $this->db->prepare("INSERT INTO publications_users (user_id, publication_id) VALUES(?,?);");
            //$stmt->execute(array($i+1, rand(1, self::COUNT_USERS)));
            $stmt->execute(array($i, $idPublications));
        }

//        $stmt = $this->db->prepare('SET FOREIGN_KEY_CHECKS = 1');
//        $stmt->execute();
    }

    /**
     * @param int $idPublications
     * @param int $countTags
     * @return void
     */
    public function insertPublicationsTags($idPublications, $countTags)
    {
        for ($i = 1; $i <= $countTags; $i++) {
            $stmt = $this->db->prepare("INSERT INTO tags_publications (publication_id, tag_id) VALUES(?,?);");
            $stmt->execute(array($idPublications, $i));
        }
    }

    /**
     * @return void
     */
    public function insertContacts()
    {
        $stmt = $this->db->prepare("INSERT INTO contacts (tel, email, address, description) VALUES(?,?,?,?);");

        $stmt->execute(array($this->faker->phoneNumber, $this->faker->email, $this->faker->address,
                $this->faker->realText(rand(70, 100))));
    }

    /**
     * @return void
     */
    public function testLoadData()
    {
        $this->clearTablesFromData();
        // insert in roles
        $stmt = $this->db->prepare("INSERT INTO roles(name) VALUES('admin');");
        $stmt->execute();
        $stmt = $this->db->prepare("INSERT INTO roles(name) VALUES('user');");
        $stmt->execute();
        $stmt = $this->db->prepare("INSERT INTO roles(name) VALUES('authors');");
        $stmt->execute();

        $this->insertFakeUsers();
        $this->insertTags();
        $this->insertPublications();
        $this->insertContacts();
        //$this->insertPublicationsUsers();
    }
}
