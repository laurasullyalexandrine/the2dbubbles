<?php

namespace App\Controller;

class HomeController extends CoreController {
    
    public function home() {
        $this->show('home');
    }
}