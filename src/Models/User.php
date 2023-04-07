<?php

namespace App\Models;

use PDO;
use App\Utils\Database;
use Exception;

class User extends CoreModel
{
    const HASH_COST = 12;

    /**
     * @var string
     */
    private $pseudo;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $password;

    /**
     * @var int
     */
    private $roles;

    /**
     * @var int
     */
    private $comments;

    /**
     * Méthode permettant de récupérer tous les enregistrements de la table user
     *
     * @return User
     */
    public static function findAll()
    {
        $pdoDBConnexion = Database::getPDO();
        $sql = "
            SELECT user.id, pseudo, email, role.name AS role
            FROM user
            INNER JOIN role ON role.id = user.roles
            WHERE user.roles 
            ORDER BY user.email ASC
            "
        ;
        $pdoStatement = $pdoDBConnexion->prepare($sql);
        $pdoStatement->execute();
        $users = $pdoStatement->fetchAll(PDO::FETCH_CLASS, self::class);
        dd($users);
        return $users;
    }

    public static function findBy(int $userId)
    {
        $pdoDBConnexion = Database::getPDO();
        $sql = "
            SELECT * 
            FROM user 
            WHERE id = :id
            "
        ;
        $pdoStatement = $pdoDBConnexion->prepare($sql);
        $pdoStatement->bindValue(':id', $userId, PDO::PARAM_INT);
        $pdoStatement->execute();

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
            WHERE `email` = :email
        ';
        $pdoStatement = $pdoDBConnexion->prepare($sql);
        // Méthode bindValue() permet de contraintes les types de données saisies 
        $pdoStatement->bindValue(':email', $email, PDO::PARAM_STR);
        $pdoStatement->execute();

        $user = $pdoStatement->fetchObject(self::class);

        return $user;
    }

    /**
     * Méthode permettant de modifier un user
     *
     * @return void
     */
    public function insert()
    {
        $pdoDBConnexion = Database::getPDO();

        $sql = "
            INSERT INTO `user` (
                email,
                password,
                roles
            )
            VALUES (
                :email,
                :password,
                :roles
            )";

        // Préparer et sécuriser de la requête d'insertion qui retournera un objet PDOStatement
        $pdoStatement = $pdoDBConnexion->prepare($sql);
            $pdoStatement->execute([
                'email' => $this->email,
                'password' => $this->password,
                'roles' => $this->roles,
            ]);


        if ($pdoStatement->rowCount() > 0) {
            $this->id = $pdoDBConnexion->lastInsertId();

            return true;
        }
        return false;
    }


    public function update()
    {
        $pdoDBConnexion = Database::getPDO();

        $sql = "
            UPDATE `user`
            SET 
                pseudo = :pseudo,
                email = :email,
                password = :password,
                roles = :roles,
                updated_at = NOW()
            WHERE id = :id
        ";

        $pdoStatement = $pdoDBConnexion->prepare($sql);
        $pdoStatement->bindValue(':id', $this->id, PDO::PARAM_INT);
        $pdoStatement->bindValue('pseudo', $this->pseudo, PDO::PARAM_STR);
        $pdoStatement->bindValue(':email', $this->email, PDO::PARAM_STR);
        $pdoStatement->bindValue(':password', $this->password, PDO::PARAM_STR);
        $pdoStatement->bindValue(':roles', $this->roles, PDO::PARAM_INT);
        // dd($pdoStatement);
        return $pdoStatement->execute();
    }


    /**
     * Méthode permettant la supression d'un utilisateur
     *
     * @return bool
     */
    public function delete()
    {
        $pdoDBConnexion = Database::getPDO();

        $sql = "
            DELETE FROM `user`
            WHERE id = :id
        ";
        $pdoStatement = $pdoDBConnexion->prepare($sql);

        // Permet d'associer une valeur à un paramètre et de contraindre la donnée attendue
        $pdoStatement->bindValue(':id', $this->id, PDO::PARAM_INT);
        $pdoStatement->execute();

        // Retourne vrai si au moins une ligne a été supprimée
        return ($pdoStatement->rowCount() > 0);
    }

    /**
     * Get the value of pseudo
     *
     * @return  string
     */ 
    public function getPseudo()
    {
        return $this->pseudo;
    }

    /**
     * Set the value of pseudo
     *
     * @param  string  $pseudo
     *
     * @return  self
     */ 
    public function setPseudo(string $pseudo)
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    /**
     * Get the value of email
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @return  self
     */
    public function setEmail($email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of password
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Set the value of password
     *
     * @return  self
     */
    public function setPassword($password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get the value of roles
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Set the value of roles
     *
     * @return  self
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get the value of comments
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Set the value of comments
     *
     * @return  self
     */
    public function setComments($comments)
    {
        $this->comments = $comments;

        return $this;
    }
}
