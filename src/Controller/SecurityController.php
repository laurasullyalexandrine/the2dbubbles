<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;

class SecurityController extends CoreController
{
    protected UserRepository $userRepository;
    protected RoleRepository $roleRepository;
    public function __construct()
    {
        $this->userRepository = new UserRepository();
        $this->roleRepository = new RoleRepository();
    }

    /**
     * Login form processing
     * @return void
     */
    public function login(): void
    {
        if ($this->isPost()) {
            $email =  filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $password = filter_input(INPUT_POST, 'password');

            // Check the existence of the user
            $userCurrent = $this->userRepository->findByEmail($email);

            // Create a form control system and if error display an alert message
            if (empty($password)) {
                $this->flashes('warning', 'Merci de saisir Ton mot de passe!');
            } elseif (
                $userCurrent
                && !empty($password)
                && !password_verify($password, $userCurrent->getPassword()) // If password verification fails
            ) {
                $this->flashes('danger', 'Mot de passe incorrect!');
            }

            // Email control
            if (empty($email)) {
                $this->flashes('warning', 'Merci de saisir ton email');
            } elseif (
                $userCurrent
                && $email !== $userCurrent->getEmail()
            ) {
                $this->flashes('danger', 'Email incorrect!');
            }

            // User control
            if (!$userCurrent) {
                $this->flashes('danger', "Cet utilisateur n'existe pas!");
                header('Location: /security/login');
            }

            // If there are errors we display them otherwise ...
            if (!empty($_SESSION["flashes"])) {
                $this->show('security/login', [
                    'user' => $userCurrent
                ]);
            } // Connect the user
            else {
                $_SESSION['id'] = $userCurrent->getId();
                $_SESSION['userObject'] = $userCurrent;
                header('Location: /main/home');
            }
        }
        $this->show('security/login');
    }

    /**
     * Processing the registration form
     * @return void
     */
    public function register(): void
    {
        $user = new User();
        $roles = $this->roleRepository->findAll();

        if ($this->isPost()) {

            // Retrieve the data received from the registration form
            $pseudo = filter_input(INPUT_POST, 'pseudo');
            $slug = $this->slugify($pseudo);
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = filter_input(INPUT_POST, 'password');
            $password_2 = filter_input(INPUT_POST, 'password_2');
            $hiddenRole = filter_input(INPUT_POST, 'role');
            $user->setPseudo($pseudo)
                ->setEmail($email);

            // Check that all fields are not empty
            if (empty($email)) {
                $this->flashes('warning', 'Le champ email est vide');
            }

            if (empty($password)) {
                $this->flashes('warning', 'Le champ mot de passe est vide');
            }

            if (empty($password_2)) {
                $this->flashes('warning', 'Le champ confirmation de mot de passe est vide');
            }

            if ($password !== $password_2) {
                $this->flashes('danger', 'Les mots de passe de correspondent pas!');
            }

            // Check if the submitted role is an existing role in BDD
            $rolesIdArray = [];
            $roleExist = false;
            foreach ($roles as $existingRole) {
                $rolesIdArray[] = $existingRole->getId();
                $getIdRoleSubmited = $this->roleRepository->findByName($hiddenRole)->getId();

                // If the submitted role id exists in the database then
                if (in_array($getIdRoleSubmited, $rolesIdArray)) {
                    // the value of the roleExist variable becomes true
                    $roleExist = true;
                    break;
                }
            }

            // If it does not exist, the alert message is displayed.
            if (!$roleExist) {
                $this->flashes('danger', 'Erreur lors du traitement!');
            }

            // If the form is valid then ...
            if (empty($_SESSION["flashes"])) {
                // Hash the password 
                $option = ['cost' => UserRepository::HASH_COST];
                $password = password_hash(
                    $password,
                    PASSWORD_BCRYPT,
                    $option
                );
                $user->setSlug($slug)
                    ->setPassword($password)
                    ->setRoleId($getIdRoleSubmited);

                // Will allow to check if the email submitted does not exist in the database
                try {
                    if ($this->userRepository->insert($user)) {
                        $this->flashes('success', 'Ton Bubbles Space a bien été créé, C\'est parti!');
                        header('Location: /security/login');
                        return;
                    } // If error while recording
                    else {
                        $this->flashes('danger', "Ton Bubbles Space n'a pas été créé!");
                    }
                } catch (\Exception $e) { // Catch exception 23000 which corresponds to MySQL Unique code (before that it indicates in the database which field is 'unique')
                    if ($e->getCode() === '23000') {
                        $this->flashes('danger', 'Il existe déjà un compte avec cet email!');
                    } else {
                        $this->flashes('danger', $e->getMessage());
                    }
                }
            } // If the form is submitted but not valid then ... 
            else {
                // Show pre-filled form with errors
                $user->setEmail($email);
                $this->show('security/register', [
                    'user' => $user
                ]);
            }
        }
        $this->show('security/register', [
            'user' => $user,
            'roles' => $roles
        ]);
    }

