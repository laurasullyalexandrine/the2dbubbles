<?php

declare(strict_types=1);

namespace App\Controller;

use Exception;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\PHPMailer;

class ContactController extends CoreController {

    public function message_send() {

        if (!$this->userIsConnected()) {
            // Sinon le rediriger vers la page de login
            header('Location: /security/login');
        } else {

            if ($this->isPost()) {
    
                $subject = filter_input(INPUT_POST, 'subject');
                $pseudo = filter_input(INPUT_POST, 'pseudo');
                $email = filter_input(INPUT_POST, 'email');
                $message = filter_input(INPUT_POST, 'message');
                
                // true permet d'engager la gestion des exceptions
                $mail = new PHPMailer(true);
        
                // Gestion des exceptions
                try {
                    // Configuration (envoyer des emails php 15'40)
                    // $mail->SMTPDebug = SMTP::DEBUG_SERVER;
                    // Permet d'afficher les informations de debug
        
                    // Configuration de SMTP
                    $mail->isSMTP();
                    $mail->Host = "localhost";
                    $mail->Port = 1025; // Port de MailHog
        
                    // Charset 
                    $mail->CharSet = "utf-8";
        
                    // Destinataire
                    $mail->addAddress("contact@2dbubbles.fr");
        
                    // Expéditeur
                    $mail->setFrom($email);
                    
                    // Contenu du message
                    $mail->isHTML(); // Permet d'ajouter des balises HTML
                    $mail->Subject = $subject;
                    $mail->Body = '<p class="contact">$message</p>';
                
                    // Envoyer le mail
                    $mail->send();
                    $this->flashes('success', 'Ton Bubbles message a bien été envoyé.');
                } catch(Exception){
                    $this->flashes('danger', "Ton Bubbles message n'a pas été envoyé!");
                    // header('Location: /email/contact/mail_not_send');
                    echo "Message non envoyé. Erreur{$mail->ErrorInfo}";
                }
            }
        }

        $this->show('/front/email/contact');
    }
}