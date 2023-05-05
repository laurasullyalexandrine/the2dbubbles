<?php

declare(strict_types=1);

namespace App\Controller;

class ContactController extends CoreController {

    public function send() {
        // dd($this->userIsConnected());
        $this->show('/front/main/contact');
    }
}