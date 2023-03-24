<?php

namespace App\Controller;

use App\Models\User;

/**
 * Controller dédié à la gestion des posts
 */
class SecurityController extends CoreController
{
    /**
     * Afficher le formulaire de connexion
     */
    public function login()
    {
        $this->show('security/login');
    }

        /**
     * Afficher le formulaire de connexion
     */
    public function register()
    {
        $this->show('security/register', [
            'user' => new User(),
            // 'tokenCSRF' => $this->generateTokenCSRF()
        ]);
    }

    /**
     * Inscription d'un user
     *
     * @return void
     */
    public function registerPost()
    {
        $alert = null;
        $message = null;

        $flashes = [
            'alert' => $alert,
            'messages' => [
                'message' => $message
            ]
        ];

        // Récupérer les données recues du formalaire d'inscription
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password_1 = filter_input(INPUT_POST, 'password_1');
        $password_2 = filter_input(INPUT_POST, 'password_2');

        // Vérifier que tous les champs ne sont pas vide 
        if (empty($email)) {
            $flashes['alert'] = 'warning';
            $flashes['messages']['message'] = 'Le champ email est vide';
        }

        if (empty($password_1)) {
            $flashes['alert'] = 'warning';
            $flashes['messages']['message'] = 'Le champ mot de passe est vide';
        }

        if (empty($password_2)) {
            $flashes['alert'] = 'warning';
            $flashes['messages']['message'] = 'Le champ confirmation de mot de passe est vide';
        }

        if ($password_1 === $password_2) {
        } else {
            $flashes['alert'] = 'danger';
            $flashes['messages']['message'] = 'Les mots de passe de corresponde pas!';
        }
        
        // Si le formulaire est valide alors ...
        if (empty($flashes['messages']['message']) && $this->isPost()) {
            // dd($flashes, 1, $this->isPost());
            // Instancier un nouvel objet User()
            $user = new User();
            // Hasher le mot de passe 
            $option = ['cost' => 12];
            $password = password_hash(
                $password_1,
                PASSWORD_BCRYPT,
                $option
            );

            // Mettre à jour les propriétés de l'instance
            $user->setEmail($email);
            $user->setPassword($password);

            // Utiliser la méthode insert() pour enregistrer les données du formulaire en base de données
            if ($user->insert()) {
                // dd($flashes, 2, $this->isPost());
                header('Location: /security/login');
                exit;
            } else { // Si erreur lors de l'enregistrement
                // dd($flashes, 3, $this->isPost());
                $flashes['alert'] = 'danger';
                $flashes['messages']['message'] = "Votre compte n'a pas été créé!";
                exit;
            }
        } // Si le formulaire est soumis mais pas valide alors ... 
        else { 
            // dd($flashes, 4, $this->isPost());
            // Afficher le formulaire pré-rempli avec les erreurs 
            $user = new User();
            $user->setEmail(filter_input(INPUT_POST, 'email'));

            // dd($checks, $uncheck);
            $this->show('security/register', [
                'user' => $user,
                'flashes' => $flashes
            ]);
        }
    }


    /**
     * Valider le formulaire de connexion d'un user
     *
     * @return void
     */
    public function loginPost()
    {
        $alert = null;
        $message = null;

        $flashes = [
            'alert' => $alert,
            'messages' => [
                'message' => $message
            ]
        ];

        $email =  filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = filter_input(INPUT_POST, 'password');

            // Vérifier l'existence du user
            $userCurrent = User::findByEmail($email);

            // Créer un système de control du formulaire et si erreur afficher un message d'alerte
            // Controle mot de passe
            if (empty($password)) {
                $flashes['alert'] = 'danger';
                $flashes['messages']['message'] = 'Merci de saisir votre mot de passe';
            } elseif (
                $userCurrent
                && !empty($password)
                && !password_verify($password, $userCurrent->getPassword())
            ) {
                $flashes['alert'] = 'danger';
                $flashes['messages']['message'] = 'Mot de passe incorrect!';
            }

            // Contrôle email
            if (empty($email)) {
                $flashes['alert'] = 'warning';
                $flashes['messages']['message'] = 'Merci de saisir votre email';
            } elseif ($email !== $userCurrent->getEmail()) {
                $flashes['alert'] = 'danger';
                $flashes['messages']['message'] = 'Email incorrect!';
            }

            // Controle du user
            if (!$userCurrent) {
                $flashes['alert'] = 'danger';
                $flashes['messages']['message'] = "Cet utilisateur n'existe pas!";
            }

            // Si il y a des erreurs on les affiches sinon ...
            if (!empty($flashes['messages']['message'])) {
                $this->show('security/login', [
                    'user' => $userCurrent,
                    'flashes' => $flashes
                ]);
                
            } else { // Connecter le user
                $_SESSION['id'] = $userCurrent->getId();
                $_SESSION['userObject'] = $userCurrent;
                header('Location: /main/home');
            }
    }

    public function logout()
    {
        session_destroy();
        header('Location: /main/home');
    }
}
