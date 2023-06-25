<?php

declare(strict_types=1);

namespace App\Controller;

class ContactController extends CoreController
{
    /**
     * Method to manage the contact form
     *
     * @return void
     */
    public function mailSend(): void
    {
        if ($this->isPost()) {
            $subject = filter_input(INPUT_POST, 'subject');
            $pseudo = filter_input(INPUT_POST, 'pseudo');
            $email = filter_input(INPUT_POST, 'email');
            $message = filter_input(INPUT_POST, 'message');

            try {
                if (empty($subject)) {
                    throw new \Exception("Ah mais là on n'sait pas de quoi tu parles...");
                }

                if (empty($pseudo)) {
                    throw new \Exception("Ton pseudo aussi ?");
                }

                if (empty($email)) {
                    throw new \Exception("Ton petit mail pour traiter au mieux ton Bubbles message.");
                }

                if (empty($message)) {
                    throw new \Exception("Ah ?! Mais tu n'as rien à nous dire. Quel dommage...");
                }

                $this->messageSend($subject, $pseudo, $email, $message, [
                    'to',
                ]);
            } catch (\Exception $e) {
                $this->flashes('warning', $e->getMessage());
            }
        }
        $this->show('/front/email/contact');
    }
}
