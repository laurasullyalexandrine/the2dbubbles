<?php

declare(strict_types=1);

namespace App\Controller;

class MainController extends CoreController {
    
    /**
     * Afficher la page d'accueil
     *
     * @return void
     */
    public function home() {
        $this->show('main/home');
    }

    public function displayContact() {
        $this->show('main/contact');
    }

    /**
     * Traitement du formulaire de contact
     * @return void
     */
    public function contact() {

        if ($this->isPost()) {

            // dump($_SERVER['REQUEST_METHOD']);
            // $firstname = filter_input(INPUT_POST, 'firstname');
            // $lastname = filter_input(INPUT_POST, 'lastname');
            // $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            // $message = filter_input(INPUT_POST, 'message');
            // dd($firstname, $lastname, $email, $message);
        }

        $this->show('main/contact');


    }
}