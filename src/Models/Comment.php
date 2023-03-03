<?php

namespace App\Models;

use PDO;
use App\Utils\Database;

class Comment extends CoreModel
{

    /**
     * Méthode permettant de récupérer tous les enregistrements de la table post
     *
     * @return Post
     */
    public function findAll()
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

    public function update()
    {
    }

    public function delete()
    {
    }
}
