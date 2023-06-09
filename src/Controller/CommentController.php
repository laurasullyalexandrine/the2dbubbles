<?php

declare(strict_types=1);

namespace App\Controller;

use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use App\Models\Comment;

class CommentController extends CoreController
{
    /**
     * Ajout d'un nouveau commentaire
     * 
     * @return Comment
     */
    public function create(string $slug)
    {
        $comment = new Comment();

        // Trouver le Post à l'aide slug qui sera transmis à la vue
        $post = Post::findBySlug($this->slugify($slug));

        // Récupérer l'id du Post afin de le setter sur le Comment en cours de création
        $postId = $post->getId();

        // Si pas de user connecté
        if (!$this->userIsConnected()) {
            // le rediriger vers la page de login
            header('Location: /security/login');
        } else {
            // Si oui Récupérer le user connecté
            $userCurrent = $this->userIsConnected();

            if ($this->isPost()) {
                $content = filter_input(INPUT_POST, 'content');

                if (empty($content)) {
                    $this->flashes('warning', 'Le champ contenu est vide');
                }
                if (empty($_SESSION["flashes"])) {
                    $comment->setContent($content)
                        ->setPosts($postId)
                        ->setStatus(2);
                    $userId = $userCurrent->getId();
                    $comment->setUsers($userId);

                    if ($comment->insert()) {
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

    /**
     * Voir un Post et ses commentaires
     * 
     * @param int $commentId
     * @return void
     */
    public function userComment(string $slug): void
    {
        $comments = Comment::findByUser($slug);

        if (empty($comments)) {
            $author = null;
        } else {
            $posts = [];
            foreach ($comments as $comment) {
                $posts[] = Post::findBySlug($this->slugify($comment->post));
                
                foreach ($posts as $post) {
                    $author = User::findByPseudo($post->user);
                }
            }
        }
        $this->show('front/comment/read', [
            'comments' => $comments,
            'author' => $author
        ]);
    }

    /**
     * Édition d'un commentaire
     *
     * @param [type] $commentId
     * @return void
     */
    public function update(int $commentId): void
    {
        $comment = Comment::findById($commentId);

        // Stocker le user en session
        $userCurrent = $this->userIsConnected();

        // Récupérer le role du user en session
        $roleId = $userCurrent->getRoles();
        Role::findById($roleId);

        // Récupérer l'id de lauteur du commentaire
        $idAuthorComment = $comment->getUsers();

        // Vérifier qu'il y a bien un user connecté
        if (!$userCurrent) {
            // Sinon le rediriger vers la page de login
            header('Location: /security/login');
        } elseif($userCurrent->getId() !== $idAuthorComment) {
            // Si le user connecté n'est pas l'auteur du commentaire
            $error403 = new ErrorController;
            $error403->accessDenied(); 
        } else {
            if ($this->isPost()) {
                $content = filter_input(INPUT_POST, 'content');
        
                if (empty($content)) {
                    $this->flashes('warning', 'Le champ contenu est vide.');
                }

                if (empty($_SESSION["flashes"])) {
                    $comment->setContent($content)
                        ->setStatus(2);

                    if ($comment->update()) {
                        header('Location: /comment/userComment/'. $userCurrent->getSlug());
                        $this->flashes('success', "Ton Bubbles Comment a bien été modifié. Par contre Il est de nouveau en cours de validation.");
                        return;
                    } else {
                        $this->flashes('danger', "Le Bubbles Comment n'a pas été modifié!");
                    }
                }
            }
        }
        // Afficher la vue en transmettant les infos du Comment et des messages d'alerte
        $this->show('front/comment/update', [
            'comment' => $comment
        ]);
    }

    /**
     * Suppression d'un commentaire
     * 
     * @param [type] $commentId
     * @return void
     */
    public function delete(int $commentId)
    {
        $comment = Comment::findById($commentId);
        $idAuthorComment = $comment->getUsers();

        if (!$this->userIsConnected()) {
            $this->flashes('warning', 'Merci de te connecter!');
            header('Location: /security/login');
        } elseif($this->userIsConnected()->getId() !== $idAuthorComment) {
             // Si le user connecté n'est pas l'auteur du commentaire
             $error403 = new ErrorController;
             $error403->accessDenied();
        } else {
            if ($comment) {
                $comment->delete();
                $this->flashes('success', "Le commentaire a bien été supprimé.");
                header('Location: /comment/userComment/'. $this->userIsConnected()->getSlug());
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
