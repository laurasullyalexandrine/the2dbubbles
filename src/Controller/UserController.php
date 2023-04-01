<?php 

namespace App\Controller;

use App\Models\Role;
use App\Models\User;

class UserController extends CoreController {

    /**
     * reading des users
     * @return void
     */
    public function read()
    {
        $users = User::findAll();
        $this->show('user/read', [
                'users' => $users
            ]
        );
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
            $password_1 = filter_input(INPUT_POST, 'password_1');
            $password_2 = filter_input(INPUT_POST, 'password_2');
            // Contraindre le type de la valeur soumis 
            $role = (int)filter_input(INPUT_POST, 'role');
            $user->setEmail($email);
            $user->setRoles($role);
    
            // Vérifier que tous les champs ne sont pas vide 
            if (empty($email)) {
                $flashes = $this->addFlash('warning', 'Le champ email est vide');
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

            // Controler si le rôle soumis est un rôle existant en BDD 
            $roleExist = false;
            foreach ($roles as $existingRole) {
                // Si l'id du rôle soumis alors existe en base de données
                if ($existingRole->getId() === $role) {
                    $roleExist = true;
                    break;
                }
            }
            
            // Si il n'existe pas on affiche le message d'alerte
            if (!$roleExist) {
                $flashes = $this->addFlash('warning', 'Donnée du select invalide'); 
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
                } catch(\Exception $e) { // Attrapper l'exception 23000 qui correspond du code Unique de MySQL (avant ça il indiquer dans la bdd quel champ est 'unique')
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
     * Affiche la vue édition d'un user 
     *
     * @param [type] $userId
     * @return void
     */
    public function update($userId)
    {
        $flashes = $this->addFlash();

        $user = User::findBy($userId);


        $roleName = filter_input(INPUT_POST, 'role');
        dd($roleName);

        // Récupérer l'id du User en session
        $session = $_SESSION;
        $id = $session['id'];
        // Vérifier l'existence du user
        $userCurrent = User::findBy($id);

        if (empty($roleName)) {
            $flashes = $this->addFlash('warning', 'Le champ du rôle est vide');
        }

        if (empty($flashes["messages"]) && $this->isPost()) {
            dd($roleName);
            $role = Role::findBy($userId);
            $role->setName($roleName)
                ->setRoleString('ROLE_'. mb_strtoupper($roleName));

            if ($role->update()) {
                header('Location: /role/read');
                exit;
            } else {
                $flashes = $this->addFlash('danger', "Le rôle n'a pas été modifié!");
                exit;
            }
        } else {
            $role = new Role();
            $role->setName(filter_input(INPUT_POST, 'name_role'));

            $this->show('role/update', [
                'user' => $userCurrent,
                'flashes' => $flashes
            ]);
        }


        $this->show('user/update', [
                'user' => $user
            ]);
    }

    public function delete($userId) 
    {
        dump($userId);
        $flashes = $this->addFlash();

        $user = User::findBy($userId);
dd($user);
        if ($user) {
            $user->delete();

            $flashes = $this->addFlash('success', "L'utilisateur a été supprimé");
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