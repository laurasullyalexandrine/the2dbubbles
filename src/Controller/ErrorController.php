<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\CoreController;

class ErrorController extends CoreController {
    /**
     * Page non trouvée
     *
     * @return void
     */

    
    public function pageNotFoundAction()
    {
        $this->show('error/404');
        return;
    }

    /**
     * Page non autorisée
     *
     * @return void
     */
    public function accessDenied()
    {
        $this->show('error/403');
        return;
    }
    
    /**
     * Page erreur serveur
     *
     * @return void
     */
    public function eternalServerError()
    {
        $this->show('error/500');
        return;
    }
}
