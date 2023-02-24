<?php

namespace App;
 
use AltoRouter;
use \Twig\Environment;
use \Twig\Loader\FilesystemLoader;

require dirname(__DIR__). '/vendor/autoload.php';

//AltoRouter is a small but powerful routing class,
$router = new AltoRouter();

$url = $_SERVER['REQUEST_URI'];

$url = trim($url, '/');
$params = explode('/', $url); // Permet de récupérer l'url sous la forme d'un tableau
$controller = array_shift($params); // Permet de stocker le premier élément du tableau qui le controlleur 
$method = array_shift($params); // Permet de stocker le premier élément du tableau qui la méthode du controlleur 

// Permet de mapper tous les controlleurs
$router->map(
    'GET',
    $url, [
        'controller' => $controller,
        'method' => $method
    ],
    'main-home'
);


dd($router);
// Routing
/**
 * Exemples de routes :
 * .../blog/list => BlogController->list()
 * .../blog/view/id/5 => BlogController->view(['id' => 5])
 * Premier mot serait le controlleur deuxième mot action (méthode) 
 * tous les couples de mots suivants seront des paramètres à transmettre à l'action
 * ou
 * .../blog/view/5 => BlogController->view(5)
 * .../blog/category/livre-d-or/laura => BlogController->category('livre-d-or', 'laura')
 */

$url = $_SERVER['REQUEST_URI'];
dd($url);
$url = trim($url, '/');
$params = explode('/', $url); // Permet de récupérer l'url sous la forme d'un tableau
$controller = array_shift($params); // Permet de stocker le premier élément du tableau qui le controlleur 
$action = array_shift($params); // Permet de stocker le premier élément du tableau qui la méthode du controlleur 

// $controller->$action(...$params);
// dd($url, $controller, $action, ...$params); // Les 3 petits points permet de récupérer chaque nouveau paramètre en tant que clé


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