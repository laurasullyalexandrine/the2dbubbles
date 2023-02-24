<?php

namespace App\Utils;

use PDO;

class Database
{
    private $databaseConnexion;
    private static $_instance;

    public function __construct()
    {
        $configData = parse_ini_file(dirname(__DIR__) . '/../config.ini');

        // Permet de créer une instance de l'objet PDO et de gérer les erreurs lors de la connexion à la base de données
        try {
            $this->databaseConnexion = new PDO(
                "mysql:host={$configData['DB_HOST']};dbname={$configData['DB_NAME']};charset=utf8",
                $configData['DB_USERNAME'],
                $configData['DB_PASSWORD'],
                array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING) // Affiche les erreurs SQL à l'écran
            );
        } catch (\Exception $exception) {
            echo 'Erreur de connexion...<br>';
            echo $exception->getMessage() . '<br>';
            echo '<pre>';
            echo $exception->getTraceAsString();
            echo '</pre>';
            exit;
        }
    }

    /**
     * Permet de créer une connexion si elle n'existe pas
     *
     * @return void
     */
    public static function getPDO()
    {
        if (empty(self::$_instance)) {
            self::$_instance = new Database();
        }
    }
}