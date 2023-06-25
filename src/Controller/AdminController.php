<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Comment;
use App\Controller\CoreController;
use App\Controller\ErrorController;
use App\Repository\CommentRepository;
use App\Repository\RoleRepository;

class AdminController extends CoreController
{
    protected CommentRepository $commentRepository;
    protected RoleRepository $roleRepository;
    public function __construct()
    {
        $this->commentRepository = new CommentRepository();
        $this->roleRepository = new RoleRepository();
    }
    /**
     * Afficher la page admin réservé au rôle super_admin et l'admin
     *
     * @return void
     */
    public function dashboard()
    {
        if (!$this->userIsConnected()) {
            $this->flashes('warning', 'Merci de te connecter!');
            header('Location: /security/login');
        } else {
            $currentUserRole = $this->roleRepository->findById($this->userIsConnected()->getRoleId());
            if ($currentUserRole->getName() === "utilisateur")  {
                $error403 = new ErrorController;
                $error403->accessDenied();
            } else {
                $this->show('admin/dashboard');
            }
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
        if (!$this->userIsConnected()) {
            $this->flashes('warning', 'Merci de te connecter!');
            header('Location: /security/login');
        } else {
            $comments = $this->commentRepository->findAll();
            // Stocker le user en session
            $userCurrent = $this->userIsConnected();
            foreach ($comments as $comment) {
                $commentId = $comment->getId();
            }

            // Récupérer le role du user en session
            $roleId = $userCurrent->getRoleId();
            $role = $this->roleRepository->findById($roleId);

            if ($role->getName() === "utilisateur") {
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
        $comment = $this->commentRepository->findById($commentId);

        if (!$this->userIsConnected()) {
            $this->flashes('warning', 'Merci de te connecter!');
            header('Location: /security/login');
        } else {
            $userCurrent = $this->userIsConnected();
            // Récupérer le role du user en session
            $roleId = $userCurrent->getRoleId();
            $role = $this->roleRepository->findById($roleId);
            if ($role->getName() === "utilisateur") {
                $error403 = new ErrorController;
                $error403->accessDenied();
            } else {
                if ($this->isPost()) {
                    $status = (int)filter_input(INPUT_POST, 'status');
                    $comment->setStatus($status);

                    if ($this->commentRepository->update($comment)) {
                        $this->flashes('success', "Le Bubbles Comment $commentId a bien été modifié.");
                        header('Location: /admin/comments');
                        return;
                    } else {
                        $this->flashes('danger', "Le Bubbles Comment  $commentId n'a pas été modifié!");
                    }
                }
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
        $comment = $this->commentRepository->findById($commentId);

        if (!$this->userIsConnected()) {
            $this->flashes('warning', 'Merci de te connecter!');
            header('Location: /security/login');
        } else {
            if (!$this->commentRepository->findById($commentId)) {
                $error404 = new ErrorController();
                $error404->pageNotFoundAction();
            } else {
                $currentUserRole = $this->roleRepository->findById($this->userIsConnected()->getRoleId());
                if ($currentUserRole->getName() === "utilisateur") {
                    $error403 = new ErrorController;
                    $error403->accessDenied();
            } else {
                    if ($comment) {
                        $this->commentRepository->delete($commentId);
                        $this->flashes('success', "Le Bubbles Comment $commentId a bien été supprimé.");
                        header('Location: /admin/comments');
                        return;
                    } else {
                        $error404 = new ErrorController();
                        $error404->pageNotFoundAction();
                    }
                }
            }
        }
    }
}
