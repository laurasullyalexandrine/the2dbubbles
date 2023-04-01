<?php 

namespace App\Controller;

use App\Models\Tag;

class TagController extends CoreController {



    /**
     * Ajout d'un nouveau tag
     *
     * @return void
     */
    public function create() 
    {
        $this->show('admin/tag/create', [
                'tag' => new Tag()
            ]);
    }

    /**
     * Permet de lire les tags
     * @return void
     */
    public function read()
    {
        $tags = Tag::findAll();
        $this->show('admin/tag/read', [
                'tags' => $tags
            ]);
    }

    /**
     * Affiche la vue d'édition d'un tag 
     *
     * @param [type] $tagId
     * @return void
     */
    public function update($tagId)
    {
        $tag = Tag::findBy($tagId);
        $this->show('admin/tag/update', [
                'tag' => $tag
            ]);
    }

    /**
     * Permet de supprimer un tag
     *
     * @param [type] $tagId
     * @return void
     */
    public function delete($tagId) 
    {
        $flashes = $this->addFlash();

        $tag = Tag::findBy($tagId);
 
        if ($tag) {
            $tag->delete();

            $flashes = $this->addFlash('success', "Le tag a été supprimé");
            header('Location: /tag/read');
        } else {
            $flashes = $this->addFlash('danger', "Ce tag n'existe pas!");
        }

        $this->show('/tag/read', [
            'tag' => $tag,
            'flashes' => $flashes
        ]);
    }
}