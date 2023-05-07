<?php

declare(strict_types=1);

namespace App\Controller;

use Exception;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\PHPMailer;

class ContactController extends CoreController {

    public function mailSend() {

        if (!$this->userIsConnected()) {
            // Sinon le rediriger vers la page de login
            header('Location: /security/login');
        } else {
            if ($this->isPost()) {
                $subject = filter_input(INPUT_POST, 'subject');
                $pseudo = filter_input(INPUT_POST, 'pseudo');
                $email = filter_input(INPUT_POST, 'email');
                $message = filter_input(INPUT_POST, 'message');
                
                if (empty($subject)) {
                    $this->flashes('warning', "Ah mais là on n'sait pas de quoi tu parles...");
                }
                
                if (empty($pseudo)) {
                    $this->flashes('warning', "Ton pseudo aussi ?");
                }
                
                if (empty($email)) {
                    $this->flashes('warning', "Ton petit mail pour traiter au mieux ton Bubbles message.");
                }

                if (empty($message)) {
                    $this->flashes('warning', "Ah ?! Mais tu n'as rien à nous dire. Quel dommage...");
                }

                $this->messageSend($subject, $pseudo, $email, $message, [
                    'to', 
                ]);
            }
        }
        $this->show('/front/email/contact');
    }
}