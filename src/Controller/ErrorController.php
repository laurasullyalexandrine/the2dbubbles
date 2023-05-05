<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\CoreController;

class ErrorController extends CoreController {
    // Page non trouvée : erreur 404
    public function pageNotFoundAction()
    {
        // header("HTTP/1.0 404 Not Found");
        $this->show('error/404');
        exit;
    }

    public function accessDenied()
    {
        // header('HTTP/1.0 403 Unauthorized');
        $this->show('error/403');
        exit;
    }
}