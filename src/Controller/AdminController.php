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
     * Display the admin page reserved for the super_admin role and the admin
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
            if ($currentUserRole->getName() === "utilisateur") {
                $error403 = new ErrorController;
                $error403->accessDenied();
            } else {
                $this->show('admin/dashboard');
            }
        }
    }

    /**
     * Editing comments
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
            // Store user in session
            $userCurrent = $this->userIsConnected();
            foreach ($comments as $comment) {
                $commentId = $comment->getId();
            }

            // Retrieve the role of the user in session
            $roleId = $userCurrent->getRoleId();
            $role = $this->roleRepository->findById($roleId);

            if ($role->getName() === "utilisateur") {
                $error403 = new ErrorController;
                $error403->accessDenied();
            } else {
                if ($this->isPost()) {
                    $this->update($commentId);
                }
                // We display our view by transmitting the Comment information and alert messages
                $this->show('/admin/comment/read', [
                    'comments' => $comments
                ]);
            }
        }
    }
    /**
     * Method to retrieve a comment
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
            // Retrieve the role of the user in session
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
     * Deleting a comment only by super_admin and admin roles
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
