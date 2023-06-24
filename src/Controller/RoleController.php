<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Role;
use App\Repository\RoleRepository;
use Exception;

class RoleController extends CoreController
{
    protected RoleRepository $roleRepository;
    public function __construct()
    {
        $this->roleRepository = new RoleRepository();
    }
    
    /**
     * Afficher tous les rôles de la base de données au role super_admin et l'admin
     * 
     * @return void
     */
    public function read()
    {
        if (!$this->userIsConnected()) {
            $error403 = new ErrorController;
            $error403->accessDenied();
        } else {
            $roles = $this->roleRepository->findAll();
            $this->show('admin/role/read', [
                'roles' => $roles
            ]);
        }
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
        $currentUserRole = $this->roleRepository->findById($userCurrent->getRoleId());
        if (!$this->userIsConnected()) {
            $error403 = new ErrorController;
            $error403->accessDenied();
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

                        if ($this->roleRepository->insert($role)) {
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
        $role = $this->roleRepository->findById($roleId);

        $currentUserRole = $this->roleRepository->findById($this->userIsConnected()->getRoleId());

        if (!$this->userIsConnected()) {
            $error403 = new ErrorController;
            $error403->accessDenied();
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

                    if ($this->roleRepository->update($role)) {
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
        $role = $this->roleRepository->findById($roleId);

        $currentUserRole = $this->roleRepository->findById($this->userIsConnected()->getRoleId());

        if (!$this->userIsConnected()) {
            $error403 = new ErrorController;
            $error403->accessDenied();
        } elseif ($currentUserRole->getName() !== "super_admin") {
            $error403 = new ErrorController;
            $error403->accessDenied();
        } else {
            if ($role) {
                $this->roleRepository->delete($roleId);
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
