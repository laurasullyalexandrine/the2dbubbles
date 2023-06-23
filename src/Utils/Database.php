<?php

namespace App\Utils;

use PDO;

class Database {

    /** @var PDO */
    protected $dbh;


    /**
     * Constructeur
     * en visibilité private
     * => seule le code de la classe a le droit de créer une instance de cette classe
     */
    public function __construct() {
        
        // Récupération des données du fichier de config
        // la fonction parse_ini_file parse le fichier et retourne un array associatif
        $configData = parse_ini_file(__DIR__. '/../config.ini') ;

        // PHP essaie d'exécuter tout le code à l'intérieur du bloc "try", mais...
        try {
            $this->dbh = new PDO(
                "mysql:host={$configData['DB_HOST']};dbname={$configData['DB_NAME']};charset=utf8",
                $configData['DB_USERNAME'],
                $configData['DB_PASSWORD'],
                array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION) // Affiche les erreurs SQL à l'écran
            );
        } 
        // ... mais si une erreur (Exception) survient, alors on attrapte l'exception et on exécute le code que l'on souhaite (ici, on affiche un message d'erreur)
        catch (\Exception $exception) {
            echo 'Erreur de connexion...<br>';
            echo $exception->getMessage() . '<br>';
            echo '<pre>';
            echo $exception->getTraceAsString();
            echo '</pre>';
            return;
        }
    }

    // 
    /**
     * Permet de créer une connexion si elle n'existe pas
     * TODO à retirer quand la migration est terminée
     * @return PDO
     */
    public static function getPDO() {

       $instance = new self();

       return $instance->dbh;

    }
}
