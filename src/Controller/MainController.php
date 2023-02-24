<?php

namespace App\Controller;

class MainController extends CoreController {
    
    public function homeAction() {
        
        $this->show('home');
    }
}