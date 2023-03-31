<?php 

namespace App\Controller;

use App\Models\Role;
use App\Models\User;

class RoleController extends CoreController {
    
    /**
     * Listing des roles
     * @return void
     */
    public function list()
    {
        $roles = Role::findAll();
        $this->show('role/list', [
                'roles' => $roles
            ]);
    }

    /**
     * Page d'ajout de role méthode GET
     *
     * @return void
     */
    public function add() 
    {
        $this->show('role/add', [
                'role' => new role()
            ]);
    }

    /**
     * Ajout d'un role méthode POST
     *
     * @return void
     */
    public function addRole() 
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
                header('Location: /role/list');
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

            $this->show('role/add', [
                'user' => $userCurrent,
                'flashes' => $flashes
            ]);
        }

    }

    /**
     * Affiche la vue édition d'un role 
     *
     * @param [type] $roleId
     * @return void
     */
    public function edit($roleId)
    {
        $role = role::findBy($roleId);
        $this->show('role/edit', [
                'role' => $role
            ]);
    }

    public function editRole($roleId) 
    {
        $flashes = $this->addFlash();

        $roleName = filter_input(INPUT_POST, 'role');

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
            $role = Role::findBy($roleId);
            $role->setName($roleName)
                ->setRoleString('ROLE_'. mb_strtoupper($roleName));

            if ($role->update()) {
                header('Location: /role/list');
                exit;
            } else {
                $flashes = $this->addFlash('danger', "Le rôle n'a pas été modifié!");
                exit;
            }
        } else {
            $role = new Role();
            $role->setName(filter_input(INPUT_POST, 'name_role'));

            $this->show('role/add', [
                'user' => $userCurrent,
                'flashes' => $flashes
            ]);
        }
    }

    public function delete($roleId) 
    {
        $flashes = $this->addFlash();

        $role = Role::findBy($roleId);

        if ($role) {
            $role->delete();

            $flashes = $this->addFlash('success', "Le rôle a été supprimé");
            header('Location: /role/list');
        } else {
            $flashes = $this->addFlash('danger', "Le rôle n'existe pas!");
        }

        $this->show('/role/list', [
            'role' => $role,
            'flashes' => $flashes
        ]);
    }
}