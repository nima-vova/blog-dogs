<?php
/**
 * Created by PhpStorm.
 * User: nima
 * Date: 17.04.18
 * Time: 20:23
 */

namespace Controllers;

use Twig_Loader_Filesystem;
use Twig_Environment;

/**
 * @property Twig_Loader_Filesystem loaderAdmin
 * @property Twig_Environment twigAdmin
 */
class AdminControllers extends AbstractControllers
{
    /**
     * AdminControllers constructor.
     */
    public function __construct()
    {
        parent::__construct();
        // створуєм twigAdmin (для адмінки інший шлях для збереження шаблонів), бо під назвою
        // twig будуть визиватись Action з базового контролера які виводять сторінки з помилками)
        // Тому щоб не переоприділить twig предка ми створємо під новоою назвою twigAdmin
        $this->loaderAdmin = new Twig_Loader_Filesystem('./templates/admin');
        // Instantiate our Twig
        $this->twigAdmin = new Twig_Environment($this->loaderAdmin);

        $this->testValue = 20;
    }
    
    /**
     * @return void|resource
     */
    public function indexAction()
    {
        // перевіряєм чи аутентифікований користувач ( за допомогою сервіса де по токену індинтифікуєтся користувач)
        // якщо не прошла аунтифікація (не правильний токен, чи взагалі відсутній), то відправляєм на сторінку авторизації
        if ($this->accsessControl->checkToken()) {
            $user = $this->accsessControl->checkToken();            //var_dump($user);
            echo $this->twigAdmin->render('main-page-admin.html', array('menu' => $user->first_name));
        } else {
            return header('Location: ' .'/login');
        }
    }

    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    //це тестовий контролер

    /**
     * @return void
     */
    public function addTokenAction(): void
    {
        $token = sha1('value');
        setcookie('token', $token);
        echo $this->twigAdmin->render('main-page-admin.html', array('param' => 'set coockie'));
    }

    /**
     * @return void
     */
    public function deleteTokenTestAction(): void
    {
        setcookie('token', "", time() - 3600);
        echo $this->twigAdmin->render('main-page-admin.html', array('param' => 'delete coockie'));
    }

    public function howTokenAction()
    {
        var_dump($_COOKIE['token']);
    }

    /**
     * @return resource|void 
     */
    public function showUsersAction()
    {
        // перевіряєм чи аутентифікований користувач ( за допомогою сервіса де по токену індинтифікуєтся користувач)
        // якщо не прошла аунтифікація (не правильний токен, чи взагалі відсутній), то відправляєм на сторінку авторизації
        if ($this->accsessControl->checkToken()) {
            $user = $this->accsessControl->checkToken();
        } else {
            return header('Location: ' .'/login');
        }

        $allUsers = $this->usersSelect->getUsers();
        if (isset($_POST['object-show']) && $_POST['object-show']=="ajax") {
            ///  $this->adminControllers->adminUsersShowAction($_POST['users-show']);

            //if ($typeRequest == "ajax") {
            header('Content-type: application/json');
            echo json_encode($allUsers);
        } else {
            //echo $allUsers;
            //var_dump($allUsers);
            echo $this->twigAdmin->render('main-page-admin.html', array('param' => json_encode($allUsers), 'menu'=>'users-show'));
            //   echo $this->twigAdmin->render('main-page-admin.html', array('param' => 'test'));
        }
    }

    /**
     * @return resource|void
     */
    public function editUsersAction()
    {
        // перевіряєм чи аутентифікований користувач ( за допомогою сервіса де по токену індинтифікуєтся користувач)
        // якщо не прошла аунтифікація (не правильний токен, чи взагалі відсутній), то відправляєм на сторінку авторизації
        if ($this->accsessControl->checkToken()) {
            $user = $this->accsessControl->checkToken();
        } else {
            return header('Location: ' .'/login');
        }

        //якщо це аякс (тобто був переданий параметр в запиті- обект з полями для зміни в бд),
        // значить відправляємо дані з запиту на оновлення запису в бд,
        if (isset($_POST) && (!empty($_POST))) {
            $jsonString = json_encode($_POST);
            $newObject = json_decode($jsonString);
            $resultUpdate = $this->usersSelect->updateElement($newObject);
            echo $resultUpdate;
        } else {
            // інакше - знач роут без аякса, тому спочатку перевіряєм чи існує в кінці url іd цілочисельний
            // і підходить критеріям (в сервісі urlAnalysis->defineIndexinUrl()).
            // Потім перевіряєм чи є в базі користувач з таким id,
            // якщо все вірно то виводим всих користувачів (все як в роуті users/show)
            // і відправляєм додатково параметр із значенням id користувача для того,
            // щоб js міг цього користувача вивести в полях форми для редагування
            $defineIndexinUrl = $this->urlAnalysis->defineIndexinUrl();
            if ($defineIndexinUrl) {
                $getUserById = $this->usersSelect->getUserById($defineIndexinUrl);
                if ($getUserById) {
                    $allUsers= $this->usersSelect->getUsers();
                    var_dump($getUserById->id);
                    echo $this->twigAdmin
                        ->render('main-page-admin.html', array('param' => json_encode($allUsers),
                            'menu'=>'users-show', 'id' => $getUserById->id, 'action' => 'edit'));
                }
            }
        }
        //echo $newObject->id;
        //echo $this->twigAdmin->render('main-page-admin.html', array('param' => json_encode($allUsers), 'menu'=>'users-show'));
    }

