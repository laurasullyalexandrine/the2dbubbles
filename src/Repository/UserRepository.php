<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;
use App\Utils\Database;
use App\Entity\User;

class UserRepository extends Database
{
    const HASH_COST = 12;

    /**
     * Method to retrieve all records from user table
     *
     * @return array
     */
    public function findAll(): array
    {
        $sql = "
            SELECT u.id, pseudo, email, r.name AS role
            FROM user u
            LEFT JOIN role r
            ON r.id = u.roleId
            WHERE u.roleId 
            ORDER BY u.roleId ASC
            ";
        $pdoStatement = $this->dbh->prepare($sql);
        $pdoStatement->execute();
        $users = $pdoStatement->fetchAll(PDO::FETCH_CLASS, User::class);

        return $users;
    }

    /**
     * Method to retrieve a record from the User table based on a given id
     *
     * @param integer $userId
     * @return ?User
     */
    public function findById(int $userId): ?User
    {
        $sql = "
            SELECT * 
            FROM user 
            WHERE id = :id
            ";
        $pdoStatement = $this->dbh->prepare($sql);
        $pdoStatement->bindValue(':id', $userId, PDO::PARAM_INT);
        $pdoStatement->execute();

        $user = $pdoStatement->fetchObject(User::class);

        return $user instanceof User ? $user : null;
    }

    /**
     * Method to retrieve a user by his email
     *
     * @param string $email
     * @return ?User
     */
    public function findByEmail(string $email): ?User
    {
        $sql = "
            SELECT *
            FROM user
            WHERE email = :email
            ";
        $pdoStatement = $this->dbh->prepare($sql);
        // bindValue() method allows constraints on input data types
        $pdoStatement->bindValue(':email', $email, PDO::PARAM_STR);
        $pdoStatement->execute();

        $user = $pdoStatement->fetchObject(User::class);

        return $user instanceof User ? $user : null;
    }


    public function findBySlug(string $slug): ?User
    {
        $sql = "
            SELECT *
            FROM user
            WHERE slug = :slug
            ";
        $pdoStatement = $this->dbh->prepare($sql);
        // bindValue() method allows constraints on input data types
        $pdoStatement->bindValue(':slug', $slug, PDO::PARAM_STR);
        $pdoStatement->execute();

        $user = $pdoStatement->fetchObject(User::class);

        return $user instanceof User ? $user : null;
    }

    /**
     * Method to find posts and comments by nickname
     * 
     * @param string $pseudo
     * @return ?User
     */
    public function findByPseudo(string $pseudo): ?User
    {
        $sql = "
            SELECT * 
            FROM user 
            WHERE pseudo = :pseudo
            ";

        $pdoStatement = $this->dbh->prepare($sql);
        $pdoStatement->bindValue(':pseudo', $pseudo, PDO::PARAM_STR);
        $pdoStatement->execute();

        $user = $pdoStatement->fetchObject(User::class);

        return $user instanceof User ? $user : null;
    }

    /**
     * Find a user by his token
     *
     * @param string $token
     * @return ?User
     */
    public function findOneByToken(string $token): ?User
    {
        $sql = "
            SELECT * 
            FROM user 
            WHERE token = :token
            ";

        $pdoStatement = $this->dbh->prepare($sql);
        $pdoStatement->bindValue(':token', $token, PDO::PARAM_STR);
        $pdoStatement->execute();

        $user = $pdoStatement->fetchObject(User::class);

        return $user instanceof User ? $user : null;
    }

    /**
     * Method to modify a user
     *
     * @return bool
     */
    public function insert(User $user): bool
    {
        $sql = "
            INSERT INTO user (
                pseudo,
                slug,
                email,
                password,
                roleId
            )
            VALUES (
                :pseudo,
                :slug,
                :email,
                :password,
                :roleId
            )
        ";

        // Prepare and secure the insert query that will return a PDOStatement object
        $pdoStatement = $this->dbh->prepare($sql);
        $pdoStatement->execute([
            'pseudo' => $user->getPseudo(),
            'slug' => $user->getSlug(),
            'email' => $user->getEmail(),
            'password' => $user->getPassword(),
            'roleId' => $user->getRoleId(),
        ]);


        if ($pdoStatement->rowCount() > 0) {
            return true;
        }
        return false;
    }

    /**
     * Update a user
     *
     * @return void
     */
    public function update(User $user): bool
    {
        $sql = "
            UPDATE user
            SET 
                pseudo = :pseudo,
                email = :email,
                password = :password,
                token = :token,
                roleId = :roleId,
                updated_at = NOW()
            WHERE id = :id
        ";

        $pdoStatement = $this->dbh->prepare($sql);
        $pdoStatement->bindValue(':id', $user->getId(), PDO::PARAM_INT);
        $pdoStatement->bindValue('pseudo', $user->getPseudo(), PDO::PARAM_STR);
        $pdoStatement->bindValue(':email', $user->getEmail(), PDO::PARAM_STR);
        $pdoStatement->bindValue(':password', $user->getPassword(), PDO::PARAM_STR);
        $pdoStatement->bindValue(':token', $user->getToken(), PDO::PARAM_STR);
        $pdoStatement->bindValue(':roleId', $user->getRoleId(), PDO::PARAM_INT);

        return $pdoStatement->execute();
    }


    /**
     * Method for deleting a user
     *
     * @return bool
     */
    public function delete(int $id): bool
    {
        $sql = "
            DELETE 
            FROM user
            WHERE id = :id
            ";

        $pdoStatement = $this->dbh->prepare($sql);
        // Used to associate a value with a parameter and to constrain the expected data
        $pdoStatement->bindValue(':id', $id, PDO::PARAM_INT);
        $pdoStatement->execute();

        // Returns true if at least one row has been deleted
        return ($pdoStatement->rowCount() > 0);
    }
}
