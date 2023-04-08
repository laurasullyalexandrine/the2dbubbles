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
     * reading des posts
     * @return void
     */
    public function list()
    {
        $posts = Post::findAll();
        
        // On les envoie à la vue
        $this->show('/post/list', [
                'posts' => $posts
            ]);
    }

    /**
     * Ajout d'un nouveau post
     *
     * @return void
     */
    public function create() 
    {
        $flashes = $this->addFlash();
        $post = new Post();
        // Vérifier qu'il y a bien un user connecté
        $session = $_SESSION;
        if (empty($session)) {
            // Sinon le rediriger vers la page de login
            header('Location: /security/login');
        } else {
            // Récupérer le user connecté
            $userCurrent = $session["userObject"];
            // TODO: Ajouter l'access control en fonction du role et la generation du token
            // $userCurrent = User::findBy($id);
            if ($this->isPost()) {
                $title = filter_input(INPUT_POST, 'title');
                $slug = $this->slugify($title);
                $chapo = filter_input(INPUT_POST, 'chapo');
                $content = filter_input(INPUT_POST, 'content');
                $status = (int)filter_input(INPUT_POST, 'status');
                // dd($status);
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
    
                if (empty($flashes["messages"])) {
                    $post->setTitle($title)
                        ->setChapo($chapo)
                        ->setContent($content)
                        ->setStatus($status)
                        ->setSlug($slug);
                    $userId = $userCurrent->getId();
                    $post->setUsers($userId);
                    if ($post->insert()) {
                        header('Location: /post/list');
                        exit;
                    }  else { 
                        // dd($flashes, 'afficher les erreurs');
                        $flashes = $this->addFlash('danger', "Le post n'a pas été créé!");
                    }
                } else {
                    $post->setTitle(filter_input(INPUT_POST, $title));
                    $slug = $this->slugify($title);
                    $post->setChapo(filter_input(INPUT_POST, $chapo));
                    // dd($content);
                    $post->setContent(filter_input(INPUT_POST, 'content'));
                    $post->setStatus(filter_input(INPUT_POST, 'status'));

                    $this->show('/post/create', [
                        'post' => new Post(),
                        'user' => $userCurrent,
                        'flashes' => $flashes
                    ]);
                }
            }
            $this->show('/post/create', [
                    'post' => new Post(),
                    'user' => $userCurrent,
                    'flashes' => $flashes
                ]);
        }
    }


    /**
     * Permet de voir un post et d'y ajouter des commentaires
     *
     * @param string $title
     * @return Post
     */
    public function read(string $slug)
    {
        $post = Post::findBySlug($slug);
        dd($post);

        // On les envoie à la vue
        $this->show('/post/read', [
                'post' => $post
            ]);
            
    }

    public function update($slug)
    {
        $flashes = $this->addFlash();
        $post = Post::findBySlug($slug);

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
    public function delete($slug) 
    {
        $flashes = $this->addFlash();
        $post = Post::findBySlug($slug);

        if ($post) {
            $post->delete();
            $flashes = $this->addFlash('success', "L'article a été supprimé");
            header('Location: /post/read');
            exit;
        } else {
            $flashes = $this->addFlash('danger', "Cet article n'existe pas!");
        }

        $this->show('/post/read', [
            'post' => $post,
            'flashes' => $flashes
        ]);
    }
}
