<?php 
namespace App\Models;

use PDO;
use App\Utils\Database;

class User extends CoreModel {
    
    private $email;
    private $password;
    private $posts;

    /**
     * Méthode permettant de récupérer tous les enregistrements de la table user
     *
     * @return User
     */
    public static function findAll() 
    {
        $pdoDBConnexion = Database::getPDO();
        $sql = 'SELECT * FROM user';
        $pdoStatement = $pdoDBConnexion->prepare($sql);
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
    public static function findBy(int $userId) 
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
     * Méthode permettant de récupérer un user par son email
     *
     * @param string $email
     * @return User
     */
    public static function findByEmail(string $email) 
    {
        $pdoDBConnexion = Database::getPDO();

        $sql = '
            SELECT *
            FROM `user`
            WHERE `email` =:email
        ';

        $pdoStatement = $pdoDBConnexion->prepare($sql);
        // Méthode bindValue() permet de contraintes les types de données saisies 
        $pdoStatement->bindValue(':email', $email, PDO::PARAM_STR);
        $pdoStatement->execute();

        $user = $pdoStatement->fetchObject(self::class);

        return $user;
    }

    public function insert() 
    {
        $pdoDBConnexion = Database::getPDO();

        $sql = "
            INSERT INTO `user` (
                email,
                password
            )
            VALUES (
                :email,
                :password 
            )";

        // Préparer et sécuriser de la requête d'insertion qui retournera un objet PDOStatement
        $pdoStatement = $pdoDBConnexion->prepare($sql);
        $pdoStatement->execute([
            ':email,
            :passwoord'
        ]);

        if ($pdoStatement->rowCount() > 0) {
            $this->id = $pdoDBConnexion->lastInsertId();

            return true;
        }
        return false;
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