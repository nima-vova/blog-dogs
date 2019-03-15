<?php

/**
 * Created by PhpStorm.
 * User: nima
 * Date: 14.04.18
 * Time: 11:29
 */
namespace Config;

use Controllers\AbstractControllers;
use Controllers\Controllers;
use Controllers\AdminControllers;

// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// do not forget to disable the uppercase letters in the url names (because now reads that Users are users,
// you only need to be a user)
class Routing
{
    /**
     * Routing constructor.
     */
    public function __construct()
    {
        $this->controllers = new Controllers();
        $this->adminControllers = new AdminControllers();
        // divide the request for particle
        $this->routs = explode('/', $_SERVER['REQUEST_URI']);
        //var_dump($this->routs);
        //var_dump(get_class_methods(new AdminControllers()));
    }

//     on the first part of the route (search on "/") by the type of controllers
//      (as admin, the controller's post (after getNameController) in AdminControllers,
//      as empty as the headboard with the Controllers-> indexAction),
//     else (default) then search in Controllers (for getNameController)
    /**
     * @return void
     */
    public function getController(): void
    {
        $routs = $this->routs;
        switch ($routs[1]) {
            case "":
                //var_dump('index');
                $this->controllers->indexAction();
                break;
            case "show":
                
                break;
            case "admin":
                // if in the routine with the admin there are still elements then we are looking for what action,
                // if it does not mean that this is the main page of the indexAction () admin
                if (isset($routs[2])) {
                    $nameAction = $this->getNameController($routs, $this->adminControllers);
                    $this->adminControllers->$nameAction();
                } else {
                    $this->adminControllers->indexAction();
                }
                break;
            case "login":
                if (!isset($routs[2])) {
                    $this->controllers->loginAction();
                } else {
                    $this->controllers->notFoundAction();
                }
                break;
            default:
                // as the rout of the one that does not admink will be on one element of the array smaller, then we are to him
                // at the beginning of the array we add an empty element before passing it to the getNameController
                // (because getNameController crawls the rows with 2 and 3 indexes, and in the usual rout one that does not admink
                // 2 is the last index, so we unify the array for getNameController)
                array_unshift($routs, "");
                $nameAction = $this->getNameController($routs, $this->controllers);
                $this->controllers->$nameAction();
        }
    }
//     by rotact particle (division by "/") determine the controller that handles this rout
//     Return the controller or just false if the controller is not found
    /**
     * @param $names
     * @param $controller
     * @return string
     */
    public function getNameController(array $names, AbstractControllers $controller): string
    {
        // split all methods of the controller into an array (elements of which are the names of controller methods)
        $methods = get_class_methods($controller);
        //var_dump($methods);
        if (isset($names[2])&& isset($names[3])) {
            // we convert the first letter to the capital
            $names[2] = ucwords($names[2]);
            //$names[3] = ucwords($names[3]);
            for ($i = 0; $i<count($methods); $i++) {
                // split the name of the controller method into words by the capital letter
                $subMethods = preg_split('/(?=[A-Z])/', $methods[$i]);
                // var_dump($subMethods);
                // is an array containing the name of the entity and the controller's action (two elements)
                $returnSubMethods = [];
                for ($j = 0; $j<count($subMethods); $j++) {
                    // if in the name of the method there is the first part of the rout, then write in
                    // in the $returnSubMethods[0] array is the name of the part of the method (this will be the action the controller does)
                    if (strcmp($subMethods[$j], $names[2]) == 0) {
                        $returnSubMethods[0] = $subMethods[$j];
                        //continue 1;
                        continue;
                        //break 2;
                    }
                    // if the method name has the second part of the rout, then write to
                    // in the $returnSubMethods[1] array the name of the part of the method (this will be the name of the entity in the controller)
                    if (strcmp($subMethods[$j], $names[3]) == 0) {
                        $returnSubMethods[1] = $subMethods[$j];
                    }
                }
                // if after having searched parts of the name of the method of the controller, we found all 2
                // then return the resulting array and exit from the cycle
                if (count($returnSubMethods) == 2) {
                    // var_dump($returnSubMethods);
                    // var_dump(count($returnSubMethods));
                    //   return  var_dump($methods[$i]);
                    return $methods[$i];
                }
            }
            // if the name of the controller is not found, then the name of the controller is displayed, which displays an error
            return 'notFoundAction';
        }
        return 'notFoundAction';
    }
}
