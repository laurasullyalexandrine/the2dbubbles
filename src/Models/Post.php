<?php 
namespace App\Models;

use App\Utils\Database;
use PDO;

class Post extends CoreModel {
    
    private $title;
    private $chapo;
    private $id_comment;

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

        
        public function insert()
        {
            $pdoDBConnexion = Database::getPDO();
            $sql = "
                INSERT INTO `post` ()";
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
     * Get the value of id_comment
     */ 
    public function getId_comment()
    {
        return $this->id_comment;
    }

    /**
     * Set the value of id_comment
     *
     * @return  self
     */ 
    public function setId_comment($id_comment)
    {
        $this->id_comment = $id_comment;

        return $this;
    }
}