<?php 

declare(strict_types=1);

namespace App\Controller;

use App\Models\Role;
use App\Models\User;

class RoleController extends CoreController {
    
    /**
     * Afficher tous les rôles de la base de données 
     * 
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
     * Ajout d'un nouveau rôle
     * 
     * @return Role
     */
    public function create() 
    {
        $flashes = $this->addFlash();
        $role = new Role();

        if (!$this->userIsConnected()) {
            // Sinon le rediriger vers la page de login
            header('Location: /security/login');
        } else {
            // Récupérer le user connecté
            $userCurrent = $this->userIsConnected(); {

            // TODO: Ajouter l'access control en fonction du role et la generation du token

            if ($this->isPost()) {
                $roleName = filter_input(INPUT_POST, 'role');

                if (empty($roleName)) {
                    $flashes = $this->addFlash('warning', 'Le champ du rôle est vide');
                }
            
                if (empty($flashes["messages"])) {
                    $role->setName($roleName)
                        ->setRoleString('ROLE_'. mb_strtoupper($roleName));

                    if ($role->insert()) {
                        header('Location: /role/read');
                        exit;
                    }  else { 
                        $flashes = $this->addFlash('danger', "Le rôle n'a pas été créé!");
                        exit; 
                    }
                } else {
                        $role->setName($role);
                        $this->show('role/create', [
                            'user' => $userCurrent,
                            'flashes' => $flashes
                        ]);
                    }
                }
            }
        $this->show('role/create', [
                'role' => new role()
            ]);
    } 
}

    /**
     * Édition d'un rôle 
     * 
     * @param [type] $roleId
     * @return Role
     */
    public function update(int $roleId)
    {
        $flashes = $this->addFlash();
        $role = role::findBy($roleId);

        // Vérifier qu'il y a bien un user connecté
        if (!$this->userIsConnected()) {
            // Sinon le rediriger vers la page de login
            header('Location: /security/login');
        } else {
            // Récupérer le user connecté
            $userCurrent = $this->userIsConnected();
        
             // TODO: Ajouter l'access control en fonction du role et la generation du token
            if ($this->isPost()) {
                $roleName = filter_input(INPUT_POST, 'role');

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
                    $role->setName($roleName);

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
     * Suppression d'un rôle
     *
     * @param [type] $roleId
     * @return void
     */
    public function delete(int $roleId) 
    {
        $flashes = $this->addFlash();
        $role = Role::findBy($roleId);

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