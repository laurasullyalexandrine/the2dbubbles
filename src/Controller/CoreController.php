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
     * Method for managing functions and display in Twig templates
     *
     * @param string $viewName
     * @param array $viewVars
     * @return void
     */
    protected function show(string $viewName, array $viewVars= []): void
    {
        // Load absolute path to front folder
        $loader = new \Twig\Loader\FilesystemLoader(dirname(__DIR__) . '/../templates/');

        // Create the environment of the models loaded with those in the front folder
        $twig = new \Twig\Environment($loader, [
            'debug' => true,
        ]);
        $twig->addExtension(new DebugExtension());

        /**
         * Access control according to user role
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
         * Lets you know if there is a user in session 
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
         * Allows you to have the flash key permanently in the session
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

        // Make models display more dynamic
        $template = $twig->load($viewName . '.html.twig');

        // Affiche les modèles
        echo $template->render($viewVars);
    }


    /**
     * Allows you to know if the submission method of a form is indeed POST
     *
     * @return boolean
     */
    protected function isPost(): bool
    {
        return $_SERVER["REQUEST_METHOD"] === 'POST';
    }

    /**
     * Allows to give access to the super global $_SERVER
     *
     * @return string
     */
    protected function uri(): string
    {
        return $_SERVER["REQUEST_URI"];
    }

    /**
     * Allows to create the flashes key in the super global $_SESSION
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
     * Allows you to create a slug 
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
     * Method to retrieve logged in user
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
     * Method to retrieve an array of roles
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
     * Send messages
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

        // Exception handling
        try {
            // SMTP Setup
            $mail->isSMTP();
            $mail->Host = "localhost";
            $mail->Port = 1025; // Port MailHog

            // Charset 
            $mail->CharSet = "utf-8";

            // Recipient
            $mail->addAddress($to);

            // Sender
            $mail->setFrom($from);

            // Message content
            $mail->isHTML(true); // Allows you to add HTML tags
            $mail->Subject = $object;
            $mail->Body = "<p>$content</p> <p>Prénom/Pseudo : $name</p>  <p>Email: $from</p> ";

            // send email
            $mail->send();
            $this->flashes('success', 'Ton Bubbles message a bien été envoyé.');
        } catch (Exception) {
            $this->flashes('danger', "Ton Bubbles message n'a pas été envoyé!");
            echo "Message non envoyé. Erreur{$mail->ErrorInfo}";
        }
    }
}
