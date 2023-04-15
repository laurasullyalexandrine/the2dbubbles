<?php

declare(strict_types=1);

namespace App\Controller;

use Twig\Extension\DebugExtension;
use Twig\TwigFunction;

class CoreController
{

    protected function show($viewName, $viewVars = [])
    {
        // Charge le chemin absolu vers le dossier front
        $loader = new \Twig\Loader\FilesystemLoader(dirname(__DIR__) . '/../templates/');

        // Crée l'environement des modèles charger avec ceux dans le dossier front
        $twig = new \Twig\Environment($loader, [
            'debug' => true
        ]);
        $twig->addExtension(new DebugExtension());
        
        // Dynamise l'affichage des modèles
        $template = $twig->load($viewName . '.html.twig');


        // Affiche les modèles
        require_once dirname(__DIR__) . '/../templates/_partial/_nav.html.twig';
        echo $template->render($viewVars);
        require_once dirname(__DIR__) . '/../templates/_partial/_footer.html.twig';
    }


    /**
     * Permet de savoir si la méthode de soumission d'un formulaire est bien POST
     *
     * @return boolean
     */
    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function addFlash(string $alert = null, string $message = null): array
    {
        $flashes = [
            'alert' => $alert,
            'messages' => $message
        ];
    
        return $flashes;
    }

    /**
     * Permet de créer un slug 
     *
     * @param string $string
     * @return string
     */
    protected function slugify(string $string): string
    {
        $matches = [
            "é" => "e",
            "è" => "e",
            "ê" => "e",
            "ë" => "e",
            "ô" => "o",
            "ö" => "o",
            "û" => "u",
            "ü" => "u"
        ];

        $string = trim($string);
        $string = mb_strtolower($string);
        $string = preg_replace("/[^\w\d\-\ ]+/", "", $string);
        $string = str_ireplace(array_keys($matches), $matches, $string);
        $string = preg_replace("/[^a-z0-9-]+/", "-", $string);
        $string = preg_replace("/\-{2,}/", "-", $string);
        $string = trim($string, "-");
        
        return $string;
    }

    protected function userIsConnected() 
    {
        $session = $_SESSION;
        
        if (!empty($session)) {
            $user = $session["userObject"];

            return $user;
        }
    }

}
