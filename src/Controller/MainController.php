<?php

declare(strict_types=1);

namespace App\Controller;

class MainController extends CoreController
{

    /**
     * Show homepage
     *
     * @return void
     */
    public function home(): void
    {
        if (!$this->userIsConnected()) {
            $this->flashes('warning', 'Merci de te connecter!');
            header('Location: /security/login');
        } else {
            $this->show('front/main/home');
        }
    }
}
