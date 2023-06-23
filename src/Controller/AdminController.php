<?php

declare(strict_types=1);

namespace App\Controller;

use App\Models\Role;
use App\Models\Comment;
use App\Controller\CoreController;
use App\Controller\ErrorController;

class AdminController extends CoreController
{

    /**
     * Afficher la page admin réservé au rôle super_admin et l'admin
     *
     * @return void
     */
    public function dashboard()
    {
        if (!$this->userIsConnected()) {
            $error403 = new ErrorController;
            $error403->accessDenied();
        } else {
            $this->show('admin/dashboard');
        }
    }


    /**
     * Édition des commentaires
     *
     * @param [type] $commentId
     * @return Comment
     */
    public function comments()
    {
        if ($this->isGet()) {
            $error403 = new ErrorController;
            $error403->accessDenied();
        } else {
            $comments = Comment::findAll();
            // Stocker le user en session
            $userCurrent = $this->userIsConnected();
            foreach ($comments as $comment) {
                $commentId = $comment->getId();
            }
    
            // Récupérer le role du user en session
            $roleId = $userCurrent->getRoleId();
            $role = Role::findById($roleId);
    
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
    }
    /**
     * Méthode permettant de récupérer un commentaire
     *
     * @param integer $commentId
     * @return void
     */
    public function update(int $commentId): void
    {
        $comment = Comment::findById($commentId);

        if ($this->isPost()) {
            $status = (int)filter_input(INPUT_POST, 'status');
            $comment->setStatus($status);

            if ($comment->update()) {
                $this->flashes('success', "Le Bubbles Comment $commentId a bien été modifié.");
                header('Location: /admin/comments');
                return;
            } else {
                $this->flashes('danger', "Le Bubbles Comment  $commentId n'a pas été modifié!");
            }
        }
    }

    /**
     * Suppression d'un commentaire seulement par les rôles super_admin et l'admin
     * 
     * @param [type] $commentId
     * @return void
     */
    public function delete(int $commentId): void
    {
        $comment = Comment::findById($commentId);
        $currentUserRole = Role::findById($this->userIsConnected()->getRoleId());

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
                return;
            } else {
                $this->flashes('danger', "Ce Bubbles Comment n'existe pas!");
            }
        }
    }
}
