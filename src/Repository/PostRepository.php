<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;
use App\Entity\Post;
use App\Utils\Database;

class PostRepository extends Database
{
    /**
     * Method to retrieve all records from table post
     *
     * @return array
     */
    public function findAll(): array
    {
        $sql = "
            SELECT p.id, title, chapo, p.content, p.slug, p.created_at, p.updated_at, u.id, u.pseudo AS user
            FROM post p
            INNER JOIN user u
            ON u.id = p.userId
            WHERE p.userId
            ORDER BY created_at DESC
            ";

        $pdoStatement = $this->dbh->prepare($sql);
        $pdoStatement->execute();
        $posts = $pdoStatement->fetchAll(PDO::FETCH_CLASS, Post::class);

        return $posts;
    }

    /**
     *  Method to retrieve a record from the Post table based on a given id
     *
     * @param [type] $postId
     * @return ?Post
     */
    public function findById(int $postId): ?Post
    {
        $sql = "
            SELECT * 
            FROM post 
            WHERE id = :id
            "
            ;
        $pdoStatement = $this->dbh->prepare($sql);
        $pdoStatement->bindValue(':id', $postId, PDO::PARAM_INT);
        $pdoStatement->execute();

        $post = $pdoStatement->fetchObject(Post::class);

        return $post instanceof Post ? $post : null;
    }

    /**
     * Method to retrieve a record from the Post table given a given slug
     *
     * @param string $slug
     * @return ?Post
     */
    public function findBySlug(string $slug): ?Post
    {
        $sql = "
            SELECT p.id, p.title, p.chapo, p.content, p.slug, p.created_at, p.updated_at, u.pseudo AS user
            FROM post p
            LEFT JOIN user u
            ON u.id = p.userId
            WHERE p.slug = :slug
            ";

        $pdoStatement = $this->dbh->prepare($sql);
        $pdoStatement->bindValue(':slug', $slug, PDO::PARAM_STR);
        $pdoStatement->execute();
        $post = $pdoStatement->fetchObject(Post::class);

        return $post instanceof Post ? $post : null;
    }


    /**
     * Method to add a record in the Post table
     *
     * @return bool
     */
    public function insert(Post $post): bool
    {
        $sql = "
            INSERT INTO post (
                userId, 
                title, 
                chapo, 
                content, 
                slug
            )
            VALUES (
                :userId, 
                :title, 
                :chapo, 
                :content, 
                :slug
            )
            ";

        $pdoStatement = $this->dbh->prepare($sql);
        $pdoStatement->execute([
            'userId' => $post->getUserId(),
            'title' => $post->getTitle(),
            'chapo' => $post->getChapo(),
            'content' => $post->getContent(),
            'slug' => $post->getSlug()
        ]);

        // Returns the number of rows affected by the last call to PDOStatement::execute()
        // If there is at least one line added then...
        if ($pdoStatement->rowCount() > 0) {
            return true;
        }
        return false;
    }

    /**
     * Method for editing an article
     *
     * @return bool
     */
    public function update(Post $post): bool
    {
        $sql = "
            UPDATE `post`
            SET 
                userId = :userId,
                title = :title,
                chapo = :chapo,
                content = :content,
                slug = :slug,
                updated_at = NOW()
            WHERE id = :id
        ";

        $pdoStatement = $this->dbh->prepare($sql);
        $pdoStatement->bindValue(':id', $post->getId(), PDO::PARAM_INT);
        $pdoStatement->bindValue(':userId', $post->getUserId(), PDO::PARAM_INT);
        $pdoStatement->bindValue(':title', $post->getTitle(), PDO::PARAM_STR);
        $pdoStatement->bindValue(':chapo', $post->getChapo(), PDO::PARAM_STR);
        $pdoStatement->bindValue(':content', $post->getContent(), PDO::PARAM_STR);
        $pdoStatement->bindValue(':slug', $post->getSlug(), PDO::PARAM_STR);

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
            DELETE FROM `post`
            WHERE id = :id
        ";
        $pdoStatement = $this->dbh->prepare($sql);
        $pdoStatement->bindValue(':id', $id, PDO::PARAM_INT);
        $pdoStatement->execute();

        return ($pdoStatement->rowCount() > 0);
    }
}
