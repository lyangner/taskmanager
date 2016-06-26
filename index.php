<?php

require_once 'config/params.php';
require_once HOME_DIR.'src/vendor/twig/lib/Twig/Autoloader.php';
require_once 'lib/MyDb.php';
require_once 'lib/Repository.php';
require_once 'lib/Router.php';

Twig_Autoloader::register();
$loader = new Twig_Loader_Filesystem(HOME_DIR . 'views');    //Папка с шаблонами
$twig = new Twig_Environment($loader, [
    'cache' => HOME_DIR . 'cache/views/', // Папка для кэша
]);

$db = new MyDB(DB_HOST, DB_NAME, DB_USER, DB_PASS);

$entityManager = new Repository($db);

$router = new Router($db, $twig, HOME_URL, HOME_DIR, $entityManager, $_GET, $_POST);

echo $router->execute();