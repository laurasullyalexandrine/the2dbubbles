<?php

namespace App\Controller;

class CoreController
{

    protected function show($viewName, $viewVars = [])
    {

        // Charge le chemin absolu vers le dossier front
        $loader = new \Twig\Loader\FilesystemLoader(dirname(__DIR__) . '/../templates/');

        // Crée l'environement des modèles charger avec ceux dans le dossier front
        $twig = new \Twig\Environment($loader);

        // Dynamise l'affichage des modèles
        $template = $twig->load($viewName . '.html.twig');
   
        // Affiche les modèles
        require_once dirname(__DIR__) . '/../templates/_partial/_nav.html.twig';
        echo $template->render($viewVars);
        require_once dirname(__DIR__) . '/../templates/_partial/_footer.html.twig';
    }
}
