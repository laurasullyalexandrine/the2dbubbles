<?php 

namespace App\Controller;

use App\Models\Comment;

class CommentController extends CoreController {
    
    /**
     * Listing des comments
     * @return void
     */
    public function list()
    {
        $comments = Comment::findAll();
        $this->show('comment/list', [
                'comments' => $comments
            ]);
    }

    /**
     * Ajout d'un nouveau comment
     *
     * @return void
     */
    public function add() 
    {
        $this->show('comment/add', [
                'comment' => new Comment()
            ]);
    }

    /**
     * Affiche la vue édition d'un comment 
     *
     * @param [type] $commentId
     * @return void
     */
    public function edit($commentId)
    {
        $comment = Comment::findBy($commentId);
        $this->show('comment/edit', [
                'comment' => $comment
            ]);
    }
}