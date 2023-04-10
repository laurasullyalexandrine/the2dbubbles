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

        if (!$this->userIsConnected()) {
            // Sinon le rediriger vers la page de login
            header('Location: /security/login');
        } else {
            // Récupérer le user connecté
            $userCurrent = $this->userIsConnected();
          
            // TODO: Ajouter l'access control en fonction du role et la generation du token

            if ($this->isPost()) {
                $title = filter_input(INPUT_POST, 'title');
                $slug = $this->slugify($title);
                $chapo = filter_input(INPUT_POST, 'chapo');
                $content = filter_input(INPUT_POST, 'content');
                // $status = (int)filter_input(INPUT_POST, 'status');

                if (empty($title)) {
                    $flashes = $this->addFlash('warning', 'Le champ titre est vide');
                }
                if (empty($chapo)) {
                    $flashes = $this->addFlash('warning', 'Le champ chapô est vide');
                }
                if (empty($content)) {
                    $flashes = $this->addFlash('warning', 'Le champ contenu est vide');
                }
                if (empty($flashes["messages"])) {
                    $post->setTitle($title)
                        ->setChapo($chapo)
                        ->setContent($content)
                        ->setStatus(0)
                        ->setSlug($slug);
                    $userId = $userCurrent->getId();
                    $post->setUsers($userId);
               
                    if ($post->insert()) {
                        header('Location: /post/list');
                        exit;
                    } else {
                        $flashes = $this->addFlash('danger', "Le post n'a pas été créé!");
                    }
                } else {
                    // dd($flashes);
                    $post->setTitle(filter_input(INPUT_POST, $title));
                    $slug = $this->slugify($title);
                    $post->setChapo(filter_input(INPUT_POST, $chapo));
                    $post->setContent(filter_input(INPUT_POST, $content));
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

        // On les envoie à la vue
        $this->show('/post/read', [
            'post' => $post
        ]);
    }

    public function update($slug)
    {
        $flashes = $this->addFlash();
        $post = Post::findBySlug($slug);

        // Vérifier qu'il y a bien un user connecté
        if (!$this->userIsConnected()) {
            // Sinon le rediriger vers la page de login
            header('Location: /security/login');
        } else {
            // Récupérer le user connecté
            $userCurrent = $this->userIsConnected();

            // TODO: Ajouter l'access control en fonction du role et la generation du token

            if ($this->isPost()) {
                $title = filter_input(INPUT_POST, 'title');
                $slug = $this->slugify($title);
                $chapo = filter_input(INPUT_POST, 'chapo');
                $content = filter_input(INPUT_POST, 'content');
                $status = (bool)filter_input(INPUT_POST, 'status');
                
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
                        ->setSlug($title);

                    $userId = $userCurrent->getId();
                    $post->setUsers($userId);

                    if ($post->update()) {
                        header('Location: /post/list');
                        exit;
                    } else {
                        $flashes = $this->addFlash('danger', "L'article n'a pas été modifié!");
                    }
                }
            }
        }
        // On affiche notre vue en transmettant les infos du post et des messages d'alerte
        $this->show('/post/update', [
            'post' => $post,
            'flashes' => $flashes
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
            header('Location: /post/list');
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
