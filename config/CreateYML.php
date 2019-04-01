<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 23.03.19
 * Time: 19:06
 */
//require "../vendor/symfony/yaml/Yaml.php";
namespace Config;
require '../vendor/autoload.php';
use Symfony\Component\Yaml\Yaml;

class CreateYML
{

    public function __construct()
    {
        $dbParameters = file('db_info.txt', FILE_IGNORE_NEW_LINES);
//echo $dbParameters[0];
        $host = $dbParameters[0];
        $dbName = $dbParameters[1];
        $user = $dbParameters[2];
        $password = $dbParameters[3];
//paths:
//migrations: '%%PHINX_CONFIG_DIR%%/db/migrations'
//    seeds: '%%PHINX_CONFIG_DIR%%/db/seeds'
//
//environments:
//    default_migration_table: phinxlog
//    default_database: development
//    production:
//        adapter:
//        host:
//        name:
//        user:
//        pass: ''
//        port: 3306
//        charset: utf8
//version_order: creation
        $addr = array(
            "paths" => array(
                "migrations" => "%%PHINX_CONFIG_DIR%%/db/migrations",
                "seeds" => "%%PHINX_CONFIG_DIR%%/db/seeds"),
            "environments" => array(
                "default_migration_table" => "phinxlog",
                "default_database" => "development",
                "production" => array(
                    "adapter" => "mysql",
                    "host" => $host,
                    "name" => $dbName,
                    "user" => $user,
                    "pass" => $password,
                    "port" => "3306",
                    "charset" => "utf8"),
            ),
             "version_order" => "creation");
       $yaml = Yaml::dump($addr);
       file_put_contents('../phinx.yml', $yaml);
    }
}

new CreateYML();