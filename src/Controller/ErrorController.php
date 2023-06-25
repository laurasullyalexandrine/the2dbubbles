<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\CoreController;

class ErrorController extends CoreController {
    /**
     * Page not found
     *
     * @return void
     */

    
    public function pageNotFoundAction()
    {
        $this->show('error/404');
        return;
    }

    /**
     * Unauthorized page
     *
     * @return void
     */
    public function accessDenied()
    {
        $this->show('error/403');
        return;
    }
    
    /**
     * Server error page
     *
     * @return void
     */
    public function eternalServerError()
    {
        $this->show('error/500');
        return;
    }
}
