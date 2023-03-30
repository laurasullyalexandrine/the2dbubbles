<?php 

namespace App\Controller;

use App\Models\Role;
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
     * Page d'ajout d'user méthode GET
     *
     * @return void
     */
    public function add()
    {
        $roles = new Role();
        $this->show('user/add', [
            'user' => new User(), 
            'roles' => $roles::findAll()
        ]);
    }


    public function addUserPost()
    {
        $flashes = $this->addFlash();
    
        // Récupérer les données recues du formalaire d'inscription
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password_1 = filter_input(INPUT_POST, 'password_1');
        $password_2 = filter_input(INPUT_POST, 'password_2');
        $role = filter_input(INPUT_POST, 'role');

        // Récupérer les emails des users déjà enregitrés en base
        $user = new User();
        $users = $user::findAll();
        foreach ($users as $userBdd) {

            $emails[] = $userBdd->getEmail();
        }

        // Vérifier que tous les champs ne sont pas vide 
        if (empty($email)) {
            $flashes = $this->addFlash('warning', 'Le champ email est vide');
        } elseif (in_array($email, $emails)) {
            $flashes = $this->addFlash('danger', 'Il existe déjà un compte avec cet email!');
        }
        
        if (empty($password_1)) {
            $flashes = $this->addFlash('warning', 'Le champ mot de passe est vide');
        }

        if (empty($password_2)) {
            $flashes = $this->addFlash('warning', 'Le champ confirmation de mot de passe est vide');
        }

        if ($password_1 === $password_2) {
        } else {
            $flashes = $this->addFlash('danger', 'Les mots de passe de corresponde pas!');
        }

        if ($role === "super_administrateur" || $role === "administrateur" || $role === "utilisateur") {
        } else {
            $flashes = $this->addFlash('warning', 'Donnée du select invalide'); 
        }

        // Si le formulaire est valide alors ...
        if (empty($flashes['messages']) && $this->isPost()) {
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
            $user->setRoles($role);
            $user->setPassword($password);
            dd($user);

            // Utiliser la méthode insert() pour enregistrer les données du formulaire en base de données
            if ($user->insert()) {
                // dd($flashes, 2, $this->isPost());
                header('Location: /user/list');
                exit;
            } // Si erreur lors de l'enregistrement
            else { 
                // dd($flashes, 3, $this->isPost());
                $flashes = $this->addFlash('danger', "Votre compte n'a pas été créé!");
                exit;
            }
        } // Si le formulaire est soumis mais pas valide alors ... 
        else { 
            // dd($flashes, 4, $this->isPost());
            // Afficher le formulaire pré-rempli avec les erreurs 
            $user = new User();
            $user->setEmail(filter_input(INPUT_POST, 'email'));
            $user->setPassword(filter_input(INPUT_POST, 'password_1'));
            $user->setRoles(filter_input(INPUT_POST, 'email'));

            $this->show('user/list', [
                'user' => $user,
                'flashes' => $flashes
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

    public function delete($userId) 
    {
        $flashes = $this->addFlash();

        $user = User::findBy($userId);
        dd($user);
        if ($user) {
            $user->delete();
            $flashes = $this->addFlash('success', "L'utilisateur a été supprimé");
            header('Location: user/list');
        } else {
            $flashes = $this->addFlash('danger', "L'utilisateur n'existe pas!");
        }

        $this->show('user/_delete', [
            'user' => $user,
            'flashes' => $flashes
        ]);
    }
}