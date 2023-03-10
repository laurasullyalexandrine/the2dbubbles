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

    public function isConnect(): bool 
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return !empty($_SESSION['login']);
    }

    public function userConnect()
    {
        if(!$this->isConnect()) {
            header('Location: /security/login');
            exit;
        }
    }

    public function SignUp() {
        $email =  filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = filter_input(INPUT_POST, 'password');

        $errors = [];

        // Vérifier l'existence du user
        $userCurrent = User::findByEmail($email);

        // Créer un système de control du formulaire et si erreur afficher un message d'alerte
        // Controle mot de passe
        if (empty($password)) {
            $errors[] = 'Merci de saisir votre mot de passe';
        } elseif ($userCurrent && !empty($password) && password_verify($password, $userCurrent->getPassword())) {
            $errors[] = 'Mot de passe incorrect!';
        }

        // Controle email
        if (empty($email)) {
            $errors[] = 'Merci de saisir votre email';
        } elseif ($email !== $userCurrent->getEmail()) {
            $errors[] = 'Email incorrect!';
        }

        // Controle mot de passe
        if (!$userCurrent) {
            $errors[] = "Cet utilisateur n'existe pas!";
        }

        // Si il y a des erreurs on les affiches sinon ...
        if (!empty($errors)) {
            dump($errors);
        } else { // Connecter le user
            $_SESSION['id'] = $userCurrent->getId();
            $_SESSION['userObject'] = $userCurrent;
            header('Location: /');
        }
    }

    public function logout()
    {
        session_destroy();
        header('Location: /security/login');
    }
}