<?php 

namespace App\Controller;

use App\Models\Role;
use App\Models\User;

class RoleController extends CoreController {
    
    /**
     * reading des roles
     * @return void
     */
    public function read()
    {
        $roles = Role::findAll();
        $this->show('role/read', [
                'roles' => $roles
            ]);
    }

    /**
     * Page d'ajout de role méthode GET
     *
     * @return void
     */
    public function create() 
    {
        $flashes = $this->addFlash();

        $roleName = filter_input(INPUT_POST, 'role');

        // Récupérer l'id du User en session
        $session = $_SESSION;
        $id = $session['id'];
        // Vérifier l'existence du user
        $userCurrent = User::findBy($id);

        // TODO: Ajouter l'access control en fonction du role et la generation du token

        if (empty($roleName)) {
            $flashes = $this->addFlash('warning', 'Le champ du rôle est vide');
        }

        if (empty($flashes["messages"]) && $this->isPost()) {
            // dd($flashes, 'créer le role');
            $role = new Role();

            $role->setName($roleName)
                ->setRoleString('ROLE_'. mb_strtoupper($roleName));
            if ($role->insert()) {
                header('Location: /role/read');
                exit;
            }  else { 
                // dd($flashes, 'afficher les erreurs');
                $flashes = $this->addFlash('danger', "Le rôle n'a pas été créé!");
                exit; 
            }
        } else {
            // dd($flashes, 'si erreur dans le traitement du formulaire');
            $role = new Role();
            $role->setName(filter_input(INPUT_POST, 'name_role'));

            $this->show('role/create', [
                'user' => $userCurrent,
                'flashes' => $flashes
            ]);
        }
        $this->show('role/create', [
                'role' => new role()
            ]);
    }

    /**
     * Éditer un rôle 
     *
     * @param [type] $roleId
     * @return void
     */
    public function update($roleId)
    {
        $flashes = $this->addFlash();
        $role = role::findBy($roleId);

        $roleName = filter_input(INPUT_POST, 'role');

        // TODO: Ajouter l'accès contrôle par rôle
        // Récupérer l'id du User en session
        $session = $_SESSION;

        if (empty($session)) {
            header('Location: /security/login');
        } else {
            $userId = $session['id'];
            // Vérifier l'existence du user
            $userCurrent = User::findBy($userId);
    
            if ($this->isPost()) {
                if (empty($roleName)) {
                    $flashes = $this->addFlash('warning', 'Le champ du rôle est vide');
                }
                if (empty($flashes["messages"])) {
                    $role->setName($roleName)
                        ->setRoleString('ROLE_'. mb_strtoupper($roleName));
                    if ($role->update()) {
                        header('Location: /role/read');
                        exit;
                    } else {
                        $flashes = $this->addFlash('danger', "Le rôle n'a pas été modifié!");
                    }
                } else {
                    $role->setName(filter_input(INPUT_POST, $roleName));
        
                    $this->show('role/update', [
                        'user' => $userCurrent,
                        'flashes' => $flashes
                    ]);
                }
            }
        }
        $this->show('/role/update', [
                'role' => $role
            ]);
    }


    /**
     * Permet de supprimer un rôle
     *
     * @param [type] $roleId
     * @return void
     */
    public function delete($roleId) 
    {
        $flashes = $this->addFlash();

        $role = Role::findBy($roleId);
        // dd($role);
        if ($role) {
            $role->delete();
            header('Location: /role/read');
            $flashes = $this->addFlash('success', "Le rôle a été supprimé");
            echo $flashes;
        } else {
            $flashes = $this->addFlash('danger', "Ce rôle n'existe pas!");
        }

        $this->show('/role/read', [
            'role' => $role,
            'flashes' => $flashes
        ]);
    }
}