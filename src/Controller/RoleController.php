<?php

declare(strict_types=1);

namespace App\Controller;

use App\Models\Role;
use Exception;

class RoleController extends CoreController
{

    
    /**
     * Afficher tous les rôles de la base de données au role super_admin et l'admin
     * 
     * @return void
     */
    public function read()
    {
        $roles = Role::findAll();
        $this->show('admin/role/read', [
            'roles' => $roles
        ]);
    }

    /**
     * Ajout d'un nouveau rôle réservé au role super_admin
     * 
     * @return void
     */
    public function create(): void
    {
        $role = new Role();
        $userCurrent = $this->userIsConnected();
        $currentUserRole = Role::findById($userCurrent->getRoleId());
        if (!$userCurrent) {
            header('Location: /security/login');
        } elseif ($currentUserRole->getName() !== "super_admin") {
            $error403 = new ErrorController;
            $error403->accessDenied();
        } else {
            $userCurrent = $this->userIsConnected(); {

                if ($this->isPost()) {
                    $roleName = filter_input(INPUT_POST, 'role');

                    if (empty($roleName)) {
                        $this->flashes('warning', 'Le champ du rôle est vide.');
                    }

                    if (empty($_SESSION["flashes"])) {
                        $role->setName($roleName)
                            ->setRoleString('ROLE_' . mb_strtoupper($roleName));

                        if ($role->insert()) {
                            $this->flashes('success', 'Le rôle a bien été créé.');
                            header('Location: /role/read');
                            return;
                        } else {
                            $this->flashes('danger', "Le rôle n'a pas été créé!");
                        }
                    } else {
                        $role->setName($roleName);
                        $this->show('admin/role/create', [
                            'user' => $userCurrent
                        ]);
                    }
                }
            }
            $this->show('/admin/role/create', [
                'role' => new role(),
            ]);
        }
    }

    /**
     * Édition d'un rôle réservé au role super_admin
     * 
     * @param [type] $roleId
     * @return void
     */
    public function update(int $roleId): void
    {
        $role = role::findById($roleId);

        $currentUserRole = Role::findById($this->userIsConnected()->getRoleId());

        if (!$this->userIsConnected()) {
            header('Location: /security/login');
        } elseif ($currentUserRole->getName() !== "super_admin") {
            $error403 = new ErrorController;
            $error403->accessDenied();
        } else {
            // Récupérer le user connecté
            $userCurrent = $this->userIsConnected();

            if ($this->isPost()) {
                $roleName = filter_input(INPUT_POST, 'role');

                if (empty($roleName)) {
                    $this->flashes('warning', 'Le champ du rôle est vide.');
                }

                if (empty($_SESSION["flashes"])) {
                    $role->setName($roleName)
                        ->setRoleString('ROLE_' . mb_strtoupper($roleName));

                    if ($role->update()) {
                        header('Location: /role/read');
                        $this->flashes('success', 'Le rôle ' . $role->getName() . ' a bien été modifié.');
                        return;
                    } else {
                        $this->flashes('danger', 'Le rôle ' . $role->getName() . ' n\'a pas été modifié!');
                    }
                } else {
                    $role->setName($roleName);

                    $this->show('admin/role/update', [
                        'user' => $userCurrent
                    ]);
                }
            }
        }
        $this->show('/admin/role/update', [
            'role' => $role
        ]);
    }

    /**
     * Suppression d'un rôle au role super_admin
     *
     * @param [type] $roleId
     * @return void
     */
    public function delete(int $roleId)
    {
        $role = Role::findById($roleId);

        $currentUserRole = Role::findById($this->userIsConnected()->getRoleId());

        if (!$this->userIsConnected()) {
            header('Location: /security/login');
        } elseif ($currentUserRole->getName() !== "super_admin") {
            $error403 = new ErrorController;
            $error403->accessDenied();
        } else {
            if ($role) {
                $role->delete();
                $this->flashes('success', 'Le Bubbles Role' . ' ' . $role->getName() . ' ' . 'a bien été supprimé.');
                header('Location: /role/read');
                return;
            } else {
                $this->flashes('danger', "Ce Bubbles Role n'existe pas!");
            }

            $this->show('admin/role/read', [
                'role' => $role,
            ]);
        }
    }
}
