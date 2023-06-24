<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\RoleRepository;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Twig\Extension\DebugExtension;

class CoreController
{
    /**
     * Méthode permettant la gestion des fonctions et affichage dans les templates Twig
     *
     * @param string $viewName
     * @param array $viewVars
     * @return void
     */
    protected function show(string $viewName, array $viewVars= []): void
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
            $roleRepository = new RoleRepository();
            if ($user) {
                $userRoleId = $user->getRoleId();
                $role = $roleRepository->findById($userRoleId);
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

    /**
     * Permet de créer la clé flashes dans la super globale $_SESSION
     *
     * @param string|null $alert
     * @param string|null $message
     * @return void
     */
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
     * @return ?User
     */
    protected function userIsConnected(): ?User
    {
        $session = $_SESSION;

        if (isset($session["userObject"])) {
            $user = $session["userObject"];
            return $user;
        } else {
            return null;
        }
    }

    /**
     * Méthode permettant de récupérer un tableau des roles
     *
     * @return array
     */
    protected function getRoles(): array
    {
        $role = new RoleRepository();
  
        $roles = $role->findAll();

        return $roles;
    }

    /**
     * Envoyer de messages
     *
     * @param string $from
     * @param string $content
     * @param string $object
     * @param string $name
     * @param array $options
     * @return void
     */
    protected function messageSend(
        string $object,
        string $name = null,
        string $from,
        string $content,
        array $options = []
    ) {
        $options = array_merge([
            'admin' => 'contact@2dbubbles.fr',
        ],  $options);

        $to = $options['admin'];

        $mail = new PHPMailer(true);

        // Gestion des exceptions
        try {
            // Configuration de SMTP
            $mail->isSMTP();
            $mail->Host = "localhost";
            $mail->Port = 1025; // Port MailHog

            // Charset 
            $mail->CharSet = "utf-8";

            // Destinataire
            $mail->addAddress($to);

            // Expéditeur
            $mail->setFrom($from);

            // Contenu du message
            $mail->isHTML(true); // Permet d'ajouter des balises HTML
            $mail->Subject = $object;
            $mail->Body = "<p>$content</p> <p>Prénom/Pseudo : $name</p>  <p>Email: $from</p> ";

            // Envoyer le mail
            $mail->send();
            $this->flashes('success', 'Ton Bubbles message a bien été envoyé.');
        } catch (Exception) {
            $this->flashes('danger', "Ton Bubbles message n'a pas été envoyé!");
            echo "Message non envoyé. Erreur{$mail->ErrorInfo}";
        }
    }
}
