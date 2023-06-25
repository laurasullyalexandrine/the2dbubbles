<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Controller\ErrorController;
use App\Repository\RoleRepository;

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
     * Show all database users
     * 
     * @return void
     */
    public function read()
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
                $users = $this->userRepository->findAll();
                $this->show('admin/user/read', [
                    'users' => $users
                ]);
            }
        }
    }

    /**
     * Adding a new user
     * 
     * @return void
     */
    public function create(): void
    {
        $user =  new User();
        $roles = $this->roleRepository->findAll();

        if (!$this->userIsConnected()) {
            $this->flashes('warning', 'Merci de te connecter!');
            header('Location: /security/login');
        } else {
            $currentUserRole =  $this->roleRepository->findById($this->userIsConnected()->getRoleId());
            if ($currentUserRole->getName() !== "super_admin") {
                $error403 = new ErrorController;
                $error403->accessDenied();
            } else {
                if ($this->isPost()) {
                    // Retrieve the data received from the registration form
                    $pseudo = filter_input(INPUT_POST, 'pseudo');
                    $slug = $this->slugify($pseudo);
                    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
                    $password = filter_input(INPUT_POST, 'password');
                    $password_2 = filter_input(INPUT_POST, 'password_2');
                    // Constrain the type of the submitted value
                    $role = (int)filter_input(INPUT_POST, 'role');

                    // Update instance properties
                    $user->setPseudo($pseudo);
                    $user->setEmail($email);
                    $user->setRoleId($role);

                    // Check that all fields are not empty
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

                    // Check if the submitted role is an existing role in BDD
                    $roleExist = false;
                    foreach ($roles as $existingRole) {
                        // If the submitted role id exists in the database
                        if ($existingRole->getId() === $role) {
                            $roleExist = true;
                            break;
                        }
                    }

                    // If it does not exist, the alert message is displayed.
                    if (!$roleExist) {
                        $this->flashes('warning', 'Le rôle choisi est invalide');
                    }

                    // If the form is valid then ...
                    if (empty($_SESSION["flashes"])) {
                        // Hasher le mot de passe 
                        $option = ['cost' => UserRepository::HASH_COST];
                        $password = password_hash(
                            $password,
                            PASSWORD_BCRYPT,
                            $option
                        );
                        // Update the rest of the instance properties
                        $user->setSlug($slug)
                            ->setPassword($password);

                        // Try to insert the new user 
                        try {
                            if ($this->userRepository->insert($user)) {
                                $this->flashes('success', "Ton compte a bien été créé.");
                                header('Location: /user/read');
                                return;
                            } // Otherwise error while saving
                            else {
                                $this->flashes('danger', "Ton compte n'a pas été créé!");
                            }
                        } catch (\Exception $e) { // Catch exception 23000 which corresponds to MySQL Unique code (before that it indicates in the database that the field is 'unique')
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
    }

    /**
     * Editing a user only by super_admin role
     *
     * @param [type] $userId
     * @return void
     */
    public function update(int $userId): void
    {
        if (!$this->userIsConnected()) {
            $this->flashes('warning', 'Merci de te connecter!');
            header('Location: /security/login');
        } else {

            if (!$this->userRepository->findById($userId)) {
                $error404 = new ErrorController;
                $error404->pageNotFoundAction();
            } else {
                $user = $this->userRepository->findById($userId);
                $updatedRole = $user->getRoleId();
                $roles = $this->roleRepository->findAll();
                $currentUserRole = $this->roleRepository->findById($this->userIsConnected()->getRoleId());
                if ($currentUserRole->getName() !== "super_admin") {
                    $error403 = new ErrorController;
                    $error403->accessDenied();
                } else {
                    // Check if the submitted role exists in the database
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

                        // Check that all fields are not empty
                        if (empty($pseudo)) {
                            $this->flashes('warning', 'Le champ Prénom/Pseudo est vide');
                        }
                        if (empty($email)) {
                            $this->flashes('warning', 'Le champ email est vide');
                        }

                        if (empty($_SESSION["flashes"])) {
                            $option = ['cost' => UserRepository::HASH_COST];
                            $password = password_hash(
                                $password,
                                PASSWORD_BCRYPT,
                                $option
                            );
                            $user->setPseudo($pseudo);
                            $user->setEmail($email);
                            $user->setRoleId($role);
                            $user->setSlug($slug)
                                ->setPassword($password);
                            if ($this->userRepository->update($user)) {
                                $this->flashes('success', 'Le Bubbles User' . ' ' . $user->getPseudo() . ' ' . 'a bien été modifié.');
                                header('Location: /user/read');
                                return;
                            } else {
                                $this->flashes('danger', "L'utilisateur n'a pas été modifié!");
                            }
                        } else {
                            $user->setPseudo(filter_input(INPUT_POST, $pseudo));
                            $user->setEmail(filter_input(INPUT_POST, $email));
                            $user->setRoleId((int)filter_input(INPUT_POST, 'role'));

                            $this->show('admin/user/update', [
                                'user' => $user,
                                'role_current_user' => $updatedRole,
                                'role_name_user' => $updatedRoleName,
                                'roles' => $roles
                            ]);
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
        }
    }

    /**
     * Deleting a user by a super_admin role
     *
     * @param [type] $userId
     * @return void
     */
    public function delete(int $userId)
    {
        $user = $this->userRepository->findById($userId);

        if (!$this->userIsConnected()) {
            $this->flashes('warning', 'Merci de te connecter!');
            header('Location: /security/login');
        } else {
            if (!$this->userRepository->findById($userId)) {
                $error404 = new ErrorController();
                $error404->pageNotFoundAction();
            } else {
                $currentUserRole = $this->roleRepository->findById($this->userIsConnected()->getRoleId());
                if ($currentUserRole->getName() !== "super_admin") {
                    $error403 = new ErrorController;
                    $error403->accessDenied();
                } else {
                    if ($user) {
                        $this->userRepository->delete($userId);
                        $this->flashes('success', 'Le Bubbles User' . ' ' . $user->getPseudo() . ' ' . 'a bien été supprimé.');
                        header('Location: /user/read');
                        return;
                    } else {
                        $error404 = new ErrorController;
                        $error404->pageNotFoundAction();
                    }
                }
            }
        }
    }
}
