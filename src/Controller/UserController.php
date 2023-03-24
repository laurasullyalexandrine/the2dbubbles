<?php 

namespace App\Controller;

use App\Models\User;

class UserController extends CoreController {

    /**
     * Listing des users
     * @return void
     */
    public function list()
    {
        $users = User::findAll();
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
        $this->show('user/edit', [
                'user' => $user
            ]);
    }

    
}