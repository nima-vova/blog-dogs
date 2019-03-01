<?php

require_once 'vendor/autoload.php';
use Config\Routing;
use Config\OldRouting;

$test = new Routing();
$test->getController();


//$test = new OldRouting();
//$test->analysingUrl();
