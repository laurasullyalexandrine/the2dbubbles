<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;
use App\Utils\Database;
use App\Entity\Role;

class RoleRepository extends Database
{

    // /**
    //  * @var string
    //  */
    // private ?string $name = null;

    // /**
    //  * @var string
    //  */
    // private ?string $roleString = null;


    /**
     * Méthode permettant de récupérer tous les enregistrements de la table role
     *
     * @return array
     */
    public function findAll(): array
    {
        $sql = 'SELECT * FROM `role`';

        $pdoStatement = $this->dbh->prepare($sql);
        $pdoStatement->execute();
        $roles = $pdoStatement->fetchAll(PDO::FETCH_CLASS, self::class);

        return $roles;
    }

    /**
     * Méthode permettant de récupérer tous les enregistrements de la table role
     *
     * @return array
     */
    public function findByUser(): array
    {

        $sql = '
            SELECT * 
            FROM role
            LEFT JOIN user ON role.id = user.roles';


        $pdoStatement = $this->dbh->prepare($sql);
        $pdoStatement->execute();
        $roles = $pdoStatement->fetchAll(PDO::FETCH_CLASS, self::class);

        return $roles;
    }

    /**
     *  Méthode permettant de récupérer un enregistrement de la table Role en fonction d'un id donné
     *
     * @param [type] $roleId
     * @return Role
     */
    public function findById(int $roleId): Role
    {
        $sql = '
            SELECT * 
            FROM role 
            WHERE id = :id';

        $pdoStatement = $this->dbh->prepare($sql);
        $pdoStatement->execute([
            'id' => $roleId
        ]);
        $role = $pdoStatement->fetchObject(Role::class);

        return $role;
    }

    /**
     *  Méthode permettant de récupérer un enregistrement de la table Role en fonction d'un id donné
     *
     * @param [type] $roleId
     * @return Role
     */
    public function findByName(string $roleName): Role
    {
        $sql = "
            SELECT * 
            FROM role 
            WHERE name = :name
            "
        ;
        $pdoStatement = $this->dbh->prepare($sql);
        $pdoStatement->execute([
            'name' => $roleName
        ]);
        $role = $pdoStatement->fetchObject(Role::class);

        return $role;
    }

    /**
     * Méthode permettant d'ajouter un enregistrement dans la table role.
     * L'objet courant doit contenir toutes les données à ajouter : 1 propriété => 1 colonne dans la table
     *
     * @return bool
     */
    public function insert(Role $role): bool
    {
        $sql = "
            INSERT INTO `role` (name, roleString)
            VALUES (:name, :roleString)
            ";

        $pdoStatement = $this->dbh->prepare($sql);
        $pdoStatement->execute([
            'name' => $role->getName(),
            'roleString' => $role->getRoleString(),
        ]);

        if ($pdoStatement->rowCount() > 0) {
            return true;
        }
        
        return false;
    }

    /**
     * Méthode permetttant l'édition d'un rôle
     *
     */
    public function update(Role $role): bool
    {
        $sql = "
            UPDATE `role`
            SET 
                name = :name,
                roleString = :roleString,
                updated_at = NOW()
            WHERE id = :id
        ";

        $pdoStatement = $this->dbh->prepare($sql);
        $pdoStatement->bindValue(':id', $role->getId(), PDO::PARAM_INT);
        $pdoStatement->bindValue(':name', $role->getName(), PDO::PARAM_STR);
        $pdoStatement->bindValue(':roleString', $role->getRoleString(), PDO::PARAM_STR);

        return $pdoStatement->execute();
    }


    /**
     * Méthode permettant la supression d'un rôle
     *
     * @return bool
     */
    public function delete($id): bool
    {
        $sql = "
            DELETE FROM `role`
            WHERE id = :id
        ";
        $pdoStatement = $this->dbh->prepare($sql);
        $pdoStatement->bindValue(':id', $id, PDO::PARAM_INT);
        $pdoStatement->execute();

        return ($pdoStatement->rowCount() > 0);
    }

    // /**
    //  * Get the value of name
    //  */
    // public function getName(): ?string
    // {
    //     return $this->name;
    // }

    // /**
    //  * Set the value of name
    //  *
    //  * @return  self
    //  */
    // public function setName(string $name): self
    // {
    //     $this->name = $name;

    //     return $this;
    // }

    // /**
    //  * Get the value of roleString
    //  */
    // public function getRoleString(): ?string
    // {
    //     return $this->roleString;
    // }

    // /**
    //  * Set the value of roleString
    //  *
    //  * @return  self
    //  */
    // public function setRoleString(string $roleString): self
    // {
    //     $this->roleString = $roleString;

    //     return $this;
    // }

    // /**
    //  * Méthode permettant d'afficher les roles
    //  *
    //  * @return string
    //  */
    // public function getDisplayRole(): string
    // {
    //     if ($this->roleString === "ROLE_SUPER_ADMIN") {
    //         return "Super admin";
    //     } elseif ($this->roleString === "ROLE_SUPER_ADMIN") {
    //         return "admin";
    //     } else {
    //         return "utilisateur";
    //     }
    // }
}
