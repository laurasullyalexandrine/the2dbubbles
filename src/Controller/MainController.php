<?php

declare(strict_types=1);

namespace App\Controller;

class MainController extends CoreController
{

    /**
     * Afficher la page d'accueil
     *
     * @return void
     */
    public function home(): void
    {
        if (!$this->userIsConnected()) {
            $error403 = new ErrorController;
            $error403->accessDenied();
        } else {
            $this->show('front/main/home');
        }
    }
}
