<?php

declare(strict_types=1);

namespace App\Controller;

use App\Models\Post;
use App\Models\Role;
use App\Models\Comment;

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
        $posts = Post::findAll();

        // On les envoie à la vue
        $this->show('/post/list', [
            'posts' => $posts
        ]);
    }

    /**
     * Ajout d'un nouveau post
     * 
     * @return Post
     */
    public function create()
    {
        $post = new Post();
        $currentUserRole = Role::findById($this->userIsConnected()->getRoles());
        if (!$this->userIsConnected()) {
            // Sinon le rediriger vers la page de login
            header('Location: /security/login');
        } elseif($currentUserRole->getName() !== "Super_admin") {
            $error403 = new ErrorController;
            $error403->accessDenied(); 
        } else {
            // Récupérer le user connecté
            $userCurrent = $this->userIsConnected();

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
                    $this->flashes('warning', 'Le champ titre est vide.');
                }
                if (empty($chapo)) {
                    $this->flashes('warning', 'Le champ chapô est vide.');
                }
                if (empty($content)) {
                    $this->flashes('warning', 'Le champ contenu est vide.');
                }
                if (empty($flashes["message"])) {

                    $userId = $userCurrent->getId();
                    $post->setUsers($userId);

                    if ($post->insert()) {
                        $this->flashes('success', "L'article a bien été créé.");
                        header('Location: /post/list');
                        exit;
                    } else {
                        $this->flashes('danger', "L'article n'a pas été créé!");
                    }
                } else {
                    $post->setTitle($title);
                    $post->setChapo($chapo);
                    $post->setContent($content);

                    $this->show('admin/post/create', [
                        'post' => $post
                    ]);
                }
            }
            $this->show('admin/post/create', [
                'post' => $post
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
        $post = Post::findBySlug($slug);

        $currentUserRole = Role::findById($this->userIsConnected()->getRoles());
        if (!$this->userIsConnected()) {
            // Sinon le rediriger vers la page de login
            header('Location: /security/login');
        } elseif($currentUserRole->getName() !== "Super_admin") {
            $error403 = new ErrorController;
            $error403->accessDenied(); 
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
                    $this->flashes('warning', 'Le champ titre est vide.');
                }
                if (empty($chapo)) {
                    $this->flashes('warning', 'Le champ chapô est vide.');
                }
                if (empty($content)) {
                    $this->flashes('warning', 'Le champ contenu est vide.');
                }

                if (empty($flashes["message"])) {

                    $post->setTitle($title)
                        ->setChapo($chapo)
                        ->setContent($content)
                        ->setSlug($slug);

                    $userId = $userCurrent->getId();
                    $post->setUsers($userId);

                    if ($post->update()) {
                        $this->flashes('success', "L'article a bien été modifié.");
                        header('Location: /post/list');
                        exit;
                    } else {
                        $this->flashes('danger', "L'article n'a pas été modifié!");
                    }
                } else {
                    $slug = $this->slugify($title);

                    $this->show('admin/post/update', [
                        'post' => $post,
                        'flashes' => $flashes
                    ]);
                }
            }
        }
        // On affiche notre vue en transmettant les infos du post et des messages d'alerte
        $this->show('admin/post/update', [
            'post' => $post,
        ]);
    }

    /**
     * Suppression d'un post uniquement avec le rôle Super_admin
     *
     * @param string $slug
     * @return void
     */
    public function delete(string $slug)
    {
        $post = Post::findBySlug($slug);

        $currentUserRole = Role::findById($this->userIsConnected()->getRoles());
        
        if (!$this->userIsConnected()) {
            // Sinon le rediriger vers la page de login
            header('Location: /security/login');
        } elseif($currentUserRole->getName() !== "Super_admin") {
            $error403 = new ErrorController;
            $error403->accessDenied(); 
        } else {
            if ($post) {
                $post->delete();
                $this->flashes('success', "Le Bubbles Post $slug a bien été supprimé.");
                header('Location: /post/list');
                exit;
            } else {
                $this->flashes('danger', "Cet article n'existe pas!");
            }
        }
        $this->show('/admin/post/read', [
            'post' => $post
        ]);
    }
}
