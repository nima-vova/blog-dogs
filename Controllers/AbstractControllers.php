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
use Services\{AccessControlService, UrlAnalysis};
use Model\{UserSelect, PublicationSelect, TagSelect};

/**
 * Class BaseControllers
 * @package Controllers
 */
abstract class AbstractControllers
{
    /**
     * BaseControllers constructor.
     */
    public function __construct()
    {
        $this->accsessControl = new AccessControlService();
        $this->usersSelect = new UserSelect();
        $this->publicationsSelect = new PublicationSelect();
        $this->tagsSelect = new TagSelect();

        // Specify our Twig templates location
        $this->loader = new Twig_Loader_Filesystem('./templates/main-page');
        // Instantiate our Twig
        $this->twig = new Twig_Environment($this->loader);
        //будем використовувати в delete або edit останню частину роута (там повинен бути індекс для редагуання або видалення)
        $this->urlAnalysis = new UrlAnalysis();
        
        $this->testValue = 10;
    }

    /**
    * @return void
    */
    public function loginAction(): void
    {
        //var_dump($_COOKIE['token']);

        if (isset($_POST['goCheck'])) {
            $login = $_POST['login'];
            $password = $_POST['password'];

            $user = $this->accsessControl->checkUser($login, $password);

            if ($user) {
                var_dump($password);
                header('Location: ' .'/admin');
            }
            //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
            //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
            //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
            //створюемо тестового користувача для входу в  "petro" адмінку
            if ($login == "petro") {
                $token = sha1('test');
                setcookie('token', $token);
                header('Location: ' .'/admin');
            }
        }
//        else{
//            $param = 'not login';
//        }
        echo $this->twig->render('login-page.html', array('param' => 'not login'));
    }

    /**
    * @return void
    */
    public function notFoundAction(): void
    {
        echo $this->twig->render('404.html');
    }
}
