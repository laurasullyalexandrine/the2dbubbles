<?php 
namespace App\Models;

use PDO;
use App\Utils\Database;

class User {
    
    private $email;
    private $password;
    private $posts;

    /**
     * Méthode permettant de récupérer tous les enregistrements de la table user
     *
     * @return User
     */
    public function findAll() 
    {
        // Récupérer de l'objet PDO représentant la connexion à la DB
        $pdoDBConnexion = Database::getPDO();

        // Ecrire la requête sql
        $sql = 'SELECT * FROM user';

        // Préparer la requête
        $pdoStatement = $pdoDBConnexion->prepare($sql);

        // Exécuter la requête
        $pdoStatement->execute();

        $users = $pdoStatement->fetchAll(PDO::FETCH_CLASS, self::class);
        
        return $users;
    }
    /**
     *  Méthode permettant de récupérer un enregistrement de la table User en fonction d'un id donné
     *
     * @param [type] $userId
     * @return User
     */
    public static function findBy($userId) 
    {
        $pdoDBConnexion = Database::getPDO();
        $sql = '
        SELECT * 
        FROM user 
        WHERE id = :id';
        $pdoStatement = $pdoDBConnexion->prepare($sql);
        $pdoStatement->execute([
            'id' => $userId
        ]);
        $user = $pdoStatement->fetchObject(self::class);
 
        return $user;
    }

    /**
     * Get the value of email
     */ 
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @return  self
     */ 
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of password
     */ 
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set the value of password
     *
     * @return  self
     */ 
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }


    /**
     * Get the value of posts
     */ 
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * Set the value of posts
     *
     * @return  self
     */ 
    public function setPosts($posts)
    {
        $this->posts = $posts;

        return $this;
    }
}