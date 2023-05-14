<?php

declare(strict_types=1);

namespace App\Controller;

use App\Models\Role;
use App\Models\Comment;
use App\Controller\CoreController;
use App\Controller\ErrorController;

class adminController extends CoreController
{

    /**
     * Afficher la page admin réservé au rôle super_admin et l'admin
     *
     * @return void
     */
    public function dashboard()
    {
        $this->show('admin/dashboard');
    }


    /**
     * Édition des commentaires
     *
     * @param [type] $commentId
     * @return Comment
     */
    public function comments()
    {
        $comments = Comment::findAll();
        // Stocker le user en session
        $userCurrent = $this->userIsConnected();
        foreach ($comments as $comment) {
            $commentId = $comment->getId();
        }

        // Récupérer le role du user en session
        $roleId = $userCurrent->getRoles();
        $role = Role::findById($roleId);
        // dd($comments, $userCurrent, $role);

        if (!$userCurrent) {
            $this->flashes('warning', 'Une petite connexion avant ?!');
            header('Location: /security/login');
        } elseif ($role->getName() == "utilisateur") {
            $error403 = new ErrorController;
            $error403->accessDenied();
        } else {
            if ($this->isPost()) {
                $this->update($commentId);
            }
        }
        // On affiche notre vue en transmettant les infos du Comment et des messages d'alerte
        $this->show('/admin/comment/read', [
            'comments' => $comments
        ]);
    }

    public function update(int $commentId)
    {
        $comment = Comment::findById($commentId);

        if ($this->isPost()) {
            $status = (int)filter_input(INPUT_POST, 'status');
            $comment->setStatus($status);

            if ($comment->update()) {
                $this->flashes('success', "Le commentaire $commentId a bien été modifié.");
                header('Location: /admin/comments');
                exit;
            } else {
                $this->flashes('danger', "L'article n'a pas été modifié!");
            }
        }
    }

    /**
     * Suppression d'un commentaire seulement par les rôles super_admin et l'admin
     * 
     * @param [type] $commentId
     * @return void
     */
    public function delete(int $commentId)
    {
        $comment = Comment::findById($commentId);
        $currentUserRole = Role::findById($this->userIsConnected()->getRoles());

        if (!$this->userIsConnected()) {
            $this->flashes('warning', 'Merci de te connecter!');
            header('Location: /security/login');
        } elseif ($currentUserRole->getName() === "utilisateur") {
            $error403 = new ErrorController;
            $error403->accessDenied();
        } else {
            if ($comment) {
                $comment->delete();
                $this->flashes('success', "Le Bubbles Comment $commentId a bien été supprimé.");
                header('Location: /admin/comments');
                exit;
            } else {
                $this->flashes('danger', "Ce Bubbles Comment n'existe pas!");
            }
        }
    }
}
