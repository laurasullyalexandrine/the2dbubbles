<?php
namespace App;
 
use \Twig\Environment;
use \Twig\Loader\FilesystemLoader;

require dirname(__DIR__). '/vendor/autoload.php';

// Routing
$page = 'home';

if (isset($_GET['p'])) {
    $page = $_GET['p'];
}

// Render of template
$loader = new FilesystemLoader(dirname(__DIR__). '/templates');

// allows to create a cache set it to false, deactivate it
$twig = new Environment($loader, [
  'cache' => false, // __DIR__ . '/tmp'
]);

switch ($page) {
    case 'contact':
        echo $twig->render('/front/contact.html.twig');
        break;
    case 'home':
        echo $twig->render('/front/home.html.twig');
        break;
    default:
        header('HTTP/1.0 404 Not Found');
        echo $twig->render('/front/404.html.twig');
        break;
}