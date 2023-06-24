<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Entity\Role;
use App\Repository\UserRepository;
use App\Controller\ErrorController;
use App\Repository\RoleRepository;

/**
 * Controller dédié à la gestion des utilisateurs
 */
class UserController extends CoreController
{
    protected UserRepository $userRepository;
    protected RoleRepository $roleRepository;
    public function __construct()
    {
        $this->userRepository = new UserRepository();
        $this->roleRepository = new RoleRepository();
    }
    /**
     * Afficher tous les utilisateurs de la base de données
     * 
     * @return void
     */
    public function read()
    {
        $users = $this->userRepository->findAll();
        $this->show('admin/user/read', [
            'users' => $users
        ]);
    }

    /**
     * Ajout d'un nouvel utilisateur
     * 
     * @return void
     */
    public function create(): void
    {
        $user =  new User();
        $roleRepository = new RoleRepository();
        $roles = $roleRepository->findAll();

        $currentUserRole = $roleRepository->findById($this->userIsConnected()->getRoleId());
        if (!$this->userIsConnected()) {
            // Sinon le rediriger vers la page de login
            header('Location: /security/login');
        } elseif ($currentUserRole->getName() !== "super_admin") {
            $error403 = new ErrorController;
            $error403->accessDenied();
        } else {
            if ($this->isPost()) {
                
                // Récupérer les données recues du formalaire d'inscription
                $pseudo = filter_input(INPUT_POST, 'pseudo');
                $slug = $this->slugify($pseudo);
                $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
                $password = filter_input(INPUT_POST, 'password');
                $password_2 = filter_input(INPUT_POST, 'password_2');
                // Contraindre le type de la valeur soumis 
                $role = (int)filter_input(INPUT_POST, 'role');

           
                // Mettre à jour les propriétés de l'instance
                $user->setPseudo($pseudo);
                $user->setEmail($email);
                $user->setRoleId($role);

                // Vérifier que tous les champs ne sont pas vide 
                if (empty($pseudo)) {
                    $this->flashes('warning', 'Le champ prénom/pseudo est vide.');
                }
                if (empty($email)) {
                    $this->flashes('warning', 'Le champ email est vide.');
                }

                if (empty($password)) {
                    $this->flashes('warning', 'Le champ mot de passe est vide.');
                }

                if (empty($password_2)) {
                    $this->flashes('warning', 'Le champ confirmation de mot de passe est vide.');
                }

                if ($password !== $password_2) {
                    $this->flashes('danger', 'Les mots de passe ne corresponde pas!');
                }

                // Contrôler si le rôle soumis est un rôle existant en BDD 
                $roleExist = false;
                foreach ($roles as $existingRole) {
                    // Si l'id du rôle soumis existe en base de données
                    if ($existingRole->getId() === $role) {
                        $roleExist = true;
                        break;
                    }
                }

                // Si il n'existe pas on affiche le message d'alerte
                if (!$roleExist) {
                    $this->flashes('warning', 'Le rôle choisi est invalide');
                }

                // Si le formulaire est valide alors ...
                if (empty($_SESSION["flashes"])) {
                    // Hasher le mot de passe 
                    $option = ['cost' => UserRepository::HASH_COST];
                    $password = password_hash(
                        $password,
                        PASSWORD_BCRYPT,
                        $option
                    );
                    // Mettre à jour le reste des propriétés de l'instance
                    $user->setSlug($slug)
                        ->setPassword($password);

                    // Essayer de faire l'insertion du nouvel utilisateur 
                    try {
                        if ($this->userRepository->insert($user)) {
                            $this->flashes('success', "Ton compte a bien été créé.");
                            header('Location: /user/read');
                            return;
                        } // Sinon erreur lors de l'enregistrement
                        else {
                            $this->flashes('danger', "Ton compte n'a pas été créé!");
                        }
                    } catch (\Exception $e) { // Attrapper l'exception 23000 qui correspond du code Unique de MySQL (avant ça il indiquer dans la bdd que le champ est 'unique')
                        if ($e->getCode() === '23000') {
                            $this->flashes('danger', 'Il existe déjà un compte avec cet email!');
                        } else {
                            $this->flashes('danger', $e->getMessage());
                        }
                    }
                }
            }
            $this->show('admin/user/create', [
                'user' => $user, 
                'roles' => $roles
            ]);
        }
    }

