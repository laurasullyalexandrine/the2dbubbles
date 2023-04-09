<?php

namespace App\Controller;

use App\Models\Role;
use App\Models\User;

class UserController extends CoreController
{

    /**
     * reading des users
     * @return void
     */
    public function read()
    {
        $users = User::findAll();
        $this->show('user/read', [
                'users' => $users
            ]);
    }


    /**
     * Page d'ajout d'user méthode GET
     *
     * @return void
     */
    public function create()
    {
        $flashes = $this->addFlash();
        $user = new User();
        $role = new Role();
        $roles = $role::findAll();

        if ($this->isPost()) {

            // Récupérer les données recues du formalaire d'inscription
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $pseudo = filter_input(INPUT_POST, 'pseudo');
            $password_1 = filter_input(INPUT_POST, 'password_1');
            $password_2 = filter_input(INPUT_POST, 'password_2');
            // Contraindre le type de la valeur soumis 
            $role = (int)filter_input(INPUT_POST, 'role');
            // Mettre à jour les propriétés de l'instance
            $user->setPseudo($pseudo);
            $user->setEmail($email);
            $user->setRoles($role);

            // Vérifier que tous les champs ne sont pas vide 
            if (empty($pseudo)) {
                $flashes = $this->addFlash('warning', 'Le champ prénom/pseudo est vide');
            }
            if (empty($email)) {
                $flashes = $this->addFlash('warning', 'Le champ email est vide');
            }

            if (empty($password_1)) {
                $flashes = $this->addFlash('warning', 'Le champ mot de passe est vide');
            }

            if (empty($password_2)) {
                $flashes = $this->addFlash('warning', 'Le champ confirmation de mot de passe est vide');
            }

            if ($password_1 !== $password_2) {
                $flashes = $this->addFlash('danger', 'Les mots de passe ne corresponde pas!');
            }

            // Contrôler si le rôle soumis est un rôle existant en BDD 
            $roleExist = false;
            foreach ($roles as $existingRole) {
                // Si l'id du rôle soumis existe en base de données
                if ($existingRole->getId() === $role) {
                    $roleExist = true;
                    break;
                }
            }

            // Si il n'existe pas on affiche le message d'alerte
            if (!$roleExist) {
                $flashes = $this->addFlash('warning', 'Le rôle choisi est invalide');
            }

            // Si le formulaire est valide alors ...
            if (empty($flashes['messages'])) {
                // Hasher le mot de passe 
                $option = ['cost' => User::HASH_COST];
                $password = password_hash(
                    $password_1,
                    PASSWORD_BCRYPT,
                    $option
                );
                // Mettre à jour les propriétés de l'instance
                $user->setPassword($password);

                // Essayer de faire l'insertion du nouvel utilisateur 
                try {
                    if ($user->insert()) {
                        header('Location: /user/read');
                        exit;
                    } // Sinon erreur lors de l'enregistrement
                    else {
                        $flashes = $this->addFlash('danger', "Votre compte n'a pas été créé!");
                    }
                } catch (\Exception $e) { // Attrapper l'exception 23000 qui correspond du code Unique de MySQL (avant ça il indiquer dans la bdd quel champ est 'unique')
                    if ($e->getCode() === '23000') {
                        $flashes = $this->addFlash('danger', 'Il existe déjà un compte avec cet email!');
                    } else {
                        $flashes = $this->addFlash('danger', $e->getMessage());
                    }
                }
            }
        }
        $this->show('user/create', [
            'user' => $user,
            'roles' => $roles,
            'flashes' => $flashes
        ]);
    }

    /**
     * Éditer un utilisateur
     *
     * @param [type] $userId
     * @return void
     */
    public function update($userId)
    {
        $flashes = $this->addFlash();
        $user = User::findById($userId);
        $roleCurrentUser = $user->getRoles();
        $roles = Role::findAll();


        foreach ($roles as $existingRole) {
            if ($existingRole->getId() === $roleCurrentUser) {
                $roleNameUser = $existingRole->getName();
            }
        }

        if ($this->isPost()) {
            $pseudo = filter_input(INPUT_POST, 'pseudo');
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password_1 = filter_input(INPUT_POST, 'password_1');
            $role = (int)filter_input(INPUT_POST, 'role');
            $user->setPseudo($pseudo);
            $user->setEmail($email);
            $user->setRoles($role);
   
            // Vérifier que tous les champs ne sont pas vide 
            if (empty($pseudo)) {
                $flashes = $this->addFlash('warning', 'Le champ Prénom/Pseudo est vide');
            }
            if (empty($email)) {
                $flashes = $this->addFlash('warning', 'Le champ email est vide');
            }
            if (empty($password_1)) {
                $flashes = $this->addFlash('warning', 'Le champ mot de passe est vide');
            }

            if (empty($flashes["messages"])) {
                $option = ['cost' => User::HASH_COST];
                $password = password_hash(
                    $password_1,
                    PASSWORD_BCRYPT,
                    $option
                );
                $user->setPassword($password);
                if ($user->update()) {
                    header('Location: /user/read');
                    exit;
                } else {
                    $flashes = $this->addFlash('danger', "L'utilisateur n'a pas été modifié!");
                }
            } else {
                $user->setPseudo(filter_input(INPUT_POST, $pseudo));
                $user->setEmail(filter_input(INPUT_POST, $email));
                $user->setRoles(filter_input(INPUT_POST, $role));
            }
        }
        $this->show('/user/update', [
            'user' => $user,
            'role_current_user' => $roleCurrentUser,
            'role_name_user' => $roleNameUser,
            'roles' => $roles,
            'flashes' => $flashes
        ]);
    }

    /**
     * Permet de supprimer un utilisateur
     *
     * @param [type] $userId
     * @return void
     */
    public function delete($userId)
    {
        $flashes = $this->addFlash();
        $user = User::findById($userId);

        if ($user) {
            $user->delete();
            header('Location: /user/read');
        } else {
            $flashes = $this->addFlash('danger', "L'utilisateur n'existe pas!");
        }

        $this->show('/user/read', [
            'user' => $user,
            'flashes' => $flashes
        ]);
    }
}
