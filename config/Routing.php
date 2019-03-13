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
// нада не забути унеможливить в назвах url великі букви (бо щас считується що Users шо users,
// а потрібно щоб було тільки user)
class Routing
{
    /**
     * Routing constructor.
     */
    public function __construct()
    {
        $this->controllers = new Controllers();
        $this->adminControllers = new AdminControllers();
        // Разділяємо наш запит на частинки
        $this->routs = explode('/', $_SERVER['REQUEST_URI']);
        //var_dump($this->routs);
        //var_dump(get_class_methods(new AdminControllers()));
    }

    // по першій частині роута (тобто розділення по "/") визначаємо якого типу нам контролери шукать
    // (якщо це admin- то шукаємо контролер (за допомогою getNameController) в AdminControllers,
    // якщо пусто це головна сторіка і ми визиваємо Controllers->indexAction),
    // якщо інще (default) то шукаємо в Сontrollers (за допомогою getNameController)
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
                // якщо в роуті з адмінкою є ще елементи то шукаємо який action,
                // якщо ні то це значить, що це головна сторінка адмінки indexAction()                
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
//             //так як роут той що не адмінка буде на один елемент масиву менший, то ми йому
                // в початок масива добавляємо пустий елемент перед тим як передавать його в getNameController
                // (тому що, getNameController розбірає масив роута з 2 і 3 індекса,  а в звичайному роуті той що не адмінка
                // 2 індекс останній, так ми уніфіціруєм масив для getNameController)
                array_unshift($routs, "");
                $nameAction = $this->getNameController($routs, $this->controllers);
                $this->controllers->$nameAction();
        }
    }

    //по часткам роута (тобто розділення по "/") визначаємо контролер який обробляє цей роут
    // Вертаємо контролер або просто false, якщо контролера не знайдено
    /**
     * @param $names
     * @param $controller
     * @return string
     */
    public function getNameController(array $names, AbstractControllers $controller): string
    {
        //розбиваєм всі методи контролера на масив (елементи якого назви методів контролера)
        $methods = get_class_methods($controller);
        //var_dump($methods);
        if (isset($names[2])&& isset($names[3])) {
            // перетворємо першу букву в заглавну
            $names[2] = ucwords($names[2]);
            //$names[3] = ucwords($names[3]);
            for ($i = 0; $i<count($methods); $i++) {
                // розбиваєм назву метода контролера на слова по Заглавній букві
                $subMethods = preg_split('/(?=[A-Z])/', $methods[$i]);
                // var_dump($subMethods);
                // це масив який міститиме назву сучності і дію контролера (два елементи)
                $returnSubMethods = [];
                for ($j = 0; $j<count($subMethods); $j++) {
                    // якщо в назві метода є перша частина з роута, то записуєм в
                    // в масив $returnSubMethods[0] імя частини метода (це буде дія яку робить контролер)
                    if (strcmp($subMethods[$j], $names[2]) == 0) {
                        $returnSubMethods[0] = $subMethods[$j];
                        //continue 1;
                        continue;
                        //break 2;
                    }
                    // якщо в назві метода є друга частина з роута, то записуєм в
                    // в масив $returnSubMethods[1] імя частини метода (це буде імя сучності в контролері)
                    if (strcmp($subMethods[$j], $names[3]) == 0) {
                        $returnSubMethods[1] = $subMethods[$j];
                    }
                }
                //якщо після перебору частин імені метода контролера ми знайшли всі 2
                //  то вертаємо результуючий масив і виходиим з цикла
                if (count($returnSubMethods) == 2) {
                    // var_dump($returnSubMethods);
                    // var_dump(count($returnSubMethods));
                    //   return  var_dump($methods[$i]);
                    return $methods[$i];
                }
            }
            // якщо назва контролера не знайдена, значить виртаєм назву контролера який виводить помилку
            return 'notFoundAction';
        }
        return 'notFoundAction';
    }
}
