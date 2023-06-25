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
     * Méthode permettant de récupérer tous les enregistrements de la table user
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
     * Méthode permettant de récupérer un enregistrement de la table User en fonction d'un id donné
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
     * Méthode permettant de récupérer un user par son email
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
        // Méthode bindValue() permet de contraintes les types de données saisies 
        $pdoStatement->bindValue(':email', $email, PDO::PARAM_STR);
        $pdoStatement->execute();

        $user = $pdoStatement->fetchObject(User::class);

        return $user instanceof User ? $user : null;
    }

    /**
     * Méthode permettant de trouver les posts et commentaire par son pseudo
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
     * Trouver un user par son token
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
     * Méthode permettant de modifier un user
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

        // Préparer et sécuriser de la requête d'insertion qui retournera un objet PDOStatement
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
     * Mettre à jour un utilisateur
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
     * Méthode permettant la supression d'un utilisateur
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
        // Permet d'associer une valeur à un paramètre et de contraindre la donnée attendue
        $pdoStatement->bindValue(':id', $id, PDO::PARAM_INT);
        $pdoStatement->execute();

        // Retourne vrai si au moins une ligne a été supprimée
        return ($pdoStatement->rowCount() > 0);
    }
}
