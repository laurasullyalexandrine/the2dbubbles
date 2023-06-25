<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;
use App\Utils\Database;
use App\Entity\Role;

class RoleRepository extends Database
{
    /**
     * Method to retrieve all records from role table
     *
     * @return array
     */
    public function findAll(): array
    {
        $sql = 'SELECT * FROM `role`';

        $pdoStatement = $this->dbh->prepare($sql);
        $pdoStatement->execute();
        $roles = $pdoStatement->fetchAll(PDO::FETCH_CLASS, Role::class);

        return $roles;
    }

    /**
     * Method to retrieve all records from role table
     *
     * @return array
     */
    public function findByUser(): array
    {
        $sql = "
            SELECT * 
            FROM role
            LEFT JOIN user ON role.id = user.roles
            "
        ;

        $pdoStatement = $this->dbh->prepare($sql);
        $pdoStatement->execute();
        $roles = $pdoStatement->fetchAll(PDO::FETCH_CLASS, Role::class);

        return $roles;
    }

    /**
     *  Method to retrieve a record from the Role table based on a given id
     *
     * @param [type] $roleId
     * @return ?Role
     */
    public function findById(int $roleId): ?Role
    {
        $sql = "
            SELECT * 
            FROM role 
            WHERE id = :id
            "
            ;

        $pdoStatement = $this->dbh->prepare($sql);
        $pdoStatement->bindValue(':id', $roleId, PDO::PARAM_INT);
        $pdoStatement->execute();

        $role = $pdoStatement->fetchObject(Role::class);

        return $role instanceof Role ? $role : null;
    }

    /**
     *  Method to retrieve a record from the Role table based on a given id
     *
     * @param [type] $roleId
     * @return ?Role
     */
    public function findByName(string $roleName): ?Role
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

        return $role instanceof Role ? $role : null;
    }

    /**
     * Method to add a record in the role table
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
     * Method for editing a role
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
     * Method for deleting a role
     *
     * @return bool
     */
    public function delete(int $id): bool
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
}
