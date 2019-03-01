<?php

/**
 * Created by PhpStorm.
 * User: nima
 * Date: 14.04.18
 * Time: 11:29
 */


namespace Config;

use Controllers\Controllers;
use Controllers\AdminControllers;

/**
 * Class OldRouting
 * @package Config
 */
class OldRouting
{
    public function __construct()
    {
        $this->controllers = new Controllers();
        $this->adminControllers = new AdminControllers();
    }

    
    
    
    public function analysingUrl()
    {
        if (php_sapi_name() == "cli") {
            // In cli-mode
            //$this->controllers->indexAction();
            //$this->controllers->addRollesAction();
            $this->adminControllers->adminIndexAction();
        } else {      
            // Not in cli-mode
            $requestURL = $_SERVER['REQUEST_URI'];



//           if (isset($_POST['name'])){
//                echo $_POST['name'];
//           }



            if ($requestURL == '/') {
                $this->controllers->indexAction();
            } elseif ($requestURL == '/news') {
                $this->controllers->newsAction();
            } elseif ($requestURL == '/contacts') {
                $this->controllers->contactsAction();
            } elseif ($requestURL == '/actual') {
                $this->controllers->actualAction();
            } elseif ($requestURL == '/load-fake-data') {
                $this->controllers->loadFakeDataAction();

            //admin-routing
            } elseif ($requestURL == '/add-token') {
                $this->adminControllers->adminTestAddTokenAction();
            } elseif ($requestURL == '/token-delete') {
                $this->adminControllers->adminTestDeleteTokenAction();
            } elseif ($requestURL == '/token-show') {
                $this->adminControllers-> adminTestShowTokenAction();
            } elseif ($requestURL == '/admin') {
                $this->adminControllers->indexAction();
            } elseif ($requestURL == '/login-in') {
                $this->adminControllers->adminLoginInAction();
            } elseif ($requestURL == '/admin/add-roles') {
                $this->adminControllers->adminAddRolesAction();
            } elseif ($requestURL == '/admin/users-show') {
                //якщо в запиті була змінна із значенням "ajax" значить це був запит відправлений ajax-ом,
                //і тоді ми визиваєм контролер вказавши йому параметр що це аякс,
                //якщо не ма змінної з значенням аякс то це звичайний запит (перезавантаження сторінки,
                //або по ссилці відкривається сторінка, тобто повністю заватаження або перезавантаження)
                //значить контролер визиваєм із пустим параметром - тобто  що це звичайний запит на завантаження всієї сторінки
                if (isset($_POST['users-show']) && $_POST['users-show']=="ajax") {
                    $this->adminControllers->showUsersAction($_POST['users-show']);
                } else {
                    $this->adminControllers->showUsersAction('null');
                }
                // ...-edit такі запити будуть в нас через аякс ідти (відправляються дані для збереження в базу даних після редагування
                //елемента в формі)
            } elseif ($requestURL == '/admin/users-edit') {
                //echo json_encode($_POST['data-update']);

                //header('Content-type: application/json');
                //echo json_encode("go");

                if (isset($_POST) && (!empty($_POST))) {
                    //print_r($_POST);
                    $this->adminControllers->adminUsersEditAction($_POST);
                }
            }
            // якщо була змінна в запиті id і не пуста, то це аяксом нам пришов id для user
            // якого будем видалять з БД, визиваєм відповідний контролер і ввідаєм id
            elseif ($requestURL == '/admin/users-delete') {
                if (isset($_POST['id']) && (!empty($_POST['id']))) {
                    $this->adminControllers->adminUsersDeleteAction($_POST['id']);
                }
            } elseif ($requestURL == '/admin/publications-show') {
                //якщо в запиті була змінна із значенням "ajax" значить це був запит відправлений ajax-ом,
                //і тоді ми визиваєм контролер вказавши йому параметр що це аякс,
                //якщо не ма змінної з значенням аякс то це звичайний запит (перезавантаження сторінки,
                //або по ссилці відкривається сторінка, тобто повністю заватаження або перезавантаження)
                //значить контролер визиваєм із пустим параметром - тобто  що це звичайний запит на завантаження всієї сторінки
                if (isset($_POST['users-show']) && $_POST['users-show']=="ajax") {
                    $this->adminControllers->adminPublicationsShowAction($_POST['users-show']);
                } else {
                    $this->adminControllers->adminPublicationsShowAction('null');
                }
            } elseif ($requestURL == '/admin/tags-show') {
                //якщо в запиті була змінна із значенням "ajax" значить це був запит відправлений ajax-ом,
                //і тоді ми визиваєм контролер вказавши йому параметр що це аякс,
                //якщо не ма змінної з значенням аякс то це звичайний запит (перезавантаження сторінки,
                //або по ссилці відкривається сторінка, тобто повністю заватаження або перезавантаження)
                //значить контролер визиваєм із пустим параметром - тобто  що це звичайний запит на завантаження всієї сторінки
                if (isset($_POST['users-show']) && $_POST['users-show']=="ajax") {
                    $this->adminControllers->adminTagsShowAction($_POST['users-show']);
                } else {
                    $this->adminControllers->adminTagsShowAction('null');
                }
            } else {
                // 404 stuff
                $this->controllers->notFoundAction();
            }
        }
    }
}