    /**
     * Management of the password reset request form
     *
     * @return void
     */
    public function forgetPassword()
    {
        if ($this->isPost()) {
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

            if (empty($email)) {
                $this->flashes('warning', "Celui il est important parce sinon on va rien pourvoir faire...");
            }
            $user = $this->userRepository->findByEmail($email);

            try {
                if (!$user instanceof User) {
                    throw new \Exception("Oupss! Cet utilisateur n'existe pas!");
                }
                $pseudo = $user->getPseudo();

                // Creating a token from a 32 character hexadecimal string
                $token = bin2hex(random_bytes(32));

                // Add generated token to recognized user
                $user->setToken($token);
                $this->userRepository->update($user);
                $host = $_SERVER["HTTP_HOST"];
                $scheme = array_key_exists("HTTPS", $_SERVER) ? "https" : "http";

                // Generate password reset link
                $resetUrl = "$scheme://$host/security/resetPassword/$token";

                // Send email
                $this->messageSend(
                    "Réinitialisation de mot de passe",
                    $pseudo,
                    $email,
                    'Hello,' . ' ' . $pseudo . ', <br> <br> Si tu n\'est pas fait cette demande, ignores simplement cet email. <br> Sinon cliques sur le lien ci-dessous <br>' . '<a href="' . $resetUrl . '">Réinitialise ton mot de passe</a>',

                );
                header("Location: /security/confirmationSendEmail");
                return;
            } catch (\Exception $e) {
                if ($e->getCode() !== '23000') {
                    $this->flashes('danger', "Oupss! l'email n'a pas été envoyé. Merci de refaire une demande. Si tu as reçu le premier n'en tiens pas compte.");
                } else {
                    $this->flashes('danger', $e->getMessage());
                }
            }
        }
        $this->show('security/password/forget_password');
    }

    /**
     * Redirection page Confirmation of sending mail
     *
     * @return void
     */
    public function confirmationSendEmail()
    {
        $this->show('security/password/confirmation');
    }

    /**
     * Management of the new password form
     *
     * @param string $token
     * @return void
     */
    public function resetPassword(string $token): void
    {
        // Check if the token exists in db
        $user = $this->userRepository->findOneByToken($token);

        if ($this->isPost()) {
            $password = filter_input(INPUT_POST, 'password');
            $password_2 = filter_input(INPUT_POST, 'password_2');

            if (empty($password)) {
                $this->flashes('warning', 'Le champ mot de passe est vide');
            }

            if (empty($password_2)) {
                $this->flashes('warning', 'Le champ confirmation de mot de passe est vide');
            }

            if ($password !== $password_2) {
                $this->flashes('danger', 'Les mots de passe de correspondent pas!');
            }

            if (!$user instanceof User) {
                throw new \Exception("Oupss! Cet utilisateur n'existe pas!");
            } else {
                if (empty($_SESSION["flashes"])) {
                    // Delete token with empty string
                    $user->setToken('');

                    // Hasher and replace password
                    $option = ['cost' => UserRepository::HASH_COST];
                    $password = password_hash(
                        $password,
                        PASSWORD_BCRYPT,
                        $option
                    );
                    $user->setPassword($password);

                    if ($this->userRepository->update($user)) {
                        $this->flashes('success', 'Ton mot de passe est maintenant modifié. Ah! Tu peux te connecter maintenant.');
                        header('Location: /security/login');
                        return;
                    } // If error while recording
                    else {
                        $this->flashes('danger', "Oupss! Ton mot de passe n'a pas été modifié!");
                    }
                }
            }
        }

        $this->show('security/password/reset_password', [
            'user' => $user
        ]);
    }

    /**
     * User logout
     * @return void
     */
    public function logout(): void
    {
        session_destroy();
        header('Location: /security/login');
    }
}
