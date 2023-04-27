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
        // $flashes = $this->flashes();

        if ($this->isPost()) {

            $email =  filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $password = filter_input(INPUT_POST, 'password');

            // Vérifier l'existence du user
            $userCurrent = User::findByEmail($email);

            // Créer un système de contrôle du formulaire et si erreur afficher un message d'alerte
            // Contrôle mot de passe
            if (empty($password)) {
                $flashes = $this->flashes('warning', 'Merci de saisir votre mot de passe!');
            } elseif (
                $userCurrent
                && !empty($password)
                && !password_verify($password, $userCurrent->getPassword()) // Si la vérification du mot de passe échoue
            ) {
                $flashes = $this->flashes('danger', 'Mot de passe incorrect!');
            }

            // Contrôle email
            if (empty($email)) {
                $flashes = $this->flashes('warning', 'Merci de saisir votre email');
            } elseif (
                $userCurrent
                && $email !== $userCurrent->getEmail()
            ) {
                $flashes = $this->flashes('danger', 'Email incorrect!');
            }

            // Contrôle du user
            if (!$userCurrent) {
                $flashes = $this->flashes('danger', "Cet utilisateur n'existe pas!");
            }

            // Si il y a des erreurs on les affiches sinon ...
            if (!empty($flashes['message'])) {

                $this->show('security/login', [
                    'user' => $userCurrent,
                    'flashes' => $flashes
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
        $flashes = $this->flashes();
        $user = new User();
        $role = new Role();
        $roles = $role::findAll();

        if ($this->isPost()) {

            // Récupérer les données recues du formalaire d'inscription
            $pseudo = filter_input(INPUT_POST, 'pseudo');
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password_1 = filter_input(INPUT_POST, 'password_1');
            $password_2 = filter_input(INPUT_POST, 'password_2');
            $hiddenRole = filter_input(INPUT_POST, 'role');
            $user->setPseudo($pseudo)
                ->setEmail($email);

            // Vérifier que tous les champs ne sont pas vide 
            if (empty($email)) {
                $flashes = $this->flashes('warning', 'Le champ email est vide');
            }

            if (empty($password_1)) {
                $flashes = $this->flashes('warning', 'Le champ mot de passe est vide');
            }

            if (empty($password_2)) {
                $flashes = $this->flashes('warning', 'Le champ confirmation de mot de passe est vide');
            }
            if ($password_1 === $password_2) {
            } else {
                $flashes = $this->flashes('danger', 'Les mots de passe de corresponde pas!');
            }


            // Contrôler si le rôle soumis est un rôle existant en BDD 
            $rolesIdArray = [];
            $roleExist = false;
            foreach ($roles as $existingRole) {
                $rolesIdArray[] = $existingRole->getId();
                $getIdRoleSubmited = $existingRole::findByName($hiddenRole)->getId();
             
                // Si l'id du rôle soumis alors existe en base de données
                if (in_array($getIdRoleSubmited, $rolesIdArray)) {
                    $roleExist = true;
                    break;
                }
            }

            // Si il n'existe pas on affiche le message d'alerte
            if (!$roleExist) {
                $flashes = $this->flashes('danger', 'Erreur lors du traitement!');
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
                $user->setPassword($password)
                    ->setRoles($getIdRoleSubmited);

                try {
                    if ($user->insert()) {
                        header('Location: /security/login');
                        exit;
                    } // Si erreur lors de l'enregistrement
                    else {
                        $flashes = $this->flashes('danger', "Votre compte n'a pas été créé!");
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
                $user->setEmail(filter_input(INPUT_POST, $email));
                $this->show('security/register', [
                    'user' => $user,
                    'flashes' => $flashes
                ]);
            }
        }
        $this->show('security/register', [
            'user' => $user,
            'roles' => $roles,
            'flashes' => $flashes
        ]);
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
