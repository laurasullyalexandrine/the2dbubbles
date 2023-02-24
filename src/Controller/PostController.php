<?php 

namespace App\Controller;

class PostController extends CoreController {
    
    public function hello($name1, $name2 = null) {
        $this->show('hello', [
            'name_1' => $name1,
            'name_2' => $name2
        ]);
    }
}