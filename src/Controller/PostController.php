<?php

namespace App\Controller;

use App\Models\Post;
/**
 * Controller dédié à la gestion des posts
 */
class PostController extends CoreController
{

    // public function hello($name1, $name2 = null) {
    //     $this->show('hello', [
    //         'name_1' => $name1,
    //         'name_2' => $name2
    //     ]);
    // }
    
    /**
     * Listing des posts
     * @return void
     */
    public function list()
    {

        $posts = Post::findAll();

        // On les envoie à la vue
        $this->show('post/list', [
                'posts' => $posts
            ]);
    }

    /**
     * Ajout d'un nouveau post
     *
     * @return void
     */
    public function add() 
    {
        $this->show('post/add-edit', [
                'post' => new Post()
            ]);
    }

    /**
     * Affiche la vue édition d'un post 
     *
     * @param [type] $postId
     * @return void
     */
    public function edit($postId)
    {
        if ($this->isPost()) {
            dd($_POST);
        }
        
        $post = Post::findBy($postId);

        // On affiche notre vue en transmettant les infos du post
        $this->show('post/add-edit', [
                'post' => $post
            ]);
    }
}
