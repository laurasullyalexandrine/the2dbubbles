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
        $flashes = $_SESSION["flashes"];
        $roles = Role::findAll();
        $this->show('role/read', [
                'roles' => $roles,
                'flashes' => $flashes
            ]);
    }

    /**
     * Ajout d'un nouveau rôle
     * 
     * @return Role
     */
    public function create() 
    {
        $flashes = $this->flashes();
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
                    $flashes = $this->flashes('warning', 'Le champ du rôle est vide');
                }

                if (empty($flashes["message"])) {
                    $role->setName($roleName)
                        ->setRoleString('ROLE_'. mb_strtoupper($roleName));

                    if ($role->insert()) {
                        header('Location: /role/read');
                        $flashes = $this->flashes('success', 'Le rôle a été créé');
                        exit;
                    } else { 
                        $flashes = $this->flashes('danger', "Le rôle n'a pas été créé!"); 
                    }
                } else {
                        $role->setName($role);
                        $this->show('role/create', [
                            'user' => $userCurrent,
                            'flashes' => $flashes,
                        ]);
                    }
                }
            }
        $this->show('role/create', [
                'role' => new role(),
                'flashes' => $flashes
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
        // $flashes = $this->addFlash();
        $role = role::findById($roleId);

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
                    $flashes = $this->flashes('warning', 'Le champ du rôle est vide');
                }

                if (empty($flashes["message"])) {
                    $role->setName($roleName)
                        ->setRoleString('ROLE_'. mb_strtoupper($roleName));

                    if ($role->update()) {
                        header('Location: /role/read');
                        $flashes = $this->flashes('success', 'Le rôle a bien été modifié.');
                        exit;
                    } else {
                        $flashes = $this->flashes('danger', "Le rôle n'a pas été modifié!");
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
        // $flashes = $this->addFlash();
        $role = Role::findById($roleId);

        if ($role) {
            $role->delete();
            header('Location: /role/read');
            $flashes = $this->flashes('success', "Le rôle a bien été supprimé");
            exit;
        } else {
            $flashes = $this->flashes('danger', "Ce rôle n'existe pas!");
        }
        
        $this->show('/role/read', [
            'role' => $role,
            'flashes' => $flashes
        ]);
    }
}