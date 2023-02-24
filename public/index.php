<?php

namespace App;
 
use AltoRouter;
use \Twig\Environment;
use \Twig\Loader\FilesystemLoader;
use App\Controller\ErrorController;
use Exception;

require dirname(__DIR__). '/vendor/autoload.php';

//AltoRouter is a small but powerful routing class,
$router = new AltoRouter();

// Routing
/**
 * Exemples de routes :
 * .../blog/list => BlogController->list()
 * .../blog/view/id/5 => BlogController->view(['id' => 5])
 * Premier mot serait le contrôleur deuxième mot action (méthode) 
 * tous les couples de mots suivants seront des paramètres à transmettre à l'action
 * ou
 * .../blog/view/5 => BlogController->view(5)
 * .../blog/category/livre-d-or/laura => BlogController->category('livre-d-or', 'laura')
 */
$url = $_SERVER['REQUEST_URI'];

$url = trim($url, '/');
$params = explode('/', $url); // Permet de récupérer l'url sous la forme d'un tableau
$controllerName = array_shift($params); // Permet de stocker le premier élément du tableau qui le contrôleur 
$method = array_shift($params); // Permet de stocker le premier élément du tableau qui la méthode du contrôleur 
// $controller->$action(...$params);

// 1 - Reconstruire le nom de la classe de contrôleur, vérifier si il existe, instancier
    // post->PostController
    // Reconstruction de nom de la classe 
    $controllerName = 'App\Controller\\'. ucfirst($controllerName).'Controller';
    // TODO: ajout le try catch
    if (class_exists($controllerName)) {
        // Instancier bon contrôleur
        $controller = new $controllerName();
    } else {
        $errorController = new Controller\ErrorController();
        $errorController->pageNotFoundAction();
    }
     
// 2 - Vérifier que le contrôleur possède la méthode $method
    if(method_exists($controller, $method)) {
        // Cherche la méthode ainsi que ses paramètres optionnels
        $controller->$method(...$params);
    } else {
        $errorController = new Controller\ErrorController();
        $errorController->pageNotFoundAction();
    }
    
// 3 - Lancer la méthode en lui passant les paramètres optionnels (ref:2)