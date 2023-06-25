<?php

namespace App\Utils;

use PDO;

class Database {

    /** @var PDO */
    protected $dbh;


    /**
     * Builder
     * n private visibility
     * => only the code of the class has the right to create an instance of this class
     */
    public function __construct() {
        
        // Retrieving data from config file
        // the parse_ini_file function parses the file and returns an associative array
        $configData = parse_ini_file(__DIR__. '/../config.ini') ;

        // PHP tries to execute all the code inside the "try" block, but...
        try {
            $this->dbh = new PDO(
                "mysql:host={$configData['DB_HOST']};dbname={$configData['DB_NAME']};charset=utf8",
                $configData['DB_USERNAME'],
                $configData['DB_PASSWORD'],
                array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION) // Affiche les erreurs SQL à l'écran
            );
        } 
        // ... but if an error (Exception) occurs, then we catch the exception and we execute the code we want (here, we display an error message)
        catch (\Exception $exception) {
            echo 'Erreur de connexion...<br>';
            echo $exception->getMessage() . '<br>';
            echo '<pre>';
            echo $exception->getTraceAsString();
            echo '</pre>';
            return;
        }
    }
}
