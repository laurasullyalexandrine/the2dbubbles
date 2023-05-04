<?php

declare(strict_types=1);

namespace App\Controller;

use App\Models\Role;
use Twig\Extension\DebugExtension;

class CoreController
{
    protected function show($viewName, $viewVars = [])
    {
        // Charge le chemin absolu vers le dossier front
        $loader = new \Twig\Loader\FilesystemLoader(dirname(__DIR__) . '/../templates/');

        // Crée l'environement des modèles charger avec ceux dans le dossier front
        $twig = new \Twig\Environment($loader, [
            'debug' => true,
        ]);
        $twig->addExtension(new DebugExtension());

        /**
         * Controle d'accès en fonction du rôle du user
         */
        $isGranted = new \Twig\TwigFunction('is_granted', function () {
            $user = $this->userIsConnected();
            if ($user) {
                $userRoleId = $user->getRoles();
                $role = Role::findById($userRoleId);
                $roleName = $role->getName();

                return $roleName;
            }
        });
        $twig->addFunction($isGranted);

        /**
         * Permet de savoir si il y a un user en session 
         */
        $user = new \Twig\TwigFunction('user', function () {
            $userCurrent = $this->userIsConnected();
            if ($userCurrent) {

                return $userCurrent;
            } else {
                return;
            }
        });
        $twig->addFunction($user);

        /**
         * Permet d'avoir la clé flashes en permanence dans la session
         */
        $displayFlashes = new \Twig\TwigFunction('display_flashes', function () {

            $flashes = isset($_SESSION['flashes']) ? $_SESSION['flashes'] : [];
            
            $_SESSION["flashes"] = [];

            return $flashes;
        });
        $twig->addFunction($displayFlashes);

        $request_uri = new \Twig\TwigFunction('request_uri', function () {
     
            $serverRoad = $_SERVER["REQUEST_URI"];
            return $serverRoad;
        });
        $twig->addFunction($request_uri);

        // Dynamiser l'affichage des modèles
        $template = $twig->load($viewName . '.html.twig');

        // Affiche les modèles
        echo $template->render($viewVars);
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

    protected function flashes(string $alert = null, string $message = null): void
    {
        $flash = [
            'alert' => $alert,
            'message' => $message
        ];

        $flashes = isset($_SESSION['flashes']) ? $_SESSION['flashes'] : [];

        $flashes[] = $flash;
        $_SESSION['flashes'] = $flashes;
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

    /**
     * Méthode permettant de récupérer le user connecté
     *
     * @return User
     */
    protected function userIsConnected()
    {
        $session = $_SESSION;

        if (isset($session["userObject"])) {
            $user = $session["userObject"];
            return $user;
        } else {
            return;
        }
    }

}
