<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;

/**
 * Controller dédié à la gestion des posts
 */
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
     * Traitement du formulaire de connexion
     * @return void
     */
    public function login(): void
    {
        if ($this->isPost()) {
            $email =  filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $password = filter_input(INPUT_POST, 'password');

            // Vérifier l'existence du user
            $userCurrent = $this->userRepository->findByEmail($email);

            // Créer un système de contrôle du formulaire et si erreur afficher un message d'alerte
            if (empty($password)) {
                $this->flashes('warning', 'Merci de saisir Ton mot de passe!');
            } elseif (
                $userCurrent
                && !empty($password)
                && !password_verify($password, $userCurrent->getPassword()) // Si la vérification du mot de passe échoue
            ) {
                $this->flashes('danger', 'Mot de passe incorrect!');
            }

            // Contrôle email
            if (empty($email)) {
                $this->flashes('warning', 'Merci de saisir ton email');
            } elseif (
                $userCurrent
                && $email !== $userCurrent->getEmail()
            ) {
                $this->flashes('danger', 'Email incorrect!');
            }

            // Contrôle du user
            if (!$userCurrent) {
                $this->flashes('danger', "Cet utilisateur n'existe pas!");
                header('Location: /security/login');
            }

            // Si il y a des erreurs on les affiches sinon ...
            if (!empty($_SESSION["flashes"])) {
                $this->show('security/login', [
                    'user' => $userCurrent
                ]);
            } // Connecter le user
            else {
                $_SESSION['id'] = $userCurrent->getId();
                $_SESSION['userObject'] = $userCurrent;
                header('Location: /main/home');
            }
        }
        $this->show('security/login');
    }

    /**
     * Traitement du formulaire d'inscription
     * @return void
     */
    public function register(): void
    {
        $user = new User();
        $roles = $this->roleRepository->findAll();

        if ($this->isPost()) {

            // Récupérer les données reçues du formalaire d'inscription
            $pseudo = filter_input(INPUT_POST, 'pseudo');
            $slug = $this->slugify($pseudo);
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = filter_input(INPUT_POST, 'password');
            $password_2 = filter_input(INPUT_POST, 'password_2');
            $hiddenRole = filter_input(INPUT_POST, 'role');
            $user->setPseudo($pseudo)
                ->setEmail($email);

            // Vérifier que tous les champs ne sont pas vide 
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

            // Contrôler si le rôle soumis est un rôle existant en BDD 
            $rolesIdArray = [];
            $roleExist = false;
            foreach ($roles as $existingRole) {
                $rolesIdArray[] = $existingRole->getId();
                $getIdRoleSubmited = $this->roleRepository->findByName($hiddenRole)->getId();

                // Si l'id du rôle soumis existe en base de données alors
                if (in_array($getIdRoleSubmited, $rolesIdArray)) {
                    // la valeur de la variable roleExist devient true
                    $roleExist = true;
                    break;
                }
            }

            // Si il n'existe pas on affiche le message d'alerte
            if (!$roleExist) {
                $this->flashes('danger', 'Erreur lors du traitement!');
            }

            // Si le formulaire est valide alors ...
            if (empty($_SESSION["flashes"])) {
                // Hasher le mot de passe 
                $option = ['cost' => UserRepository::HASH_COST];
                $password = password_hash(
                    $password,
                    PASSWORD_BCRYPT,
                    $option
                );
                $user->setSlug($slug)
                    ->setPassword($password)
                    ->setRoleId($getIdRoleSubmited);

                // Permettra de vérifier si l'email soumis n'exite pas en base
                try {
                    if ($this->userRepository->insert($user)) {
                        $this->flashes('success', 'Ton Bubbles Space a bien été créé, C\'est parti!');
                        header('Location: /security/login');
                        return;
                    } // Si erreur lors de l'enregistrement
                    else {
                        $this->flashes('danger', "Ton Bubbles Space n'a pas été créé!");
                    }
                } catch (\Exception $e) { // Attrapper l'exception 23000 qui correspond du code Unique de MySQL (avant ça il indiquer dans la bdd quel champ est 'unique')
                    if ($e->getCode() === '23000') {
                        $this->flashes('danger', 'Il existe déjà un compte avec cet email!');
                    } else {
                        $this->flashes('danger', $e->getMessage());
                    }
                }
            } // Si le formulaire est soumis mais pas valide alors ... 
            else {
                // Afficher le formulaire pré-rempli avec les erreurs 
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
     * Gestion du formulaire demande de réinitialisation de mot de passe
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

                // Création d'un token d'une chaîne hexadecimal de 32 caractères
                $token = bin2hex(random_bytes(32));

                // Ajout du token généré à l'utilisateur reconnu
                $user->setToken($token);
                $this->userRepository->update($user);
                $host = $_SERVER["HTTP_HOST"];
                $scheme = array_key_exists("HTTPS", $_SERVER) ? "https" : "http";

                // Générer le lien de réinitialisation de mot de passe
                $resetUrl = "$scheme://$host/security/resetPassword/$token";

                // Envoyer le mail
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
     * Page redirection Confirmation d'envoi de mail
     *
     * @return void
     */
    public function confirmationSendEmail()
    {
        $this->show('security/password/confirmation');
    }

    /**
     * Gestion du formulaire du nouveau mot de passe
     *
     * @param string $token
     * @return void
     */
    public function resetPassword(string $token): void
    {
        // Vérifier si le token existe en bdd
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
                    // Effacer le token avec une chaîne de caractère vide
                    $user->setToken('');

                    // Hasher et remplacer le mot de passe 
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
                    } // Si erreur lors de l'enregistrement
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
     * Déconnexion de l'utilisateur
     * @return void
     */
    public function logout(): void
    {
        session_destroy();
        header('Location: /security/login');
    }
}
