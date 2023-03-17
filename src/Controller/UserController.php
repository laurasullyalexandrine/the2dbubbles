<?php 

namespace App\Controller;

use App\Models\User;

class UserController extends CoreController {

    /**
     * Listing des users
     * @return void
     */
    public function list()
    {
        $users = User::findAll();
        $this->show('user/list', [
                'users' => $users
            ]
        );
    }

    /**
     * Inscription d'un user
     *
     * @return void
     */
    public function register() {
        // Récupérer les données recues du formalaire d'inscription
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password_1 = filter_input(INPUT_POST, 'password_1');
        $password_2 = filter_input(INPUT_POST, 'password_2');

        $errors = [];
        // Vérifier que tous les champs ne sont pas vide 
        if (empty($email)) {
             $errors[] = 'Le champ email est vide';
        }

        if (empty($password_1)) {
             $errors[] = 'Le champ mot de passe est vide';
        }
        if (empty($password_2)) {
             $errors[] = 'Le champ mot de passe est vide';
        }

        if ($password_1 === $password_2) {
        } else {
            $errors[] = 'Les mots de passe ne correspondent pas';
        }

        // Si formulaire valide alors ...
        if(empty($errors)) {
            // Instancier un nouvel objet User()
            $user = new User();
            dd($user);
            // Hasher le mot de passe 
            $option = ['cost' => 12];
            $password = password_hash(
                $password_1, PASSWORD_BCRYPT, $option
            );

            // Mettre à jour les propriétés de l'instance
            $user->setEmail($email);
            $user->setPassword($password);
    
            // Utiliser la méthode insert() pour enregistrer les données du formulaire en base de données
            if ($user->insert()) {
                header('Location: /user/list');
                exit;
            } else { // Si erreur lors de l'enregistrement
                $errors[] = "L'utilisateur n'a pas été enregistré";
            }
        } else { // Si le formulaire est soumis mais pas valide alors ... 

            // Afficher le formulaire pré-rempli avec les erreurs 
            $user = new User();

            $user->setEmail(filter_input(INPUT_POST, 'email'));

            $this->show('user/register', [
                'user' => $user,
                'errors' => $errors
            ]);
        }

    }
    /**
     * Affiche la vue édition d'un user 
     *
     * @param [type] $userId
     * @return void
     */
    public function edit($userId)
    {
        $user = User::findBy($userId);
        $this->show('user/edit', [
                'user' => $user
            ]);
    }

    
}