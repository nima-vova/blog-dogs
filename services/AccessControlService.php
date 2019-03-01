<?php
/**
 * Created by PhpStorm.
 * User: nima
 * Date: 05.05.18
 * Time: 16:19
 */

namespace Services;

use Model\UserSelect;

/**
 * Class AccessControlService
 * @package Services
 */
class AccessControlService
{
    /**
     * AccessControlService constructor.
     */
    public function __construct()
    {
        if (isset($_COOKIE['token'])) {
            $this->token = $_COOKIE['token'];
        } else {
            $this->token = null;
        }

        $this->userAgent = $_SERVER['HTTP_USER_AGENT'];
    }

    /**
     * @return bool|mixed|UserSelect
     */
    public function checkToken()
    {
        if (isset($this->token)) {
            $user = new UserSelect();
            $user = $user->getUserByToken($this->token);
            //var_dump($user);
            //не видалять це потрібно!!!!!!!!!!!!!!!!!!!!!!!!!1
            //if(!empty($user)&& ($user[0]['user_agent']==$this->userAgent)){
            if (!empty($user)) {
                return $user;
            }
        }
        return false;
    }

    /**
     * @param $login
     * @param $password
     * @return bool|mixed
     */
    public function checkUser($login, $password)
    {
        //   echo 'good';
        $user = new UserSelect();
        $getUser = $user->getUserByLogin($login);
        var_dump($getUser);
        if (!empty($getUser) && ($getUser->password == $password)) {
            $token = 'value20180212';
            setcookie('token', $token);
            //var_dump($getUser);
            $user->alterTokenForUser($login, $token, $this->userAgent);
            return $getUser;
        }
        return false;
    }
}
