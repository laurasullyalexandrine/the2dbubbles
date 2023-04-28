<?php

declare(strict_types=1);

namespace App\Models;

use PDO;
use App\Utils\Database;

class Comment extends CoreModel
{
    /**
     * @var string
     */
    private $content;

    /**
     * @var bool
     */
    private $status;

    /**
     * @var int
     */
    private $users;

    /**
     * @var int
     */
    private $posts;

    /**
     *  Méthode permettant de récupérer un enregistrement de la table Comment en fonction d'un id donné
     *
     * @param [type] $commentId
     * @return Comment
     */
    public static function findById($commentId)
    {
        $pdoDBConnexion = Database::getPDO();
        $sql = "
            SELECT * 
            FROM comment 
            WHERE id = :id
            "
        ;
        $pdoStatement = $pdoDBConnexion->prepare($sql);
        $pdoStatement->bindValue(':id', $commentId, PDO::PARAM_INT);
        $pdoStatement->execute();

        $comment = $pdoStatement->fetchObject(self::class);

        return $comment;
    }

    /**
     * Méthode permettant de récupérer tous les commentaires d'un même auteur
     *
     * @return Post
     */
    public static function findByUser($pseudo)
    {
        $pdoDBConnexion = Database::getPDO();

        $sql = "
            SELECT c.id, c.content, c.status, c.created_at, c.updated_at, u.pseudo AS pseudo, p.title AS post
            FROM comment c
            LEFT JOIN user u
            ON u.id = c.users
            LEFT JOIN post p
            ON p.id = c.posts
            WHERE pseudo = :pseudo
            ORDER BY created_at ASC
            "
        ;

        $pdoStatement = $pdoDBConnexion->prepare($sql);
        $pdoStatement->bindValue(':pseudo', $pseudo, PDO::PARAM_STR);
        $pdoStatement->execute();

        $comments = $pdoStatement->fetchAll(PDO::FETCH_CLASS, self::class);
    
        return $comments;
    }

    /**
     * Permet de trouver tous les commentaires d'un Post
     *
     * @param [type] $slug
     * @return Comment
     */
    public static function findBySlugPost($slug)
    {
        $pdoDBConnexion = Database::getPDO();
        $sql = "
            SELECT c.id, c.content, c.status, c.created_at, c.updated_at, p.slug AS slug, u.pseudo
            FROM comment c
            LEFT JOIN post p
            ON p.id = c.posts
            LEFT JOIN user u
            ON u.id = c.users
            WHERE p.slug = :slug
            ORDER BY c.created_at ASC 
            "
        ;
        $pdoStatement = $pdoDBConnexion->prepare($sql);
        $pdoStatement->bindValue(':slug', $slug, PDO::PARAM_STR);
        $pdoStatement->execute();

        $comments = [];
        while($comment = $pdoStatement->fetchObject(self::class)) {
            $comments[] = $comment;
        }
 
        return $comments;
    }

    /**
     * Méthode permettant d'ajouter un enregistrement dans la table comment.
     * L'objet courant doit contenir toutes les données à ajouter : 1 propriété => 1 colonne dans la table
     *
     * @return bool
     */
    public function insert(): bool
    {
        $pdoDBConnexion = Database::getPDO();
        $sql = "
            INSERT INTO comment(
                    posts, 
                    users, 
                    content, 
                    status
                )
                VALUES (
                    :posts, 
                    :users, 
                    :content, 
                    :status
                )
            "
        ;

        $pdoStatement = $pdoDBConnexion->prepare($sql);
        $pdoStatement->execute([
            'posts' => $this->posts,
            'users' => $this->users,
            'content' => $this->content,
            'status' => $this->status
        ]);
    
        if ($pdoStatement->rowCount() > 0) {
            $this->id = $pdoDBConnexion->lastInsertId();
            return true;
        } 

        return false;
    }

    /**
     * Méthode permettant l'édition d'un commentaire
     *
     * @return Comment
     */
    public function update()
    {
        $pdoDBConnexion = Database::getPDO();

        $sql = "
            UPDATE comment
            SET 
                content = :content,
                status = :status,
                updated_at = NOW()
            WHERE id = :id
        ";

        $pdoStatement = $pdoDBConnexion->prepare($sql);
        $pdoStatement->bindValue(':content', $this->content, PDO::PARAM_STR);
        $pdoStatement->bindValue(':status', $this->status, PDO::PARAM_BOOL);

        return $pdoStatement->execute();
    }


    /**
     * Méthode permettant la supression d'un commentaire
     *
     * @return bool
     */
    public function delete()
    {
        $pdoDBConnexion = Database::getPDO();

        $sql = "
            DELETE 
            FROM comment
            WHERE id = :id
            "
        ;
        $pdoStatement = $pdoDBConnexion->prepare($sql);
        $pdoStatement->bindValue(':id', $this->id, PDO::PARAM_INT);
        $pdoStatement->execute();
        
        return ($pdoStatement->rowCount() > 0);
    }

    /**
     * Get the content of the entities Post and Comment
     *
     * @return  string
     */ 
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set the content of the entities Post and Comment
     *
     * @param  string  $content  The content of the entities Post and Comment
     *
     * @return  self
     */ 
    public function setContent(string $content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get the status of the entities Post and Comment
     *
     * @return  bool
     */ 
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the status of the entities Post and Comment
     *
     * @param  bool  $status  The status of the entities Post and Comment
     *
     * @return  self
     */ 
    public function setStatus(bool $status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get the value of users
     *
     * @return  int
     */ 
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Set the value of users
     *
     * @param  int  $users
     *
     * @return  self
     */ 
    public function setUsers(int $users)
    {
        $this->users = $users;

        return $this;
    }

    /**
     * Get the value of posts
     *
     * @return  int
     */ 
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * Set the value of posts
     *
     * @param  int  $posts
     *
     * @return  self
     */ 
    public function setPosts(int $posts)
    {
        $this->posts = $posts;

        return $this;
    }
}
