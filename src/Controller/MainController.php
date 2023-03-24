<?php

namespace App\Controller;

class MainController extends CoreController {
    
    public function home() {
        $this->show('main/home');
    }

    public function displayContact() {
        $this->show('main/contact');
    }

    public function contact() {
        if ($this->isPost()) {

            dump($_SERVER['REQUEST_METHOD']);
            $firstname = filter_input(INPUT_POST, 'firstname');
            $lastname = filter_input(INPUT_POST, 'lastname');
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $message = filter_input(INPUT_POST, 'message');
            dd($firstname, $lastname, $email, $message);
        }


    }
}