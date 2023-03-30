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
     * Méthode permettant de récupérer tous les enregistrements de la table post
     *
     * @return Post
     */
    public static function findAll() 
    {
        $pdoDBConnexion = Database::getPDO();

        $sql = 'SELECT * FROM post';
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
    public static function findBy($postId) 
    {
        $pdoDBConnexion = Database::getPDO();
        $sql = '
        SELECT * 
        FROM post 
        WHERE id = :id';
        $pdoStatement = $pdoDBConnexion->prepare($sql);
        $pdoStatement->execute([
            'id' => $postId
        ]);
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
            INSERT INTO `post` (title, chapo, content, status, created_at)
            VALUES (:title, :chapo, :content, :status, :created_at)"
            ;

        $pdoStatement = $pdoDBConnexion->prepare($sql);
        $pdoStatement->execute([
            ':title' => $this->title,
            ':chapo' => $this->chapo,
            ':content' => $this->content,
            ':status' => 0,
            ':created_at' => $this->created_at
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
     * Méthode permettant l'édition d'un commentaire
     *
     * @return void
     */
    public function update()
    {
        $pdoDBConnexion = Database::getPDO();

        $sql = "
            UPDATE `comment`
            SET 
                title = :title,
                chapo = :chapo
                content = :content,
                status = :status,
                updated_at = NOW()
        ";

        $pdoStatement = $pdoDBConnexion->prepare($sql);
        $pdoStatement->execute([
            ':content' => $this->content,
            ':status' => $this->status,
        ]);

        return $pdoStatement;
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
}