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
        // create twigAdmin (for admin is another way to save templates), because under the name
        // twig will be called Action from the base controller that output the error pages)
        // Therefore, in order not to redefine the twig parent we create under the new name twigAdmin
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
        // interpret user authentications (for the service provided by the token authentication user)
        // allegedly did not pass authentication (not correct token, or absent token), t sends the login page
        if ($this->accsessControl->checkToken()) {
            $user = $this->accsessControl->checkToken();            //var_dump($user);
            echo $this->twigAdmin->render('main-page-admin.html', array('menu' => $user->first_name));
        } else {
            return header('Location: ' .'/login');
        }
    }

    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    //this is a test controller

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
        // check whether the authenticated user (using the service where the user is indexed by this token)
        // if there is no authentication (not a valid token, or not at all), then we send it to the authorization page.
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
        // check whether the authenticated user (using the service where the user is indexed by this token)
        // if there is no authentication (not a valid token, or not at all), then we send it to the authorization page.
        if ($this->accsessControl->checkToken()) {
            $user = $this->accsessControl->checkToken();
        } else {
            return header('Location: ' .'/login');
        }

        // if this is ajax (i.e., the parameter was passed to the query-the object with the fields to change in bd),
        // therefore, we send data from the request to update the record in the bd
        if (isset($_POST) && (!empty($_POST))) {
            $jsonString = json_encode($_POST);
            $newObject = json_decode($jsonString);
            $resultUpdate = $this->usersSelect->updateElement($newObject);
            echo $resultUpdate;
        } else {
            // else - meaning rout without ayax, so first check whether there is an end of url and integer
            // and meets the criteria (in the service urlAnalysis->defineIndexinUrl()).
            // Then check if there is a user with such id,
            // if everything is correct, we output all users (all as in the rows users/show)
            // and send an optional parameter with the user id value to
            // so that js could display this user in the form fields for editing
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

    // delete work only through ajax
    /**
     * @return void
     */
    public function deleteUsersAction(): void
    {
        // if this is ajax (i.e., the parameter was passed to the query-the object with the fields to change in bd),
        // then we send the data on the request to delete the record in the bd
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
        // check whether the authenticated user (using the service where the user is indexed by this token)
        // if there is no authentication (not a valid token, or not at all), then we send it to the authorization page.
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
        // check whether the authenticated user (using the service where the user is indexed by this token)
        // if there is no authentication (not a valid token, or not at all), then we send it to the authorization page.
        if ($this->accsessControl->checkToken()) {
            $user = $this->accsessControl->checkToken();
        } else {
            return header('Location: ' .'/login');
        }
        // if this is ajax (i.e., the parameter was passed to the query-the object with the fields to change in bd),
        // then we send the data on the request to delete the record in the bd
        if (isset($_POST) && (!empty($_POST))) {
            $jsonString = json_encode($_POST);
            $newObject = json_decode($jsonString);
            //var_dump($newObject);
            $resultUpdate = $this->publicationsSelect->updateElement($newObject);
            echo $resultUpdate;
        } else {
            // else - meaning rout without ayax, so first check whether there is an end of url and integer
            // and meets the criteria (in the service urlAnalysis->defineIndexinUrl()).
            // Then check if there is a user with such id,
            // if everything is correct, we output all users (all as in the rows users/show)
            // and send an optional parameter with the user id value to
            // so that js could display this user in the form fields for editing
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

    // delete work only through ajax
    /**
     * @return void
     */
    public function deletePublicationsAction(): void
    {
        // if this is ajax (i.e., the parameter was passed to the query-the object with the fields to change in bd),
       // then we send the data on the request to delete the record in the bd
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
        // check whether the authenticated user (using the service where the user is indexed by this token)
        // if there is no authentication (not a valid token, or not at all), then we send it to the authorization page.
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
        // check whether the authenticated user (using the service where the user is indexed by this token)
        // if there is no authentication (not a valid token, or not at all), then we send it to the authorization page.
        if ($this->accsessControl->checkToken()) {
            $user = $this->accsessControl->checkToken();
        } else {
            return header('Location: ' .'/login');
        }
        // if this is ajax (i.e., the parameter was passed to the query-the object with the fields to change in bd),
        // then we send the data on the request to delete the record in the bd
        if (isset($_POST) && (!empty($_POST))) {
            $jsonString = json_encode($_POST);
            $newObject = json_decode($jsonString);
            //var_dump($newObject);
            $resultUpdate = $this->tagsSelect->updateElement($newObject);
            echo $resultUpdate;
        } else {
            // else - meaning rout without ayax, so first check whether there is an end of url and integer
            // and meets the criteria (in the service urlAnalysis->defineIndexinUrl()).
            // Then check if there is a user with such id,
            // if everything is correct, we output all users (all as in the rows users/show)
            // and send an optional parameter with the user id value to
            // so that js could display this user in the form fields for editing
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
    
    // delete work only through ajax
    /**
     * @return void
     */
    public function deleteTagsAction(): void
    {
        // if this is ajax (i.e., the parameter was passed to the query-the object with the fields to change in bd),
        // then we send the data on the request to delete the record in the bd
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
