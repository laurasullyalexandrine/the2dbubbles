<?php

declare(strict_types=1);

namespace App\Controller;

use App\Models\Comment;
use App\Models\Post;

/**
 * Controller dédié à la gestion des posts
 */
class PostController extends CoreController
{
    /**
     * Afficher tous les artilces de la base de données
     * 
     * @return void
     */
    public function list()
    {
        if (!isset($_SESSION["flashes"])) {
            $flashes = $this->flashes();
        } else {
            $flashes = $_SESSION["flashes"];
        }
        $posts = Post::findAll();

        // On les envoie à la vue
        $this->show('/post/list', [
            'posts' => $posts,
            'flashes' => $flashes
        ]);
    }

    /**
     * Ajout d'un nouveau post
     * 
     * @return Post
     */
    public function create()
    {
        $flashes = $this->flashes();
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

                $post->setTitle($title)
                    ->setSlug($slug)
                    ->setContent($content)
                    ->setChapo($chapo);

                if (empty($title)) {
                    $flashes = $this->flashes('warning', 'Le champ titre est vide');
                }
                if (empty($chapo)) {
                    $flashes = $this->flashes('warning', 'Le champ chapô est vide');
                }
                if (empty($content)) {
                    $flashes = $this->flashes('warning', 'Le champ contenu est vide');
                }
                if (empty($flashes["message"])) {

                    $userId = $userCurrent->getId();
                    $post->setUsers($userId);

                    if ($post->insert()) {
                        header('Location: /post/list');
                        $flashes = $this->flashes('success', "L'article a bien été créé");
                        exit;
                    } else {
                        $flashes = $this->flashes('danger', "L'article n'a pas été créé!");
                    }
                } else {
                    $post->setTitle($title);
                    $post->setChapo($chapo);
                    $post->setContent($content);

                    $this->show('/post/create', [
                        'post' => $post,
                        'user' => $userCurrent,
                        'flashes' => $flashes
                    ]);
                }
            }
            $this->show('/post/create', [
                'post' => new Post(),
                'user' => $userCurrent
            ]);
        }
    }

    /**
     * Voir un Post et ses commentaires
     *
     * @param string $title
     * @return Post
     */
    public function read(string $slug)
    {
        $post = Post::findBySlug($slug);
        $postId = $post->getId();

        // Récupérer les tableaux des commentaires
        $comments = Comment::findBySlugPost($slug);
        $commentsCheck = [];

        foreach ($comments as $comment) {
            if ($comment->getStatus() === 1) {
                $commentsCheck[] = $comment;
            }
        }

        // On les envoie à la vue
        $this->show('/post/read', [
            'post' => $post,
            'comments' => $comments,
            'commentsCheck' => $commentsCheck
        ]);
    }

    /**
     * Édition d'un Post (article)
     *
     * @param string $slug
     * @return Post
     */
    public function update(string $slug)
    {
        // $flashes = $this->flashes();
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

                if (empty($title)) {
                    $flashes = $this->flashes('warning', 'Le champ titre est vide');
                }
                if (empty($chapo)) {
                    $flashes = $this->flashes('warning', 'Le champ chapô est vide');
                }
                if (empty($content)) {
                    $flashes = $this->flashes('warning', 'Le champ contenu est vide');
                }

                if (empty($flashes["messages"])) {

                    $post->setTitle($title)
                    ->setChapo($chapo)
                    ->setContent($content)
                    ->setSlug($slug);

                    $userId = $userCurrent->getId();
                    $post->setUsers($userId);

                    if ($post->update()) {
                        header('Location: /post/list');
                        exit;
                    } else {
                        $flashes = $this->flashes('danger', "L'article n'a pas été modifié!");
                    }
                } else {
                    $slug = $this->slugify($title);

                    $this->show('post/update', [
                        'post' => $post,
                        'flashes' => $flashes
                    ]);
                }
            }
        }
        // On affiche notre vue en transmettant les infos du post et des messages d'alerte
        $this->show('/post/update', [
            'post' => $post,
        ]);
    }

    /**
     * Suppression d'un post
     *
     * @param [type] $postId
     * @return void
     */
    public function delete(string $slug)
    {
        // $flashes = $this->flashes();
        $post = Post::findBySlug($slug);

        if ($post) {
            // $flashes = $this->flashes('success', "L'article a été supprimé");
            $post->delete();
            header('Location: /post/list');
            exit;
        } else {
            $flashes = $this->flashes('danger', "Cet article n'existe pas!");
        }

        $this->show('/post/read', [
            'post' => $post,
            'flashes' => $flashes
        ]);
    }
}
