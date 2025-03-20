<?php

namespace App\Core;

use \PDO;

class Database
{
  private static ?Database $instance = null;
  public PDO $connection;

  public function __construct()
  {
    try {
      $databaseIni = parse_ini_file(__DIR__ . '/../../config.ini', true)['database'];
    } catch (\Exception $e) {
      die("Erreur lors de la lecture du config.ini : " . $e->getMessage());
    }

    try {
      $driver = $databaseIni['driver'];
      $host = $databaseIni['host'];
      $port = $databaseIni['port'];
      $database = $databaseIni['database'];
      $username = $databaseIni['username'];
      $password = $databaseIni['password'];

      $dsn = "$driver:host=$host;port=$port;dbname=$database";

      $options = [
        PDO::ATTR_PERSISTENT => true,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_FOUND_ROWS => true
      ];

      $this->connection = new PDO($dsn, $username, $password, $options);

    } catch (\PDOException $e) {
      die("Erreur lors de la création de la connexion à la base de données : " . $e->getMessage() . json_encode($databaseIni));
    }
  }

  // singleton pour éviter de créer plusieurs connexions à la base de données
  public static function getPDO(): PDO
  {
    if (self::$instance === null) {
      self::$instance = new Database();
    }

    return self::$instance->connection;
  }
}