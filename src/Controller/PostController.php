<?php

namespace App\Controller;

use App\Models\Post;

class PostController extends CoreController
{

    // public function hello($name1, $name2 = null) {
    //     $this->show('hello', [
    //         'name_1' => $name1,
    //         'name_2' => $name2
    //     ]);
    // }

    public function list()
    {

        // On récupère tous les produits
        $postObject = new Post();
        // Récupérer tous les posts
        $posts = $postObject->findAll();

        // On les envoie à la vue
        $this->show(
            'post/list',
            [
                'posts' => $posts
            ]
        );
    }

    public function edit($postId)
    {
        $post = Post::findBy($postId);

        // On affiche notre vue en transmettant les infos du post
        $this->show(
            'post/add-edit',
            [
                'post' => $post
            ]
        );
    }
}
