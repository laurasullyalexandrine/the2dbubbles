<?php 

declare(strict_types=1);

namespace App\Repository;

use PDO;
use App\Entity\Post;
use App\Utils\Database;

class PostRepository extends Database {
    
    // /**
    //  * @var string
    //  */
    // private ?string $title = null;

    // /**
    //  * @var string
    //  */
    // private ?string $slug = null;

    // /**
    //  * @var string
    //  */
    // private ?string $chapo = null;

    // /**
    //  * @var string
    //  */
    // private ?string $content = null;

    // /**
    //  * @var int
    //  */
    // private int $users;

    
    /**
     * Méthode permettant de récupérer tous les enregistrements de la table post
     *
     * @return array
     */
    public function findAll(): array
    {
        $sql = "
            SELECT p.id, title, chapo, p.content, p.slug, p.created_at, p.updated_at, u.id, u.pseudo AS user
            FROM post p
            INNER JOIN user u
            ON u.id = p.users
            WHERE p.users
            ORDER BY created_at DESC
            "
        ;
        
        $pdoStatement = $this->dbh->prepare($sql);
        $pdoStatement->execute();
        $posts = $pdoStatement->fetchAll(PDO::FETCH_CLASS, Post::class);

        return $posts;
    }
    
    /**
     *  Méthode permettant de récupérer un enregistrement de la table Post en fonction d'un id donné
     *
     * @param [type] $postId
     * @return Post
     */
    public function findById(int $postId): Post
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

        return $post;
    }

    /**
     * Undocumented function
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
            ON u.id = p.users
            WHERE p.slug = :slug
            "
        ;

        $pdoStatement = $this->dbh->prepare($sql);
        $pdoStatement->bindValue(':slug', $slug, PDO::PARAM_STR);
        $pdoStatement->execute();
        $post = $pdoStatement->fetchObject(Post::class);

        return $post;
    }


    /**
     * Méthode permettant d'ajouter un enregistrement dans la table Post.
     * L'objet courant doit contenir toutes les données à ajouter : 1 propriété => 1 colonne dans la table
     *
     * @return bool
     */
    public function insert(Post $post): bool
    {
        $sql = "
            INSERT INTO post (
                users, 
                title, 
                chapo, 
                content, 
                slug
            )
            VALUES (
                :users, 
                :title, 
                :chapo, 
                :content, 
                :slug
            )
            "
        ;

        $pdoStatement = $this->dbh->prepare($sql);
        $pdoStatement->execute([
            'users' => $post->getUsers(),
            'title' => $post->getTitle(),
            'chapo' => $post->getChapo(),
            'content' => $post->getContent(),
            'slug' => $post->getSlug()
        ]);

        // Retourne le nombre de lignes affectées par le dernier appel à la fonction PDOStatement::execute()
        // Si il y a au moins une ligne ajoutée alors...
        if ($pdoStatement->rowCount() > 0) {
            return true;
        } 
        return false;
    }
    
    /**
     * Méthode permettant l'édition d'un article
     *
     * @return bool
     */
    public function update(Post $post): bool
    {
        $sql = "
            UPDATE `post`
            SET 
                users = :users,
                title = :title,
                chapo = :chapo,
                content = :content,
                slug = :slug,
                updated_at = NOW()
            WHERE id = :id
        ";

        $pdoStatement = $this->dbh->prepare($sql);
        $pdoStatement->bindValue(':id', $post->getId(), PDO::PARAM_INT);
        $pdoStatement->bindValue(':users', $post->getUsers(), PDO::PARAM_INT);
        $pdoStatement->bindValue(':title', $post->getTitle(), PDO::PARAM_STR);
        $pdoStatement->bindValue(':chapo', $post->getChapo(), PDO::PARAM_STR);
        $pdoStatement->bindValue(':content', $post->getContent(), PDO::PARAM_STR);
        $pdoStatement->bindValue(':slug', $post->getSlug(), PDO::PARAM_STR);

        return $pdoStatement->execute();
    }


    /**
     * Méthode permettant la supression d'un commentaire
     *
     * @return bool
     */
    public function delete(string $slug): bool
    {
        $pdoDBConnexion = Database::getPDO();

        $sql = "
            DELETE FROM `post`
            WHERE slug = :slug
        ";
        $pdoStatement = $pdoDBConnexion->prepare($sql);
        $pdoStatement->bindValue(':slug', $slug, PDO::PARAM_INT);
        $pdoStatement->execute();

        return ($pdoStatement->rowCount() > 0);
    }

     

    // /**
    //  * Get the value of title
    //  */ 
    // public function getTitle(): ?string
    // {
    //     return $this->title;
    // }

    // /**
    //  * Set the value of title
    //  *
    //  * @return  self
    //  */ 
    // public function setTitle(string $title): self
    // {
    //     $this->title = $title;

    //     return $this;
    // }

    // /**
    //  * Get the value of slug
    //  *
    //  * @return  string
    //  */ 
    // public function getSlug(): ?string
    // {
    //     return $this->slug;
    // }

    // /**
    //  * Set the value of slug
    //  *
    //  * @param  string  $slug
    //  *
    //  * @return  self
    //  */ 
    // public function setSlug(string $slug): self
    // {
    //     $this->slug = $slug;

    //     return $this;
    // }

    // /**
    //  * Get the value of chapo
    //  */ 
    // public function getChapo(): ?string
    // {
    //     return $this->chapo;
    // }

    // /**
    //  * Set the value of chapo
    //  *
    //  * @return  self
    //  */ 
    // public function setChapo(string $chapo): self
    // {
    //     $this->chapo = $chapo;

    //     return $this;
    // }

    // /**
    //  * Get the value of content
    //  *
    //  * @return  string
    //  */ 
    // public function getContent(): ?string
    // {
    //     return $this->content;
    // }

    // /**
    //  * Set the value of content
    //  *
    //  * @param  string  $content
    //  *
    //  * @return  self
    //  */ 
    // public function setContent(string $content): self
    // {
    //     $this->content = $content;

    //     return $this;
    // }

    // /**
    //  * Get the value of users
    //  *
    //  * @return  int
    //  */ 
    // public function getUsers(): int
    // {
    //     return $this->users;
    // }

    // /**
    //  * Set the value of users
    //  *
    //  * @param  int  $users
    //  *
    //  * @return  self
    //  */ 
    // public function setUsers(int $users): self
    // {
    //     $this->users = $users;

    //     return $this;
    // }
}
