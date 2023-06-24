<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;
use App\Entity\Comment;
use App\Utils\Database;

class CommentRepository extends Database
{
    // /**
    //  * @var string
    //  */
    // private string $content;

    // /**
    //  * @var int
    //  */
    // private int $status;

    // /**
    //  * @var int
    //  */
    // private ?int $userId = null;

    // /**
    //  * @var int
    //  */
    // private ?int $postId = null;

    /**
     * Méthode permettant de récupérer tous les commentaires
     *
     * @return array
     */
    public static function findAll(): array
    {
        $pdoDBConnexion = Database::getPDO();

        $sql = "
            SELECT *
            FROM comment
            "
        ;
        
        $pdoStatement = $pdoDBConnexion->prepare($sql);
        $pdoStatement->execute();
        $comments = $pdoStatement->fetchAll(PDO::FETCH_CLASS, Comment::class);

        return $comments;
    }

    /**
     *  Méthode permettant de récupérer un enregistrement de la table Comment en fonction d'un id donné
     *
     * @param [type] $commentId
     * @return Comment
     */
    public function findById(int $commentId): Comment
    {
        $sql = "
            SELECT * 
            FROM comment 
            WHERE id = :id
            "
        ;
        $pdoStatement = $this->dbh->prepare($sql);
        $pdoStatement->bindValue(':id', $commentId, PDO::PARAM_INT);
        $pdoStatement->execute();

        $comment = $pdoStatement->fetchObject(Comment::class);

        return $comment;
    }

    /**
     * Méthode permettant de récupérer tous les commentaires d'un même auteur
     *
     * @return array
     */
    public function findByUser(string $slug): array
    {
        $sql = "
            SELECT c.id, c.content, c.status, c.created_at, c.updated_at, u.slug AS user, p.title AS post
            FROM comment c
            LEFT JOIN user u
            ON u.id = c.users
            LEFT JOIN post p
            ON p.id = c.posts
            WHERE u.slug = :slug
            ORDER BY created_at ASC
            "
        ;

        $pdoStatement = $this->dbh->prepare($sql);
        $pdoStatement->bindValue(':slug', $slug, PDO::PARAM_STR);
        $pdoStatement->execute();

        $comments = $pdoStatement->fetchAll(PDO::FETCH_CLASS, Comment::class);

        return $comments;
    }

    /**
     * Permet de trouver tous les commentaires d'un Post
     *
     * @param [type] $slug
     * @return array
     */
    public function findBySlugPost(string $slug): array
    {
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
        $pdoStatement = $this->dbh->prepare($sql);
        $pdoStatement->bindValue(':slug', $slug, PDO::PARAM_STR);
        $pdoStatement->execute();

        $comments = [];
        while($comment = $pdoStatement->fetchObject(Comment::class)) {
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
    public function insert(Comment $comment): bool
    {
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

        $pdoStatement = $this->dbh->prepare($sql);
        $pdoStatement->execute([
            'postId' => $comment->getPostId(),
            'userId' => $comment->getUserId(),
            'content' => $comment->getContent(),
            'status' => $comment->getStatus()
        ]);
    
        if ($pdoStatement->rowCount() > 0) {
            return true;
        } 

        return false;
    }

    /**
     * Méthode permettant l'édition d'un commentaire
     *
     * @return bool
     */
    public function update(Comment $comment): bool
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
        $pdoStatement->bindValue(':id', $comment->getId(), PDO::PARAM_INT);
        $pdoStatement->bindValue(':content', $comment->getContent(), PDO::PARAM_STR);
        $pdoStatement->bindValue(':status', $comment->getStatus(), PDO::PARAM_INT);

        return $pdoStatement->execute();
    }


    /**
     * Méthode permettant la supression d'un commentaire
     *
     * @return bool
     */
    public function delete(int $id): bool
    {
        $sql = "
            DELETE 
            FROM comment
            WHERE id = :id
            "
        ;
        $pdoStatement = $this->dbh->prepare($sql);
        $pdoStatement->bindValue(':id', $id, PDO::PARAM_INT);
        $pdoStatement->execute();
        
        return ($pdoStatement->rowCount() > 0);
    }

    // /**
    //  * Get the content of the entities Post and Comment
    //  *
    //  * @return  string
    //  */ 
    // public function getContent(): string
    // {
    //     return $this->content;
    // }

    // /**
    //  * Set the content of the entities Post and Comment
    //  *
    //  * @param  string  $content  The content of the entities Post and Comment
    //  *
    //  * @return  self
    //  */ 
    // public function setContent(string $content): self
    // {
    //     $this->content = $content;

    //     return $this;
    // }
    
    // /**
    //  * Get the value of status
    //  *
    //  * @return  int
    //  */ 
    // public function getStatus(): int
    // {
    //     return $this->status;
    // }

    // /**
    //  * Set the value of status
    //  *
    //  * @param  int  $status
    //  *
    //  * @return  self
    //  */ 
    // public function setStatus(int $status): self
    // {
    //     $this->status = $status;

    //     return $this;
    // }
    
    // /**
    //  * Get the value of userId
    //  *
    //  * @return  int
    //  */ 
    // public function getUserId()
    // {
    //     return $this->userId;
    // }

    // /**
    //  * Set the value of userId
    //  *
    //  * @param  int  $userId
    //  *
    //  * @return  self
    //  */ 
    // public function setUserId(int $userId)
    // {
    //     $this->userId = $userId;

    //     return $this;
    // }

    // /**
    //  * Get the value of postId
    //  *
    //  * @return  int
    //  */ 
    // public function getPostId()
    // {
    //     return $this->postId;
    // }

    // /**
    //  * Set the value of postId
    //  *
    //  * @param  int  $postId
    //  *
    //  * @return  self
    //  */ 
    // public function setPostId(int $postId)
    // {
    //     $this->postId = $postId;

    //     return $this;
    // }
}
