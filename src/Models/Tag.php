<?php 
namespace App\Models;

use PDO;
use App\Utils\Database;

class Tag extends CoreModel {
    
    /**
     * Méthode permettant de récupérer tous les enregistrements de la table tag
     *
     * @return Tag
     */
    public static function findAll() 
    {
        $pdoDBConnexion = Database::getPDO();

        // Ecrire la requête sql
        $sql = 'SELECT * FROM `tag`';

        $pdoStatement = $pdoDBConnexion->prepare($sql);
        $pdoStatement->execute();
        $tags = $pdoStatement->fetchAll(PDO::FETCH_CLASS, self::class);
        
        return $tags;
    }
    /**
     *  Méthode permettant de récupérer un enregistrement de la table tag en fonction d'un id donné
     *
     * @param [type] $tagId
     * @return Tag
     */
    public static function findBy($tagId) 
    {
        $pdoDBConnexion = Database::getPDO();
        $sql = '
        SELECT * 
        FROM tag 
        WHERE id = :id';
        $pdoStatement = $pdoDBConnexion->prepare($sql);
        $pdoStatement->execute([
            'id' => $tagId
        ]);
        $tag = $pdoStatement->fetchObject(self::class);
 
        return $tag;
    }

    /**
     * Méthode permettant d'ajouter un enregistrement dans la table tag.
     * L'objet courant doit contenir toutes les données à ajouter : 1 propriété => 1 colonne dans la table
     *
     * @return void
     */    
    public function insert()
    {
        $pdoDBConnexion = Database::getPDO();
        $sql = "
            INSERT INTO `tag` (name)
            VALUES (:name)"
            ;

        $pdoStatement = $pdoDBConnexion->prepare($sql);
        $pdoStatement->execute([
            ':name' => $this->name,
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