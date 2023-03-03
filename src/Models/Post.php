<?php 
namespace App\Models;

use App\Utils\Database;
use PDO;

class Post extends CoreModel {
    
    private $title;
    private $chapo;
    private $comments;

    /**
     * Méthode permettant de récupérer tous les enregistrements de la table post
     *
     * @return Post
     */
    public function findAll() 
    {
        // Récupérer de l'objet PDO représentant la connexion à la DB
        $pdoDBConnexion = Database::getPDO();

        // Ecrire la requête sql
        $sql = 'SELECT * FROM post';

        // Préparer la requête
        $pdoStatement = $pdoDBConnexion->prepare($sql);

        // Exécuter la requête
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
    public function insert()
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

        // Si il y a au moins une ligne ajoutée alors...
        if ($pdoStatement->rowCount() > 0) {
            // Récupérer l'id auto-incrémenté généré par MySQL
            $this->id = $pdoDBConnexion->lastInsertId();

            // Retourner true si l'ajout est validé
            return true;
        } 

        return false;
    }
    
    public function update()
    {
            
    }

    public function delete()
    {
            
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