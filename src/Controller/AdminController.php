<?php 

declare(strict_types=1);

namespace App\Controller;

use App\Controller\CoreController;

class AdminController extends CoreController {

    /**
     * Afficher la page admin
     *
     * @return void
     */
    public function dashboard() {
        $session = $_SESSION;
        $this->show('admin/dashboard', [
            'sessions' => $session
        ]);
    }
}