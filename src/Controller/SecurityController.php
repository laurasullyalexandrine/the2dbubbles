<?php

namespace App\Controller;

use App\Models\User;
/**
 * Controller dédié à la gestion des posts
 */
class SecurityController extends CoreController
{

    public function login() {
        $this->show('security/login');
    }
}