<?php

require 'vendor/autoload.php';

// Routing
$page = 'home';
if (isset($_GET['p'])) {
    $page = $_GET['p'];
}

// Render of template
$loader = new \Twig_loader_Filesystem(__DIR__ . '/templates');
dd($loader);
$twig = new \Twig_Environnement($loader, [
  'cache' => __DIR__ . '/tmp'
]);


if ($page === 'home') {
    echo $twig->render('home.html.twig');
}