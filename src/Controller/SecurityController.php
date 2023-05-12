<?php

declare(strict_types=1);

namespace App\Controller;

use App\Models\Role;
use App\Models\User;

/**
 * Controller dédié à la gestion des posts
 */
class SecurityController extends CoreController
{
    /**
     * Traitement du formulaire de connexion
     * @return void
     */
    public function login()
    {
        if ($this->isPost()) {

            $email =  filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $password = filter_input(INPUT_POST, 'password');

            // Vérifier l'existence du user
            $userCurrent = User::findByEmail($email);
            
            // Créer un système de contrôle du formulaire et si erreur afficher un message d'alerte
            if (empty($password)) {
                $this->flashes('warning', 'Merci de saisir votre mot de passe!');
            } elseif (
                $userCurrent
                && !empty($password)
                && !password_verify($password, $userCurrent->getPassword()) // Si la vérification du mot de passe échoue
            ) {
                $this->flashes('danger', 'Mot de passe incorrect!');
            }

            // Contrôle email
            if (empty($email)) {
                $this->flashes('warning', 'Merci de saisir votre email');
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
            if (!empty($flashes['message'])) {
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
     * @return User
     */
    public function register()
    {
        $user = new User();
        $role = new Role();
        $roles = $role::findAll();

        if ($this->isPost()) {

            // Récupérer les données recues du formalaire d'inscription
            $pseudo = filter_input(INPUT_POST, 'pseudo');
            $slug = $this->slugify($pseudo);
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password_1 = filter_input(INPUT_POST, 'password_1');
            $password_2 = filter_input(INPUT_POST, 'password_2');
            $hiddenRole = filter_input(INPUT_POST, 'role');
            $user->setPseudo($pseudo)
                ->setEmail($email);

            // Vérifier que tous les champs ne sont pas vide 
            if (empty($email)) {
                $this->flashes('warning', 'Le champ email est vide');
            }

            if (empty($password_1)) {
                $this->flashes('warning', 'Le champ mot de passe est vide');
            }

            if (empty($password_2)) {
                $this->flashes('warning', 'Le champ confirmation de mot de passe est vide');
            }
            if ($password_1 === $password_2) {
            } else {
                $this->flashes('danger', 'Les mots de passe de corresponde pas!');
            }


            // Contrôler si le rôle soumis est un rôle existant en BDD 
            $rolesIdArray = [];
            $roleExist = false;
            foreach ($roles as $existingRole) {
                $rolesIdArray[] = $existingRole->getId();
                $getIdRoleSubmited = $existingRole::findByName($hiddenRole)->getId();
             
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
            if (empty($flashes['message'])) {
                // Hasher le mot de passe 
                $option = ['cost' => User::HASH_COST];
                $password = password_hash(
                    $password_1,
                    PASSWORD_BCRYPT,
                    $option
                );
                $user->setSlug($slug)
                    ->setPassword($password)
                    ->setRoles($getIdRoleSubmited);
                    
                // Permettra de vérifier si l'email soumis n'exite pas en base
                try {
                    if ($user->insert()) {
                        $this->flashes('success', 'Votre compte a bien été créé, merci de vous connecter.');
                        header('Location: /security/login');
                        exit;
                    } // Si erreur lors de l'enregistrement
                    else {
                        $this->flashes('danger', "Votre compte n'a pas été créé!");
                    }
                } catch (\Exception $e) { // Attrapper l'exception 23000 qui correspond du code Unique de MySQL (avant ça il indiquer dans la bdd quel champ est 'unique')
                    if ($e->getCode() === '23000') {
                        $flashes = $this->flashes('danger', 'Il existe déjà un compte avec cet email!');
                    } else {
                        $flashes = $this->flashes('danger', $e->getMessage());
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

    public function forgetPassword() 
    {
        if ($this->isPost()) {
   
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

            if (empty($email)) {
                $this->flashes('warning', "Celui il est important parce sinon on va rien pourvoir faire...");
            }
            $user = User::findByEmail($email);

            // Ici je n'arrive pas intercepter l'erreur PHP
            //Fatal error: Uncaught Error: Call to a member function getPseudo() on bool in C:\xampp\htdocs\the2dbubbles\src\Controller\SecurityController.php:191 Stack trace: #0 C:\xampp\htdocs\the2dbubbles\public\index.php(51): App\Controller\SecurityController->forgetPassword() #1 {main} thrown in C:\xampp\htdocs\the2dbubbles\src\Controller\SecurityController.php on line 191
            if (!isset($user)) {
                $this->flashes('danger', "Oupss! Cet utilisateur n'existe pas!");
            } else {
                $pseudo = $user->getPseudo();
            }
      
            try {
                if ($user) {
                    // Création d'un token d'une chaîne hexadecimal de 32 caractères
                    $token = bin2hex(random_bytes(32));
                    dd($token);
                    // $recoveryLink = $this->recoveryLink();
                        $this->messageSend(
                        $subject = "Réinitialisation de mot de passe",
                        $pseudo,
                        $email,
                        $content = "Hello, $pseudo, <br> <br> Si tu n'est pas fait cette demande, ignores simplement cet email. <br> Sinon cliques sur le lien ci-dessous <br> ",
                    );
                } else {
                    $this->flashes('danger', "Oupss! L'email $email n'existe pas!");
                }
            } catch (\Exception $e) {
                dd($e);
                if ($e->getCode() !== '23000') {
                    $this->flashes('danger', "Oupss! l'email n'a pas été envoyé. Merci de refaire une demande.");
                } else {
                    $this->flashes('danger', $e->getMessage());
                }
            }
            
        }
        $this->show('security/password/forget_password');
    }


    /**
     * Déconnexion de l'utilisateur
     * @return void
     */
    public function logout()
    {
        session_destroy();
        header('Location: /main/home');
    }
}
