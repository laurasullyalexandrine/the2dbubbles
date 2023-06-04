<?php

declare(strict_types=1);

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
    private $slug;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $token;

    /**
     * @var int
     */
    private $roles;


    public function __toString()
    {
        return $this->pseudo;
    }

    /**
     * Méthode permettant de récupérer tous les enregistrements de la table user
     *
     * @return array
     */
    public static function findAll(): array
    {
        $pdoDBConnexion = Database::getPDO();
        $sql = "
            SELECT u.id, pseudo, email, r.name AS role
            FROM user u
            LEFT JOIN role r
            ON r.id = u.roles
            WHERE u.roles 
            ORDER BY u.roles ASC
            "
        ;
        $pdoStatement = $pdoDBConnexion->prepare($sql);
        $pdoStatement->execute();
        $users = $pdoStatement->fetchAll(PDO::FETCH_CLASS, self::class);

        return $users;
    }

    /**
     * Méthode permettant de récupérer un enregistrement de la table User en fonction d'un id donné
     *
     * @param integer $userId
     * @return User
     */
    public static function findById(int $userId)
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

        $sql = "
            SELECT *
            FROM user
            WHERE email = :email
            "
        ;
        $pdoStatement = $pdoDBConnexion->prepare($sql);
        // Méthode bindValue() permet de contraintes les types de données saisies 
        $pdoStatement->bindValue(':email', $email, PDO::PARAM_STR);
        $pdoStatement->execute();

        $user = $pdoStatement->fetchObject(self::class);

        return $user;
    }

    /**
     * Méthode permettant de trouver les posts et commentaire par son pseudo
     *
     * @param string $pseudo
     * @return User
     */
    public static function findByPseudo(string $pseudo) 
    {
        $pdoDBConnexion = Database::getPDO();

        $sql = "
            SELECT * 
            FROM user 
            WHERE pseudo = :pseudo
            "
        ;

        $pdoStatement = $pdoDBConnexion->prepare($sql);
        $pdoStatement->bindValue(':pseudo', $pseudo, PDO::PARAM_STR);
        $pdoStatement->execute();

        $user = $pdoStatement->fetchObject(self::class);

        return $user;
        
    }

    /**
     * Trouver un user par son token
     *
     * @param string $token
     * @return User
     */
    public static function findOneByToken(string $token): User
    {
        $pdoDBConnexion = Database::getPDO();

        $sql = "
            SELECT * 
            FROM user 
            WHERE token = :token
            "
        ;

        $pdoStatement = $pdoDBConnexion->prepare($sql);
        $pdoStatement->bindValue(':token', $token, PDO::PARAM_STR);
        $pdoStatement->execute();

        $user = $pdoStatement->fetchObject(self::class);

        return $user;
    }

    /**
     * Méthode permettant de modifier un user
     *
     * @return bool
     */
    public function insert(): bool
    {
        $pdoDBConnexion = Database::getPDO();

        $sql = "
            INSERT INTO user (
                pseudo,
                slug,
                email,
                password,
                roles
            )
            VALUES (
                :pseudo,
                :slug,
                :email,
                :password,
                :roles
            )
        "
    ;

        // Préparer et sécuriser de la requête d'insertion qui retournera un objet PDOStatement
        $pdoStatement = $pdoDBConnexion->prepare($sql);
            $pdoStatement->execute([
                'pseudo' => $this->pseudo,
                'slug' => $this->slug,
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

    /**
     * Mettre à jour un utilisateur
     *
     * @return void
     */
    public function update()
    {
        $pdoDBConnexion = Database::getPDO();

        $sql = "
            UPDATE user
            SET 
                pseudo = :pseudo,
                email = :email,
                password = :password,
                token = :token,
                roles = :roles,
                updated_at = NOW()
            WHERE id = :id
        "
    ;

        $pdoStatement = $pdoDBConnexion->prepare($sql);
        $pdoStatement->bindValue(':id', $this->id, PDO::PARAM_INT);
        $pdoStatement->bindValue('pseudo', $this->pseudo, PDO::PARAM_STR);
        $pdoStatement->bindValue(':email', $this->email, PDO::PARAM_STR);
        $pdoStatement->bindValue(':password', $this->password, PDO::PARAM_STR);
        $pdoStatement->bindValue(':token', $this->token, PDO::PARAM_STR);
        $pdoStatement->bindValue(':roles', $this->roles, PDO::PARAM_INT);

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
            DELETE 
            FROM user
            WHERE id = :id
            "
        ;

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
     * Get the value of slug
     *
     * @return  string
     */ 
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set the value of slug
     *
     * @param  string  $slug
     *
     * @return  self
     */ 
    public function setSlug(string $slug)
    {
        $this->slug = $slug;

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
    public function setEmail(string $email): self
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
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get the value of token
     *
     * @return  string
     */ 
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set the value of token
     *
     * @param  string  $token
     *
     * @return  self
     */ 
    public function setToken(string $token)
    {
        $this->token = $token;

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
    public function setRoles(array $roles)
    {
        $this->roles = $roles;

        return $this;
    }
}
