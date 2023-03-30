<?php

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
     * Méthode permettant de récupérer tous les enregistrements de la table post
     *
     * @return Post
     */
    public static function findAll()
    {
        $pdoDBConnexion = Database::getPDO();

        $sql = 'SELECT * FROM comment';

        $pdoStatement = $pdoDBConnexion->prepare($sql);
        $pdoStatement->execute();
        $comments = $pdoStatement->fetchAll(PDO::FETCH_CLASS, self::class);

        return $comments;
    }
    /**
     *  Méthode permettant de récupérer un enregistrement de la table Comment en fonction d'un id donné
     *
     * @param [type] $commentId
     * @return Comment
     */
    public static function findBy($commentId)
    {
        $pdoDBConnexion = Database::getPDO();
        $sql = '
        SELECT * 
        FROM comment 
        WHERE id = :id';
        $pdoStatement = $pdoDBConnexion->prepare($sql);
        $pdoStatement->execute([
            'id' => $commentId
        ]);
        $comment = $pdoStatement->fetchObject(self::class);

        return $comment;
    }

    /**
     * Méthode permettant d'ajouter un enregistrement dans la table comment.
     * L'objet courant doit contenir toutes les données à ajouter : 1 propriété => 1 colonne dans la table
     *
     * @return void
     */
    public function insert()
    {
        $pdoDBConnexion = Database::getPDO();
        $sql = "
                INSERT INTO `comment` (content, status, created_at)
                VALUES (:content, :status, :created_at)";

        $pdoStatement = $pdoDBConnexion->prepare($sql);
        $pdoStatement->execute([
            ':content' => $this->content,
            ':status' => 0,
            ':created_at' => $this->created_at
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
     * @return void
     */
    public function update()
    {
        $pdoDBConnexion = Database::getPDO();

        $sql = "
            UPDATE `comment`
            SET 
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
            DELETE FROM `comment`
            WHERE id = :id
        ";
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
}
