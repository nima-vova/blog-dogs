<?php
/**
 * Created by PhpStorm.
 * User: nima
 * Date: 18.03.19
 * Time: 14:32
 */
$dbParameters = file('db_info.txt', FILE_IGNORE_NEW_LINES);
//echo $dbParameters[0];
$host = $dbParameters[0];
$dbName = $dbParameters[1];
$user = $dbParameters[2];
$password = $dbParameters[3];
try {
    $conn = new PDO("mysql:host=$host", $user, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "CREATE DATABASE $dbName";
    // use exec() because no results are returned
    $conn->exec($sql);
    //echo "Database created successfully<br>";
} catch (PDOException $e) {
    echo $e->getMessage();
}
$conn = null;


//creating file yml for use phinx migration db
$addr = array(
    "paths" => array(
        "migrations" => "%%PHINX_CONFIG_DIR%%/db/migrations",
        "seeds" =>"%%PHINX_CONFIG_DIR%%/db/seeds"),
    "environments" => array(
        "default_migration_table" => "phinxlog",
        "default_database" => "development",
        "production" => array(
            "adapter" => "",
            "host" => "",
            "name" => "",
            "user" => "",
            "pass" => "",
            "port" => "3306",
            "charset" => "utf8",
        ),
    ),
    "version_order" => "creation",
);

