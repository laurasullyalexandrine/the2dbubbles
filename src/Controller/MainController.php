<?php

declare(strict_types=1);

namespace App\Controller;

class MainController extends CoreController {
    
    /**
     * Afficher la page d'accueil
     *
     * @return void
     */
    public function home(): void
    {
        $this->show('front/main/home');
    }
}
