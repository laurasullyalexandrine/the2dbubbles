<?php 

namespace app\Controller;

use App\Models\User;

class UserController extends CoreController {

    /**
     * Listing des user
     * @return void
     */
    public function list()
    {
        // Récupérer tous les users
        $users = User::findAll();

        // On les envoie à la vue
        $this->show('user/list', [
                'users' => $users
            ]
        );
    }

    /**
     * Affiche la vue édition d'un user 
     *
     * @param [type] $userId
     * @return void
     */
    public function edit($userId)
    {
        $user = User::findBy($userId);

        // On affiche notre vue en transmettant les infos du user
        $this->show(
            'user/add-edit',
            [
                'user' => $user
            ]
        );
    }
}