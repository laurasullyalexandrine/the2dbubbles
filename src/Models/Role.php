<?php

declare(strict_types=1);

namespace App\Models;

use PDO;
use App\Utils\Database;

class Role extends CoreModel
{


    /**
     * @var string
     */
    private string $name;

    /**
     * @var string
     */
    private string $roleString;


    public function __toString(): string
    {
        return $this->name;
    }


    /**
     * Méthode permettant de récupérer tous les enregistrements de la table role
     *
     * @return array
     */
    public static function findAll(): array
    {
        $pdoDBConnexion = Database::getPDO();

        $sql = 'SELECT * FROM `role`';

        $pdoStatement = $pdoDBConnexion->prepare($sql);
        $pdoStatement->execute();
        $roles = $pdoStatement->fetchAll(PDO::FETCH_CLASS, self::class);

        return $roles;
    }

    /**
     * Méthode permettant de récupérer tous les enregistrements de la table role
     *
     * @return array
     */
    public static function findByUser(): array
    {
        $pdoDBConnexion = Database::getPDO();

        $sql = '
            SELECT * 
            FROM role
            LEFT JOIN user ON role.id = user.roles';


        $pdoStatement = $pdoDBConnexion->prepare($sql);
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
    public static function findById(int $roleId): Role
    {
        $pdoDBConnexion = Database::getPDO();
        $sql = '
            SELECT * 
            FROM role 
            WHERE id = :id';

        $pdoStatement = $pdoDBConnexion->prepare($sql);
        $pdoStatement->execute([
            'id' => $roleId
        ]);
        $role = $pdoStatement->fetchObject(self::class);

        return $role;
    }

    /**
     *  Méthode permettant de récupérer un enregistrement de la table Role en fonction d'un id donné
     *
     * @param [type] $roleId
     * @return Role
     */
    public static function findByName(string $roleName): Role
    {
        $pdoDBConnexion = Database::getPDO();
        $sql = '
            SELECT * 
            FROM role 
            WHERE name = :name';
        $pdoStatement = $pdoDBConnexion->prepare($sql);
        $pdoStatement->execute([
            'name' => $roleName
        ]);
        $role = $pdoStatement->fetchObject(self::class);

        return $role;
    }

    /**
     * Méthode permettant d'ajouter un enregistrement dans la table role.
     * L'objet courant doit contenir toutes les données à ajouter : 1 propriété => 1 colonne dans la table
     *
     * @return bool
     */
    public function insert(): bool
    {
        $pdoDBConnexion = Database::getPDO();
        $sql = "
            INSERT INTO `role` (name, roleString)
            VALUES (:name, :roleString)
            ";

        $pdoStatement = $pdoDBConnexion->prepare($sql);
        $pdoStatement->execute([
            'name' => $this->name,
            'roleString' => $this->roleString,
        ]);

        if ($pdoStatement->rowCount() > 0) {
            $this->id = $pdoDBConnexion->lastInsertId();
            return true;
        }
        
        return false;
    }

    /**
     * Méthode permetttant l'édition d'un rôle
     *
     */
    public function update(): void
    {
        $pdoDBConnexion = Database::getPDO();

        $sql = "
            UPDATE `role`
            SET 
                name = :name,
                roleString = :roleString,
                updated_at = NOW()
            WHERE id = :id
        ";

        $pdoStatement = $pdoDBConnexion->prepare($sql);
        $pdoStatement->bindValue(':id', $this->id, PDO::PARAM_INT);
        $pdoStatement->bindValue(':name', $this->name, PDO::PARAM_STR);
        $pdoStatement->bindValue(':roleString', $this->roleString, PDO::PARAM_STR);

        $pdoStatement->execute();
    }


    /**
     * Méthode permettant la supression d'un rôle
     *
     * @return bool
     */
    public function delete(): bool
    {
        $pdoDBConnexion = Database::getPDO();

        $sql = "
            DELETE FROM `role`
            WHERE id = :id
        ";
        $pdoStatement = $pdoDBConnexion->prepare($sql);
        $pdoStatement->bindValue(':id', $this->id, PDO::PARAM_INT);
        $pdoStatement->execute();

        return ($pdoStatement->rowCount() > 0);
    }

    /**
     * Get the value of name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of roleString
     */
    public function getRoleString(): ?string
    {
        return $this->roleString;
    }

    /**
     * Set the value of roleString
     *
     * @return  self
     */
    public function setRoleString(string $roleString): self
    {
        $this->roleString = $roleString;

        return $this;
    }

    /**
     * Méthode permettant d'afficher les roles
     *
     * @return string
     */
    public function getDisplayRole(): string
    {
        if ($this->roleString === "ROLE_SUPER_ADMIN") {
            return "Super admin";
        } elseif ($this->roleString === "ROLE_SUPER_ADMIN") {
            return "admin";
        } else {
            return "utilisateur";
        }
    }
}