    // видалення робем тільки через ajax
    /**
     * @return void
     */
    public function deleteUsersAction(): void
    {
        //якщо це аякс (тобто був переданий параметр в запиті- обект з полями для зміни в бд),
        // значить відправляємо дані з запиту на видалення запису в бд,
        if (isset($_POST) && (!empty($_POST))) {
            $jsonString = json_encode($_POST);
            $newObject = json_decode($jsonString);
            $resultDelete = $this->usersSelect->deleteUser($newObject->id);
            echo $resultDelete;
        } else {
            $this->notFoundAction();
        }
    }

    /**
     * @return resource|void
     */
    public function showPublicationsAction()
    {
        // перевіряєм чи аутентифікований користувач ( за допомогою сервіса де по токену індинтифікуєтся користувач)
        // якщо не прошла аунтифікація (не правильний токен, чи взагалі відсутній), то відправляєм на сторінку авторизації
        if ($this->accsessControl->checkToken()) {
            $user = $this->accsessControl->checkToken();
        } else {
            return header('Location: ' .'/login');
        }

        //$allPublications = $this->publicationsSelect->showPublications();
        $allPublications = $this->publicationsSelect->getPublications();

        if (isset($_POST['object-show']) && $_POST['object-show']=="ajax") {
            ///  $this->adminControllers->adminUsersShowAction($_POST['users-show']);

            //if ($typeRequest == "ajax") {
            header('Content-type: application/json');
            echo json_encode($allPublications);
        } else {
            //echo $allUsers;
            //var_dump($allUsers);
            echo $this->twigAdmin->render('main-page-admin.html', array('param' => json_encode($allPublications), 'menu'=>'publications-show'));
            //   echo $this->twigAdmin->render('main-page-admin.html', array('param' => 'test'));
        }
    }

    /**
     * @return resource|void
     */
    public function editPublicationsAction()
    {
        // перевіряєм чи аутентифікований користувач ( за допомогою сервіса де по токену індинтифікуєтся користувач)
        // якщо не прошла аунтифікація (не правильний токен, чи взагалі відсутній), то відправляєм на сторінку авторизації
        if ($this->accsessControl->checkToken()) {
            $user = $this->accsessControl->checkToken();
        } else {
            return header('Location: ' .'/login');
        }
        //якщо це аякс (тобто був переданий параметр в запиті- обект з полями для зміни в бд),
        // значить відправляємо дані з запиту на оновлення запису в бд,
        if (isset($_POST) && (!empty($_POST))) {
            $jsonString = json_encode($_POST);
            $newObject = json_decode($jsonString);
            //var_dump($newObject);
            $resultUpdate = $this->publicationsSelect->updateElement($newObject);
            echo $resultUpdate;
        } else {
            // інакше - знач роут без аякса, тому спочатку перевіряєм чи існує в кінці url іd цілочисельний
            // і підходить критеріям (в сервісі urlAnalysis->defineIndexinUrl()).
            // Потім перевіряєм чи є в базі користувач з таким id,
            // якщо все вірно то виводим всих користувачів (все як в роуті users/show)
            // і відправляєм додатково параметр із значенням id користувача для того,
            // щоб js міг цього користувача вивести в полях форми для редагування
            $defineIndexinUrl = $this->urlAnalysis->defineIndexinUrl();
            if ($defineIndexinUrl) {
                $getPublicationById = $this->publicationsSelect->getPublicationById($defineIndexinUrl);
                if ($getPublicationById) {
                    $allPublications= $this->publicationsSelect->getPublications();
                    var_dump($getPublicationById->id);
                    echo $this->twigAdmin
                        ->render('main-page-admin.html', array('param' => json_encode($allPublications),
                            'menu'=>'users-show', 'id' => $getPublicationById->id, 'action' => 'edit'));
                }
            }
        }
        //echo $newObject->id;
        //echo $this->twigAdmin->render('main-page-admin.html', array('param' => json_encode($allUsers), 'menu'=>'users-show'));
    }

