<?php 

declare(strict_types=1);

namespace App\Models;

use App\Utils\Database;
use PDO;

class Post extends CoreModel {
    
    /**
     * @var string
     */
    private string $title;

    /**
     * @var string
     */
    private string $slug;

    /**
     * @var string
     */
    private string $chapo;

    /**
     * @var string
     */
    private string $content;

    /**
     * @var int
     */
    private int $users;

    
    /**
     * Méthode permettant de récupérer tous les enregistrements de la table post
     *
     * @return array
     */
    public static function findAll(): array
    {
        $pdoDBConnexion = Database::getPDO();

        $sql = "
            SELECT p.id, title, chapo, p.content, p.slug, p.created_at, p.updated_at, u.id, u.pseudo AS user
            FROM post p
            INNER JOIN user u
            ON u.id = p.users
            WHERE p.users
            ORDER BY created_at DESC
            "
        ;
        
        $pdoStatement = $pdoDBConnexion->prepare($sql);
        $pdoStatement->execute();
        $posts = $pdoStatement->fetchAll(PDO::FETCH_CLASS, self::class);

        return $posts;
    }
    
    /**
     *  Méthode permettant de récupérer un enregistrement de la table Post en fonction d'un id donné
     *
     * @param [type] $postId
     * @return Post
     */
    public static function findById(int $postId): Post
    {
        $pdoDBConnexion = Database::getPDO();
        $sql = "
            SELECT * 
            FROM post 
            WHERE id = :id
            "
        ;
        $pdoStatement = $pdoDBConnexion->prepare($sql);
        $pdoStatement->bindValue(':id', $postId, PDO::PARAM_INT);
        $pdoStatement->execute();

        $post = $pdoStatement->fetchObject(self::class);

        return $post;
    }

    /**
     * Undocumented function
     *
     * @param string $slug
     * @return self
     */
    public static function findBySlug(string $slug): self
    {
        $pdoDBConnexion = Database::getPDO();
        $sql = "
            SELECT p.id, p.title, p.chapo, p.content, p.slug, p.created_at, p.updated_at, u.pseudo AS user
            FROM post p
            LEFT JOIN user u
            ON u.id = p.users
            WHERE p.slug = :slug
            "
        ;

        $pdoStatement = $pdoDBConnexion->prepare($sql);
        $pdoStatement->bindValue(':slug', $slug, PDO::PARAM_STR);
        $pdoStatement->execute();
        $post = $pdoStatement->fetchObject(self::class);

        return $post;
    }


    /**
     * Méthode permettant d'ajouter un enregistrement dans la table Post.
     * L'objet courant doit contenir toutes les données à ajouter : 1 propriété => 1 colonne dans la table
     *
     * @return bool
     */
    public function insert(): bool
    {
        $pdoDBConnexion = Database::getPDO();
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

        $pdoStatement = $pdoDBConnexion->prepare($sql);
        $pdoStatement->execute([
            'users' => $this->users,
            'title' => $this->title,
            'chapo' => $this->chapo,
            'content' => $this->content,
            'slug' => $this->slug
        ]);

        // Retourne le nombre de lignes affectées par le dernier appel à la fonction PDOStatement::execute()
        // Si il y a au moins une ligne ajoutée alors...
        if ($pdoStatement->rowCount() > 0) {
            $this->id = $pdoDBConnexion->lastInsertId();

            return true;
        } 
        return false;
    }
    
    /**
     * Méthode permettant l'édition d'un article
     *
     * @return bool
     */
    public function update(): bool
    {
        $pdoDBConnexion = Database::getPDO();

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

        $pdoStatement = $pdoDBConnexion->prepare($sql);
        $pdoStatement->bindValue(':id', $this->id, PDO::PARAM_INT);
        $pdoStatement->bindValue(':users', $this->users, PDO::PARAM_INT);
        $pdoStatement->bindValue(':title', $this->title, PDO::PARAM_STR);
        $pdoStatement->bindValue(':chapo', $this->chapo, PDO::PARAM_STR);
        $pdoStatement->bindValue(':content', $this->content, PDO::PARAM_STR);
        $pdoStatement->bindValue(':slug', $this->slug, PDO::PARAM_STR);

        return $pdoStatement->execute();
    }


    /**
     * Méthode permettant la supression d'un commentaire
     *
     * @return bool
     */
    public function delete(): bool
    {
        $pdoDBConnexion = Database::getPDO();

        $sql = "
            DELETE FROM `post`
            WHERE id = :id
        ";
        $pdoStatement = $pdoDBConnexion->prepare($sql);
        $pdoStatement->bindValue(':id', $this->id, PDO::PARAM_INT);
        $pdoStatement->execute();

        return ($pdoStatement->rowCount() > 0);
    }

     

    /**
     * Get the value of title
     */ 
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Set the value of title
     *
     * @return  self
     */ 
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the value of slug
     *
     * @return  string
     */ 
    public function getSlug(): string
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
    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get the value of chapo
     */ 
    public function getChapo(): string
    {
        return $this->chapo;
    }

    /**
     * Set the value of chapo
     *
     * @return  self
     */ 
    public function setChapo(string $chapo): self
    {
        $this->chapo = $chapo;

        return $this;
    }

    /**
     * Get the value of content
     *
     * @return  string
     */ 
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Set the value of content
     *
     * @param  string  $content
     *
     * @return  self
     */ 
    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get the value of users
     *
     * @return  int
     */ 
    public function getUsers(): int
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
    public function setUsers(int $users): self
    {
        $this->users = $users;

        return $this;
    }
}
