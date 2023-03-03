<?php 

namespace App\Controller;

use App\Models\Role;

class RoleController extends CoreController {
    
    /**
     * Listing des roles
     * @return void
     */
    public function list()
    {
        $roleObject = new Role();
        $roles = $roleObject->findAll();

        $this->show(
            'role/list',
            [
                'roles' => $roles
            ]
        );
    }

    /**
     * Ajout d'un nouveau role
     *
     * @return void
     */
    public function add() 
    {
        $this->show(
            'role/add-edit',
            [
                'role' => new role()
            ]
        );
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

        $this->show(
            'role/add-edit',
            [
                'role' => $role
            ]
        );
    }
}