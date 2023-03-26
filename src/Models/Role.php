<?php 
namespace App\Models;

use PDO;
use App\Utils\Database;

class Role extends CoreModel {
    
    private $roleName;
    private $roleString;
    
    /**
     * Méthode permettant de récupérer tous les enregistrements de la table role
     *
     * @return Role
     */
    public static function findAll() 
    {
        $pdoDBConnexion = Database::getPDO();

        $sql = 'SELECT * FROM `role`';


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
    public static function findBy($roleId) 
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
     * Méthode permettant d'ajouter un enregistrement dans la table role.
     * L'objet courant doit contenir toutes les données à ajouter : 1 propriété => 1 colonne dans la table
     *
     * @return void
     */    
    public function insert()
    {
        $pdoDBConnexion = Database::getPDO();
        $sql = "
            INSERT INTO `role` (rolename, roleString)
            VALUES (:rolename, :roleString)"
            ;
        // dd($sql);
        $pdoStatement = $pdoDBConnexion->prepare($sql);
        $pdoStatement->execute([
            ':rolename' => $this->name,
            ':roleString' => $this->roleString,
        ]);

        if ($pdoStatement->rowCount() > 0) {
            $this->id = $pdoDBConnexion->lastInsertId();
            return true;
        } 

        return false;
    }

    public function update()
    {
        
    }

    public function delete()
    {
        
    }

    /**
     * Get the value of roleName
     */ 
    public function getRoleName()
    {
        $roleName = $this->roleName;
        $roleName = 'role_user';
        return $this->roleName;
    }

    /**
     * Set the value of roleName
     *
     * @return  self
     */ 
    public function setRoleName($roleName)
    {
        $this->roleName = $roleName;

        return $this;
    }

    /**
     * Get the value of roleString
     */ 
    public function getRoleString()
    {
        return $this->roleString;
    }

    /**
     * Set the value of roleString
     *
     * @return  self
     */ 
    public function setRoleString($roleString)
    {
        $this->roleString = $roleString;

        return $this;
    }

    public function getDisplayRole(): string
    {
        if ($this->roleString === "ROLE_SUPER_ADMIN") {
            return "Super Administrateur";
        } elseif ($this->roleString === "ROLE_SUPER_ADMIN") {
            return "Administrateur";
        } else {
            return "2dbubblebum";
        }
    }


}