<?php
/**
 * Created by PhpStorm.
 * User: nima
 * Date: 06.03.19
 * Time: 15:13
 */

namespace Config;


class DbParametersConfig
{
//    public const HOST;
//    public const DB_NAME;
//    public const USER ;
//    public const PASSWORD ;

    public $parameters;
//    public $host;
//    public $dbName;
//    public $user;
//    public $password;
    //public $db;

    function __construct()
    {
        $this->parameters = file('./config/db_info.txt', FILE_IGNORE_NEW_LINES);
        $this->host = $this->parameters[0];
        $this->dbName = $this->parameters[1];
        $this->user = $this->parameters[2];
        $this->password = $this->parameters[3];
    }
}