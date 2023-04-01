<?php 

namespace App\Controller;

use App\Models\Comment;

class CommentController extends CoreController {
    
    /**
     * Ajout d'un nouveau comment
     *
     * @return void
     */
    public function create() 
    {
        $this->show('/comment/create', [
                'comment' => new Comment()
            ]);
    }

    /**
     * reading des comments
     * @return void
     */
    public function read()
    {
        $comments = Comment::findAll();
        $this->show('/comment/read', [
                'comments' => $comments
            ]);
    }

    /**
     * Affiche la vue édition d'un comment 
     *
     * @param [type] $commentId
     * @return void
     */
    public function update($commentId)
    {
        $comment = Comment::findBy($commentId);
        $this->show('/comment/update', [
                'comment' => $comment
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

        $comment = Comment::findBy($commentId);

        if ($comment) {
            $comment->delete();

            $flashes = $this->addFlash('success', "Le commentaire a été supprimé");
            header('Location: /comment/read');
        } else {
            $flashes = $this->addFlash('danger', "Ce commentaire n'existe pas!");
        }

        $this->show('/comment/read', [
            'comment' => $comment,
            'flashes' => $flashes
        ]);
    }
}