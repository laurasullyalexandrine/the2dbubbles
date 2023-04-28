<?php 

declare(strict_types=1);

namespace App\Controller;

use App\Controller\CoreController;

class AdminController extends CoreController {

    /**
     * Afficher la page admin réservé au rôle Super_admin et l'admin
     *
     * @return void
     */
    public function dashboard() {
        $this->show('admin/dashboard');
    }
}