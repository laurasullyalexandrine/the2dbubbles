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
        $this->show('admin/role/list', [
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
        $this->show('admin/role/add', [
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

        $name_role = filter_input(INPUT_POST, 'name_role');
        // dd($name);

        // Récupérer l'id du User en session
        $session = $_SESSION;
        $id = $session['id'];
        // Vérifier l'existence du user
        $userCurrent = User::findBy($id);

        // TODO: Ajouter l'access control en fonction du role et la generation du token

        if (empty($name_role)) {
            $flashes = $this->addFlash('warning', 'Le champ nom est vide');
        }

        if (empty($flashes["messages"]) && $this->isPost()) {
            // dd($flashes, 'créer le role');
            $role = new Role();

            $role->setName($name_role);
            $role->setRoleString('ROLE_'. mb_strtoupper($name_role));
            if ($role->insert()) {
                header('Location: admin/role/list');
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

            $this->show('admin/role/add', [
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
}