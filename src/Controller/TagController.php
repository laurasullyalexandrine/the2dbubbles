<?php 

namespace App\Controller;

use App\Models\Tag;

class TagController extends CoreController {
    /**
     * Listing des tags
     * @return void
     */
    public function list()
    {
        $tags = Tag::findAll();
        $this->show('tag/list', [
                'tags' => $tags
            ]);
    }

    /**
     * Ajout d'un nouveau tag
     *
     * @return void
     */
    public function add() 
    {
        $this->show('tag/add-edit', [
                'tag' => new Tag()
            ]);
    }

    /**
     * Affiche la vue édition d'un tag 
     *
     * @param [type] $tagId
     * @return void
     */
    public function edit($tagId)
    {
        $tag = Tag::findBy($tagId);
        $this->show('tag/add-edit', [
                'tag' => $tag
            ]);
    }
}