<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;
use App\Entity\Comment;
use App\Utils\Database;

class CommentRepository extends Database
{
    /**
     * Method to retrieve all comments
     *
     * @return array
     */
    public function findAll(): array
    {
        $sql = "
            SELECT *
            FROM comment
            ORDER BY created_at DESC
            "
        ;
        
        $pdoStatement = $this->dbh->prepare($sql);
        $pdoStatement->execute();
        $comments = $pdoStatement->fetchAll(PDO::FETCH_CLASS, Comment::class);

        return $comments;
    }

    /**
     *  Method to retrieve a record from the Comment table given a given id
     *
     * @param [type] $commentId
     * @return ?Comment
     */
    public function findById(int $commentId): ?Comment
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

        return $comment instanceof Comment ? $comment : null;
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
            ON u.id = c.userId
            LEFT JOIN post p
            ON p.id = c.postId
            WHERE u.slug = :slug
            ORDER BY created_at DESC
            "
        ;

        $pdoStatement = $this->dbh->prepare($sql);
        $pdoStatement->bindValue(':slug', $slug, PDO::PARAM_STR);
        $pdoStatement->execute();

        $comments = $pdoStatement->fetchAll(PDO::FETCH_CLASS, Comment::class);

        return $comments;
    }

    /**
     * Allows you to find all the comments of a Post
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
            ON p.id = c.postId
            LEFT JOIN user u
            ON u.id = c.userId
            WHERE p.slug = :slug
            ORDER BY c.created_at DESC 
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
     * Method for adding a record to the comment table.
     * The current object must contain all the data to add: 1 property => 1 column in the table
     *
     * @return bool
     */
    public function insert(Comment $comment): bool
    {
        $sql = "
            INSERT INTO comment(
                    postId, 
                    userId, 
                    content, 
                    status
                )
                VALUES (
                    :postId, 
                    :userId, 
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
     * Method for editing a comment
     *
     * @return bool
     */
    public function update(Comment $comment): bool
    {
        $sql = "
            UPDATE comment
            SET 
                content = :content,
                status = :status,
                updated_at = NOW()
            WHERE id = :id
        ";

        $pdoStatement = $this->dbh->prepare($sql);
        $pdoStatement->bindValue(':id', $comment->getId(), PDO::PARAM_INT);
        $pdoStatement->bindValue(':content', $comment->getContent(), PDO::PARAM_STR);
        $pdoStatement->bindValue(':status', $comment->getStatus(), PDO::PARAM_INT);

        return $pdoStatement->execute();
    }


    /**
     * Method for deleting a comment
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
}
