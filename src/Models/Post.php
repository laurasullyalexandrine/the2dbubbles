<?php 
namespace App\Models;

use App\Utils\Database;
use PDO;

class Post extends CoreModel {
    
    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $slug;

    /**
     * @var string
     */
    private $chapo;

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
    private $comments;

    /**
     * @var int
     */
    private $users;

    /**
     * Méthode permettant de récupérer tous les enregistrements de la table post
     *
     * @return Post
     */
    public static function findAll() 
    {
        $pdoDBConnexion = Database::getPDO();

        $sql = "
            SELECT post.id, title, chapo, post.content, post.slug, post.status, post.created_at, post.updated_at, user.id, user.pseudo AS user
            FROM post
            INNER JOIN user ON user.id = post.users
            WHERE post.users
            ORDER BY created_at ASC
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
    public static function findById(int $postId) 
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


    public static function findBySlug($slug)
    {
        $pdoDBConnexion = Database::getPDO();
        $sql = "
            SELECT post.id, post.title, post.chapo, post.content, post.slug, post.status, post.created_at, post.updated_at, comment.id AS comment, user.pseudo AS user
            FROM post
            LEFT JOIN comment ON comment.id = post.comments
            INNER JOIN user ON user.id = post.users
            WHERE slug = :slug
            "
        ;

        $pdoStatement = $pdoDBConnexion->prepare($sql);
        $pdoStatement->bindValue(':slug', $slug, PDO::PARAM_STR);
        $pdoStatement->execute();
        $post = $pdoStatement->fetchObject(self::class);

        return $post;
    }


    /**
     * Méthode permettant d'ajouter un enregistrement dans la table post.
     * L'objet courant doit contenir toutes les données à ajouter : 1 propriété => 1 colonne dans la table
     *
     * @return bool
     */
    public function insert(): bool
    {
        $pdoDBConnexion = Database::getPDO();
        $sql = "
            INSERT INTO `post` (users, title, chapo, content, status, slug)
            VALUES (:users, :title, :chapo, :content, :status, :slug)"
            ;

        $pdoStatement = $pdoDBConnexion->prepare($sql);
        $pdoStatement->execute([
            ':users' => $this->users,
            ':title' => $this->title,
            ':chapo' => $this->chapo,
            ':content' => $this->content,
            ':status' => 0,
            ':slug' => $this->slug
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
     * @return Post
     */
    public function update()
    {
        $pdoDBConnexion = Database::getPDO();

        $sql = "
            UPDATE `post`
            SET 
                users = :users,
                title = :title,
                chapo = :chapo,
                content = :content,
                status = :status,
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
        $pdoStatement->bindValue(':status', $this->status, PDO::PARAM_BOOL);
        $pdoStatement->bindValue(':slug', $this->slug, PDO::PARAM_STR);

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
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the value of title
     *
     * @return  self
     */ 
    public function setTitle($title)
    {
        $this->title = $title;

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
     * Get the value of chapo
     */ 
    public function getChapo()
    {
        return $this->chapo;
    }

    /**
     * Set the value of chapo
     *
     * @return  self
     */ 
    public function setChapo($chapo)
    {
        $this->chapo = $chapo;

        return $this;
    }

    /**
     * Get the value of content
     *
     * @return  string
     */ 
    public function getContent()
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
    public function setContent(string $content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get the value of status
     *
     * @return  bool
     */ 
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the value of status
     *
     * @param  bool  $status
     *
     * @return  self
     */ 
    public function setStatus(bool $status)
    {
        $this->status = $status;

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
}