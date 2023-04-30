<?php

declare(strict_types=1);

namespace App\Controller;

use App\Models\Post;
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
                if (empty($flashes["message"])) {
                    $comment->setContent($content)
                        ->setPosts($postId)
                        ->setStatus(false);
                    $userId = $userCurrent->getId();
                    $comment->setUsers($userId);

                    if ($comment->insert()) {
                        $this->flashes('warning', 'Votre commentaire est bien enregistré et est attente de validation!');
                        header('Location: /post/read/' . $post->getSlug());
                        exit;
                    } else {
                        $this->flashes('danger', "Le commentaire n'a pas été créé!");
                    }
                } else {
                    $comment->setContent(filter_input(INPUT_POST, $content));
                }
            }
            $this->show('/Comment/create', [
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
     * @return Comment
     */
    public function comment_user($pseudo)
    {
        $comments = Comment::findByUser($pseudo);
        $posts = [];
        foreach ($comments as $comment) {
            $posts[] = Post::findBySlug($this->slugify($comment->post));
            
            foreach ($posts as $post) {
                $author = User::findByPseudo($post->user);
            }
        }

        $this->show('/comment/read', [
            'comments' => $comments,
            'author' => $author
        ]);
    }

    /**
     * Édition d'un commentaire
     *
     * @param [type] $commentId
     * @return Comment
     */
    public function update(int $commentId)
    {
        $comment = Comment::findById($commentId);

        // Vérifier qu'il y a bien un user connecté
        if (!$this->userIsConnected()) {
            // Sinon le rediriger vers la page de login
            header('Location: /security/login');
        } else {
            // Récupérer le user connecté
            $userCurrent = $this->userIsConnected();

            if ($this->isPost()) {
                $content = filter_input(INPUT_POST, 'content');
                $status = (bool)filter_input(INPUT_POST, 'status');

                if (empty($content)) {
                    $flashes = $this->flashes('warning', 'Le champ contenu est vide.');
                }
                if (empty($status)) {
                    $this->flashes('warning', 'Choisir un status.');
                }

                if (empty($flashes["message"])) {
                    $comment->setContent($content)
                        ->setStatus($status);

                    $userId = $userCurrent->getId();
                    $comment->setUsers($userId);

                    if ($comment->update()) {
                        header('Location: /comment/list');
                        $this->flashes('success', "Le commentaire a bien été modifié.");
                        exit;
                    } else {
                        $this->flashes('danger', "L'article n'a pas été modifié!");
                    }
                }
            }
        }
        // On affiche notre vue en transmettant les infos du Comment et des messages d'alerte
        $this->show('/Comment/update', [
            'Comment' => $comment
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
        if (!$this->userIsConnected()) {
            header('Location: /security/login');
        } else {
            $comment = Comment::findById($commentId);
            // dd($comment);
            if ($comment) {
                $comment->delete();
                $flashes = $this->flashes('success', "Le commentaire a bien été supprimé");
                header('Location: /comment/comment_user/'. $this->userIsConnected()->getPseudo());
                exit;
            } else {
                $flashes = $this->flashes('danger', "Ce commentaire n'existe pas!");
            }

            $this->show('/comment/read', [
                'comment' => $comment,
                'flashes' => $flashes
            ]);
        }
    }
}