    // видалення робем тільки через ajax
    /**
     * @return void
     */
    public function deletePublicationsAction(): void
    {
        //якщо це аякс (тобто був переданий параметр в запиті- обект з полями для зміни в бд),
        // значить відправляємо дані з запиту на видалення запису в бд,
        if (isset($_POST) && (!empty($_POST))) {
            $jsonString = json_encode($_POST);
            $newObject = json_decode($jsonString);
            $resultDelete = $this->publicationsSelect->deletePublication($newObject->id);
            echo $resultDelete;
        } else {
            $this->notFoundAction();
        }
    }

    /**
     * @return resource|void
     */
    public function showTagsAction()
    {
        // перевіряєм чи аутентифікований користувач ( за допомогою сервіса де по токену індинтифікуєтся користувач)
        // якщо не прошла аунтифікація (не правильний токен, чи взагалі відсутній), то відправляєм на сторінку авторизації
        if ($this->accsessControl->checkToken()) {
            $user = $this->accsessControl->checkToken();
        } else {
            return header('Location: ' .'/login');
        }
        //$allTags = $this->tagsSelect->showTags();
        $allTags = $this->tagsSelect->getTags();
        //var_dump($allTags);
        if (isset($_POST['object-show']) && $_POST['object-show']=="ajax") {
            header('Content-type: application/json');
            echo json_encode($allTags);
        } else {
            //echo $allUsers;
            //var_dump($allUsers);
            echo $this->twigAdmin->render('main-page-admin.html', array('param' => json_encode($allTags), 'menu'=>'tags-show'));
            //   echo $this->twigAdmin->render('main-page-admin.html', array('param' => 'test'));
        }
    }

    /**
     * @return resource|void
     */
    public function editTagsAction()
    {
        // перевіряєм чи аутентифікований користувач ( за допомогою сервіса де по токену індинтифікуєтся користувач)
        // якщо не прошла аунтифікація (не правильний токен, чи взагалі відсутній), то відправляєм на сторінку авторизації
        if ($this->accsessControl->checkToken()) {
            $user = $this->accsessControl->checkToken();
        } else {
            return header('Location: ' .'/login');
        }
        //якщо це аякс (тобто був переданий параметр в запиті- обект з полями для зміни в бд),
        // значить відправляємо дані з запиту на оновлення запису в бд,

        if (isset($_POST) && (!empty($_POST))) {
            $jsonString = json_encode($_POST);
            $newObject = json_decode($jsonString);
            //var_dump($newObject);
            $resultUpdate = $this->tagsSelect->updateElement($newObject);
            echo $resultUpdate;
        } else {
            // інакше - знач роут без аякса, тому спочатку перевіряєм чи існує в кінці url іd цілочисельний
            // і підходить критеріям (в сервісі urlAnalysis->defineIndexinUrl()).
            // Потім перевіряєм чи є в базі користувач з таким id,
            // якщо все вірно то виводим всих користувачів (все як в роуті users/show)
            // і відправляєм додатково параметр із значенням id користувача для того,
            // щоб js міг цього користувача вивести в полях форми для редагування
            $defineIndexinUrl = $this->urlAnalysis->defineIndexinUrl();
            if ($defineIndexinUrl) {
                $getTagById = $this->tagsSelect->getTagById($defineIndexinUrl);
                if ($getTagById) {
                    $allTags= $this->tagsSelect->getTags();
                    
                    echo $this->twigAdmin
                        ->render('main-page-admin.html', array('param' => json_encode($allTags),
                            'menu'=>'tags-show', 'id' => $getTagById->id, 'action' => 'edit'));
                }
            }
        }
        //echo $newObject->id;
        //echo $this->twigAdmin->render('main-page-admin.html', array('param' => json_encode($allUsers), 'menu'=>'users-show'));
    }
    
    // видалення робем тільки через ajax
    /**
     * @return void
     */
    public function deleteTagsAction(): void
    {
        //якщо це аякс (тобто був переданий параметр в запиті- обект з полями для зміни в бд),
        // значить відправляємо дані з запиту на видалення запису в бд,
        if (isset($_POST) && (!empty($_POST))) {
            $jsonString = json_encode($_POST);
            $newObject = json_decode($jsonString);
            $resultUpdate = $this->tagsSelect->deleteTag($newObject->id);
            echo $resultUpdate;
        } else {
            $this->notFoundAction();
        }
    }

//    public function AddRolesAction()
//    {
        // покищо заглушка, можливо взагалі це не потрібнл
//        $model = new BaseCRUDModel();
//        $model->nameObject = 'roles';
//        $model->nameValues = 'name';
//        $model->values = ':name';
//        $model->NewParamsObject = array('name' => 'author');
//        $model->cteateObject();
//        echo $this->twigAdmin->render('add-roles.html', array('menu' => 'add-roles',));
//    }
}
