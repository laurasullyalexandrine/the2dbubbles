<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Comment;
use App\Repository\PostRepository;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use App\Repository\CommentRepository;

class CommentController extends CoreController
{
    protected UserRepository $userRepository;
    protected CommentRepository $commentRepository;
    protected RoleRepository $roleRepository;
    protected PostRepository $postRepository;
    public function __construct()
    {
        $this->userRepository = new UserRepository();
        $this->commentRepository = new CommentRepository();
        $this->roleRepository = new RoleRepository();
        $this->postRepository = new PostRepository();
    }
    /**
     * Ajout d'un nouveau commentaire
     * 
     * @return void
     */
    public function create(string $slug): void
    {
        $comment = new Comment();
        if (!$this->userIsConnected()) {
            $this->flashes('warning', 'Merci de te connecter!');
            header('Location: /security/login');
        } else {
            // Find the Post using slug which will be passed to the view
            $post =  $this->postRepository->findBySlug($this->slugify($slug));

            // Retrieve the Post id in order to set it on the Comment being created
            $postId = $post->getId();

            // If no user connected
            if (!$this->userIsConnected()) {
                // redirect him to the login page
                header('Location: /security/login');
            } else {
                // If yes Retrieve the connected user
                $userCurrent = $this->userIsConnected();

                if ($this->isPost()) {
                    $content = filter_input(INPUT_POST, 'content');

                    if (empty($content)) {
                        $this->flashes('warning', 'Le champ contenu est vide');
                    }
                    if (empty($_SESSION["flashes"])) {
                        $comment->setContent($content)
                            ->setPostId($postId)
                            ->setStatus(Comment::STATUS_WAITING);
                        $userId = $userCurrent->getId();
                        $comment->setUserId($userId);

                        if ($this->commentRepository->insert($comment)) {
                            $this->flashes('warning', 'Ton bubbles Comment a bien été enregistré. Il est maintenant attente de validation!');
                            header('Location: /post/read/' . $post->getSlug());
                            return;
                        } else {
                            $this->flashes('danger', "Le commentaire n'a pas été créé!");
                        }
                    } else {
                        $comment->setContent(filter_input(INPUT_POST, $content));
                    }
                }
                $this->show('/front/comment/create', [
                    'Comment' => new Comment(),
                    'user' => $userCurrent,
                    'post' => $post
                ]);
            }
        }
    }

    /**
     * See a Post and its comments
     * 
     * @param int $commentId
     * @return void
     */
    public function userComment(string $slug): void
    {
        // Retrieve author of comments or future comments
        $uri= $this->uri();
        $uri = trim($uri, '/');
        $params = explode('/', $uri);
        $userCurrentslug = end($params);

        $comments = $this->commentRepository->findByUser($slug);
        if (!$this->userIsConnected()) {
            $this->flashes('warning', 'Merci de te connecter!');
            header('Location: /security/login');
        } else {
            $author = $this->userRepository->findBySlug($userCurrentslug);
            if ($author == $this->userIsConnected()) {
                $posts = [];
                foreach ($comments as $comment) {
                    $posts[] = $this->postRepository->findBySlug($this->slugify($comment->post));

                    foreach ($posts as $post) {
                        $author = $this->userRepository->findByPseudo($post->user);
                    }
                }
                $this->show('front/comment/read', [
                    'comments' => $comments,
                    'author' => $author
                ]);
            } else {
                $error403 = new ErrorController;
                $error403->accessDenied();
            }
        }
    }

    /**
     * Editing a comment
     *
     * @param [type] $commentId
     * @return void
     */
    public function update(int $commentId): void
    {
        if (!$this->commentRepository->findById($commentId)) {
            $error404 = new ErrorController();
            $error404->pageNotFoundAction();
        } else {
            $comment = $this->commentRepository->findById($commentId);

            // Store user in session
            $userCurrent = $this->userIsConnected();

            // Check that there is indeed a connected user
            if (!$userCurrent) {
                $this->flashes('warning', 'Merci de te connecter!');
                header('Location: /security/login');
            } else {
                // Retrieve the role of the user in session
                $roleId = $userCurrent->getRoleId();
                $this->roleRepository->findById($roleId);

                // Get comment author id
                $idAuthorComment = $comment->getUserId();
                if ($userCurrent->getId() !== $idAuthorComment) {
                    // If the connected user is not the author of the comment
                    $error403 = new ErrorController;
                    $error403->accessDenied();
                }

                if ($this->isPost()) {
                    $content = filter_input(INPUT_POST, 'content');

                    if (empty($content)) {
                        $this->flashes('warning', 'Le champ contenu est vide.');
                    }

                    if (empty($_SESSION["flashes"])) {
                        $comment->setContent($content)
                            ->setStatus(Comment::STATUS_WAITING);

                        if ($this->commentRepository->update($comment)) {
                            header('Location: /comment/userComment/' . $userCurrent->getSlug());
                            $this->flashes('success', "Ton Bubbles Comment a bien été modifié. Par contre il est de nouveau en cours de validation.");
                            return;
                        } else {
                            $this->flashes('danger', "Le Bubbles Comment n'a pas été modifié!");
                        }
                    }
                }
                // Show view by passing Comment info and alert messages
                $this->show('front/comment/update', [
                    'comment' => $comment
                ]);
            }
        }
    }

    /**
     * Deleting a comment
     * 
     * @param [type] $commentId
     * @return void
     */
    public function delete(int $commentId)
    {
        if (!$this->commentRepository->findById($commentId)) {
            $error404 = new ErrorController();
            $error404->pageNotFoundAction();
        } else {
            $comment = $this->commentRepository->findById($commentId);
            $idAuthorComment = $comment->getUserId();

            if (!$this->userIsConnected()) {
                $this->flashes('warning', 'Merci de te connecter!');
                header('Location: /security/login');
            } elseif ($this->userIsConnected()->getId() !== $idAuthorComment) {
                // If the connected user is not the author of the comment
                $error403 = new ErrorController;
                $error403->accessDenied();
            } else {
                if ($comment) {
                    $this->commentRepository->delete($commentId);
                    $this->flashes('success', "Le commentaire a bien été supprimé.");
                    header('Location: /comment/userComment/' . $this->userIsConnected()->getSlug());
                    return;
                } else {
                    $this->flashes('danger', "Ce commentaire n'existe pas!");
                }
                $this->show('front/comment/read', [
                    'comment' => $comment
                ]);
            }
        }
    }
}
