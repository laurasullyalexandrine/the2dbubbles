<?php

declare(strict_types=1);

namespace App\Controller;

use App\Models\Role;
use Exception;

class RoleController extends CoreController
{
    /**
     * Afficher tous les rôles de la base de données 
     * 
     * @return void
     */
    public function read()
    {
        if (!isset($_SESSION["flashes"])) {
            $flashes = $this->flashes();
        } else {
            $flashes = $_SESSION["flashes"];
        }

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
        $role = new Role();

        if (!$this->userIsConnected()) {
            header('Location: /security/login');
        } else {
            $userCurrent = $this->userIsConnected(); {

                if ($this->isPost()) {
                    $roleName = filter_input(INPUT_POST, 'role');

                    if (empty($roleName)) {
                        $this->flashes('warning', 'Le champ du rôle est vide.');
                    }

                    if (empty($flashes["message"])) {
                        $role->setName($roleName)
                            ->setRoleString('ROLE_' . mb_strtoupper($roleName));

                        if ($role->insert()) {
                            $this->flashes('success', 'Le rôle a bien été créé.');
                            header('Location: /role/read');
                            exit;
                        } else {
                            $this->flashes('danger', "Le rôle n'a pas été créé!");
                        }
                    } else {
                        $role->setName($role);
                        $this->show('role/create', [
                            'user' => $userCurrent
                        ]);
                    }
                }
            }
            $this->show('role/create', [
                'role' => new role(),
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

            if ($this->isPost()) {
                $roleName = filter_input(INPUT_POST, 'role');

                if (empty($roleName)) {
                    $this->flashes('warning', 'Le champ du rôle est vide.');
                }

                if (empty($flashes["message"])) {
                    $role->setName($roleName)
                        ->setRoleString('ROLE_' . mb_strtoupper($roleName));

                    if ($role->update()) {
                        header('Location: /role/read');
                        $this->flashes('success', 'Le rôle a bien été modifié.');
                        exit;
                    } else {
                       $this->flashes('danger', "Le rôle n'a pas été modifié!");
                    }
                } else {
                    $role->setName($roleName);

                    $this->show('role/update', [
                        'user' => $userCurrent
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
        if (!$this->userIsConnected()) {
            header('Location: /security/login');
        } elseif ($this->userIsConnected()->getRoles() !== "Super_admin") {
            $error403 = new ErrorController;
            $error403->accessDenied();
        } else {
            $role = Role::findById($roleId);

            if ($role) {
                $role->delete();
                $this->flashes('success', "Le rôle a bien été supprimé.");
                header('Location: /role/read');
                exit;
            } else {
                $flashes = $this->flashes('danger', "Ce rôle n'existe pas!");
            }

            $this->show('/role/read', [
                'role' => $role,
            ]);
        }
    }
}
