<?php 

declare(strict_types=1);

namespace App\Controller;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;

class CommentController extends CoreController {

    /**
     * Ajout d'un nouveau commentaire
     *
     * @return Comment
     */
    public function create($slug)
    {
        $flashes = $this->addFlash();

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
                    $flashes = $this->addFlash('warning', 'Le champ contenu est vide');
                }
                if (empty($flashes["messages"])) {
                    $comment->setContent($content)
                        ->setPosts($postId)
                        ->setStatus(false);
                    $userId = $userCurrent->getId();
                    $comment->setUsers($userId);

                    if ($comment->insert()) {
                        echo $flashes = $this->addFlash('warning', 'Votre commentaire est en attente de validation!');
                        header('Location: /post/read/'. $post->getSlug());
                        exit;
                    } else {
                        $flashes = $this->addFlash('danger', "Le commentaire n'a pas été créé!");
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


    /**
     * Permet de voir un Post et d'y ajouter des commentaires
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
     * Undocumented function
     *
     * @param [type] $commentId
     * @return Comment
     */
    public function update($commentId)
    {
        $flashes = $this->addFlash();
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
                    $flashes = $this->addFlash('warning', 'Le champ contenu est vide');
                }
                if (empty($status)) {
                    $flashes = $this->addFlash('warning', 'Choisir un status');
                }

                if (empty($flashes["messages"])) {
                    $comment->setContent($content)
                        ->setStatus($status);

                    $userId = $userCurrent->getId();
                    $comment->setUsers($userId);

                    if ($comment->update()) {
                        header('Location: /comment/list');
                        exit;
                    } else {
                        $flashes = $this->addFlash('danger', "L'article n'a pas été modifié!");
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
     * Permet de supprimer un commentaire
     *
     * @param [type] $commentId
     * @return void
     */
    public function delete($commentId)
    {
        $flashes = $this->addFlash();
        $comment = Comment::findById($commentId);

        if ($comment) {
            $comment->delete();
            $flashes = $this->addFlash('success', "L'article a été supprimé");
            header('Location: /comment/list');
            exit;
        } else {
            $flashes = $this->addFlash('danger', "Cet article n'existe pas!");
        }

        $this->show('/comment/read', [
            'comment' => $comment,
            'flashes' => $flashes
        ]);
    }
}