    /**
     * Édition d'un utilisateur seulement par le rôle super_admin
     *
     * @param [type] $userId
     * @return void
     */
    public function update(int $userId): void
    {
        $user = $this->userRepository->findById($userId);
        $updatedRole = $user->getRoleId();
        $roles = $this->roleRepository->findAll();

        $currentUserRole = $this->roleRepository->findById($this->userIsConnected()->getRoleId());

        if (!$this->userIsConnected()) {
            header('Location: /security/login');
        } elseif ($currentUserRole->getName() !== "super_admin") {
            $error403 = new ErrorController;
            $error403->accessDenied();
        } else {
            // Vérifier si le rôle soumis existe en base
            foreach ($roles as $existingRole) {
                if ($existingRole->getId() === $updatedRole) {
                    $updatedRoleName = $existingRole->getName();
                }
            }

            if ($this->isPost()) {
                $pseudo = filter_input(INPUT_POST, 'pseudo');
                $slug = $this->slugify($pseudo);
                $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
                $password = filter_input(INPUT_POST, 'password');
                $role = (int)filter_input(INPUT_POST, 'role');
                $user->setPseudo($pseudo);
                $user->setEmail($email);
                $user->setRoleId($role);

                // Vérifier que tous les champs ne sont pas vide 
                if (empty($pseudo)) {
                    $this->flashes('warning', 'Le champ Prénom/Pseudo est vide');
                }
                if (empty($email)) {
                    $this->flashes('warning', 'Le champ email est vide');
                }
                if (empty($password)) {
                    $this->flashes('warning', 'Le champ mot de passe est vide');
                }

                if (empty($_SESSION["flashes"])) {
                    $option = ['cost' => UserRepository::HASH_COST];
                    $password = password_hash(
                        $password,
                        PASSWORD_BCRYPT,
                        $option
                    );
                    $user->setSlug($slug)
                        ->setPassword($password);
                    if ($this->userRepository->update($user)) {
                        $this->flashes('success', 'Le Bubbles User'. ' ' . $user->getPseudo(). ' ' . 'a bien été modifié.');
                        header('Location: /user/read');
                        return;
                    } else {
                        $this->flashes('danger', "L'utilisateur n'a pas été modifié!");
                    }
                } else {
                    $user->setPseudo(filter_input(INPUT_POST, $pseudo));
                    $user->setEmail(filter_input(INPUT_POST, $email));
                    $user->setRoleId((int)filter_input(INPUT_POST, 'role'));
                }
            }
            $this->show('admin/user/update', [
                'user' => $user,
                'role_current_user' => $updatedRole,
                'role_name_user' => $updatedRoleName,
                'roles' => $roles
            ]);
        }
    }

    /**
     * Suppression d'un utilisateur par une rôle super_admin
     *
     * @param [type] $userId
     * @return void
     */
    public function delete(int $userId)
    {
        $user = $this->userRepository->findById($userId);

        $currentUserRole = $this->roleRepository->findById($this->userIsConnected()->getRoleId());

        if (!$this->userIsConnected()) {
            header('Location: /security/login');
        } elseif ($currentUserRole->getName() !== "super_admin") {
            $error403 = new ErrorController;
            $error403->accessDenied();
        } else {
            if ($user) {
                $this->userRepository->delete($userId);
                $this->flashes('success', 'Le Bubbles User' . ' ' . $user->getPseudo() . ' ' . 'a bien été supprimé.');
                header('Location: /user/read');
                return;
            } else {
                $this->flashes('danger', "Ce Bubbles User n'existe pas!");
            }

            $this->show('/admin/user/read', [
                'user' => $user
            ]);
        }
    }
}
