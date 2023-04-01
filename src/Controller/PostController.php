<?php

namespace App\Controller;

use App\Models\Post;
use App\Models\User;
/**
 * Controller dédié à la gestion des posts
 */
class PostController extends CoreController
{
    
    /**
     * Ajout d'un nouveau post
     *
     * @return void
     */
    public function create() 
    {
        $this->show('/post/create', [
                'post' => new Post()
            ]);
    }

    public function createPostPost() 
    {
        $flashes = $this->addFlash();

        $title = filter_input(INPUT_POST, 'title');
        $chapo = filter_input(INPUT_POST, 'chapo');
        $content = filter_input(INPUT_POST, 'content');
        $status = filter_input(INPUT_POST, 'status');

        // Récupérer l'id du User en session
        $session = $_SESSION;
        $id = $session['id'];
        // Vérifier l'existence du user
        $userCurrent = User::findBy($id);

        // TODO: Ajouter l'access control en fonction du role et la generation du token
        
            if (empty($title)) {
                $flashes = $this->addFlash('warning', 'Le champ titre est vide');
            }
    
            if (empty($chapo)) {
                $flashes = $this->addFlash('warning', 'Le champ chapô est vide');
            }
            if (empty($content)) {
                $flashes = $this->addFlash('warning', 'Le champ contenu est vide');
            }
    
            if (empty($status)) {
                $flashes = $this->addFlash('warning', 'Choisir un status');
            }
        

        if (empty($flashes["messages"]) && $this->isPost()) {

            $post = new Post();
            $post->setTitle($title)
                ->setChapo($chapo)
                ->setContent($content)
                ->setStatus($status);
            // dd($post);
            if ($post->insert()) {
                header('Location: /post/read');
                exit;
            }  else { 
                // dd($flashes, 'afficher les erreurs');
                $flashes = $this->addFlash('danger', "Le rôle n'a pas été créé!");
                exit; 
            }
        } else {
            // dd($flashes, 'si erreur dans le traitement du formulaire');
            $post = new Post();
            $post->setTitle(filter_input(INPUT_POST, 'title'));
            $post->setChapo(filter_input(INPUT_POST, 'chapo'));
            $post->setContent(filter_input(INPUT_POST, 'content'));
            $post->setStatus(filter_input(INPUT_POST, 'status'));

            $this->show('/post/create', [
                'user' => $userCurrent,
                'flashes' => $flashes
            ]);
        }
    }

    /**
     * reading des posts
     * @return void
     */
    public function read()
    {
        $posts = Post::findAll();

        // On les envoie à la vue
        $this->show('/post/read', [
                'posts' => $posts
            ]);
    }

    /**
     * Affiche la vue édition d'un post 
     *
     * @param [type] $postId
     * @return void
     */
    public function update($postId)
    {
        $post = Post::findBy($postId);

        // On affiche notre vue en transmettant les infos du post
        $this->show('/post/update', [
                'post' => $post
            ]);
    }

    /**
     * Permet de supprimer un post
     *
     * @param [type] $postId
     * @return void
     */
    public function delete($postId) 
    {
        $flashes = $this->addFlash();

        $post = Post::findBy($postId);

        if ($post) {
            $post->delete();

            $flashes = $this->addFlash('success', "L'article a été supprimé");
            header('Location: /post/read');
        } else {
            $flashes = $this->addFlash('danger', "Cet article n'existe pas!");
        }

        $this->show('/post/read', [
            'post' => $post,
            'flashes' => $flashes
        ]);
    }
}
