<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Post;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use App\Repository\RoleRepository;

/**
 * Controller dédié à la gestion des posts
 */
class PostController extends CoreController
{
    protected PostRepository $postRepository;
    protected RoleRepository $roleRepository;
    protected CommentRepository $commentRepository;
    public function __construct()
    {
        $this->postRepository = new PostRepository();
        $this->roleRepository = new RoleRepository();
        $this->commentRepository = new CommentRepository();
    }
    /**
     * Afficher tous les artilces de la base de données
     * 
     * @return void
     */
    public function list()
    {
        if (!$this->userIsConnected()) {
            $this->flashes('warning', 'Merci de te connecter!');
            header('Location: /security/login');
        } else {
            $posts = $this->postRepository->findAll();
            // On les envoie à la vue
            $this->show('/front/post/list', [
                'posts' => $posts
            ]);
        }
    }

    /**
     * Ajout d'un nouveau post
     * 
     * @return void
     */
    public function create(): void
    {
        $post = new Post();

        if (!$this->userIsConnected()) {
            $this->flashes('warning', 'Merci de te connecter!');
            header('Location: /security/login');
        } else {
            $currentUserRole = $this->roleRepository->findById($this->userIsConnected()->getRoleId());
            if ($currentUserRole->getName() !== "super_admin") {
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
                    if (empty($_SESSION["flashes"])) {

                        $userId = $userCurrent->getId();
                        $post->setUserId($userId);

                        if ($this->postRepository->insert($post)) {
                            $this->flashes('success', "L'article a bien été créé.");
                            header('Location: /post/list');
                            return;
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
    }

    /**
     * Voir un Post et ses commentaires
     *
     * @param string $title
     * @return Post
     */
    public function read(string $slug)
    {
        if (!$this->userIsConnected()) {
            $this->flashes('warning', 'Merci de te connecter!');
            header('Location: /security/login');
        } else {
            if (!$this->postRepository->findBySlug($slug)) {
                $error404 = new ErrorController();
                $error404->pageNotFoundAction();
            } else {
                $post =  $this->postRepository->findBySlug($slug);
                // Récupérer les tableaux des commentaires
                $comments = $this->commentRepository->findBySlugPost($slug);
                $commentsCheck = [];

                foreach ($comments as $comment) {
                    if ($comment->getStatus() === 1) {
                        $commentsCheck[] = $comment;
                    }
                }
                // On les envoie à la vue
                $this->show('/front/post/read', [
                    'post' => $post,
                    'comments' => $comments,
                    'commentsCheck' => $commentsCheck
                ]);
            }
        }
    }

    /**
     * Édition d'un Post (article)
     *
     * @param string $slug
     * @return void
     */
    public function update(string $slug): void
    {
        if (!$this->postRepository->findBySlug($slug)) {
            $error404 = new ErrorController();
            $error404->pageNotFoundAction();
        } else {
            $post = $this->postRepository->findBySlug($slug);
            $currentUserRole = $this->roleRepository->findById($this->userIsConnected()->getRoleId());
            if (!$this->userIsConnected()) {
                $this->flashes('warning', 'Merci de te connecter!');
                header('Location: /security/login');
            } elseif ($currentUserRole->getName() !== "super_admin") {
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

                    if (empty($title)) {
                        $this->flashes('warning', 'Le champ titre est vide.');
                    }
                    if (empty($chapo)) {
                        $this->flashes('warning', 'Le champ chapô est vide.');
                    }
                    if (empty($content)) {
                        $this->flashes('warning', 'Le champ contenu est vide.');
                    }

                    if (empty($_SESSION["flashes"])) {

                        $post->setTitle($title)
                            ->setChapo($chapo)
                            ->setContent($content)
                            ->setSlug($slug);

                        $userId = $userCurrent->getId();
                        $post->setUserId($userId);

                        if ($this->postRepository->update($post)) {
                            $this->flashes('success', "L'article a bien été modifié.");
                            header('Location: /post/list');
                            return;
                        } else {
                            $this->flashes('danger', "L'article n'a pas été modifié!");
                        }
                    } else {
                        $slug = $this->slugify($title);

                        $this->show('admin/post/update', [
                            'post' => $post
                        ]);
                    }
                }
                // On affiche notre vue en transmettant les infos du post et des messages d'alerte
                $this->show('admin/post/update', [
                    'post' => $post,
                ]);
            }
        }
    }

    /**
     * Suppression d'un post uniquement avec le rôle super_admin
     *
     * @param string $slug
     * @return void
     */
    public function delete(string $slug)
    {
        if (!$this->userIsConnected()) {
            $this->flashes('warning', 'Merci de te connecter!');
            header('Location: /security/login');
        } else {
            if (!$this->postRepository->findBySlug($slug)) {
                $error404 = new ErrorController();
                $error404->pageNotFoundAction();
            } else {
                $post = $this->postRepository->findBySlug($slug);
                $currentUserRole = $this->roleRepository->findById($this->userIsConnected()->getRoleId());

                if ($currentUserRole->getName() !== "super_admin") {
                    $error403 = new ErrorController;
                    $error403->accessDenied();
                } else {
                    if ($post) {
                        $this->postRepository->delete($post->getId());
                        $this->flashes('success', "Le Bubbles Post $slug a bien été supprimé.");
                        header('Location: /post/list');
                        return;
                    } else {
                        $this->flashes('danger', "Cet article n'existe pas!");
                    }
                    $this->show('/admin/post/read', [
                        'post' => $post
                    ]);
                }
            }
        }
    }
}
