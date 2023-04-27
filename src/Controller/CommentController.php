<?php

declare(strict_types=1);

namespace App\Controller;

use App\Models\Comment;
use App\Models\Post;

class CommentController extends CoreController
{

    /**
     * Ajout d'un nouveau commentaire
     * 
     * @return Comment
     */
    public function create(string $slug)
    {
        $flashes = $this->flashes();
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
                    $flashes = $this->flashes('warning', 'Le champ contenu est vide');
                }
                if (empty($flashes["message"])) {
                    $comment->setContent($content)
                        ->setPosts($postId)
                        ->setStatus(false);
                    $userId = $userCurrent->getId();
                    $comment->setUsers($userId);

                    if ($comment->insert()) {
                        echo $flashes = $this->flashes('warning', 'Votre commentaire est en attente de validation!');
                        header('Location: /post/read/' . $post->getSlug());
                        exit;
                    } else {
                        $flashes = $this->flashes('danger', "Le commentaire n'a pas été créé!");
                    }
                } else {
                    $comment->setContent(filter_input(INPUT_POST, $content));
                }
            }
            $this->show('/Comment/create', [
                'Comment' => new Comment(),
                'user' => $userCurrent,
                'post' => $post,
                'flashes' => $flashes
            ]);
        }
    }

    // TODO: à revoir
    /**
     * Voir un Post et ses commentaires
     * 
     * @param int $commentId
     * @return Comment
     */
    public function read(int $commentId)
    {
        $comment = Comment::findById($commentId);

        // On les envoie à la vue
        $this->show('/comment/read', [
            'comment' => $comment
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
        $flashes = $this->flashes();
        $comment = Comment::findById($commentId);

        // Vérifier qu'il y a bien un user connecté
        if (!$this->userIsConnected()) {
            // Sinon le rediriger vers la page de login
            header('Location: /security/login');
        } else {
            // Récupérer le user connecté
            $userCurrent = $this->userIsConnected();

            // TODO: Ajouter l'access control en fonction du role et la generation du token

            if ($this->isPost()) {
                $content = filter_input(INPUT_POST, 'content');
                $status = (bool)filter_input(INPUT_POST, 'status');

                if (empty($content)) {
                    $flashes = $this->flashes('warning', 'Le champ contenu est vide.');
                }
                if (empty($status)) {
                    $flashes = $this->flashes('warning', 'Choisir un status.');
                }

                if (empty($flashes["message"])) {
                    $comment->setContent($content)
                        ->setStatus($status);

                    $userId = $userCurrent->getId();
                    $comment->setUsers($userId);

                    if ($comment->update()) {
                        header('Location: /comment/list');
                        $flashes = $this->flashes('success', "Le commentaire a bien été modifié.");
                        exit;
                    } else {
                        $flashes = $this->flashes('danger', "L'article n'a pas été modifié!");
                    }
                }
            }
        }
        // On affiche notre vue en transmettant les infos du Comment et des messages d'alerte
        $this->show('/Comment/update', [
            'Comment' => $comment,
            'flashes' => $flashes
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

            if ($comment) {
                $comment->delete();
                header('Location: /post/list');
                $flashes = $this->flashes('success', "Le commentaire a bien été supprimé");
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
