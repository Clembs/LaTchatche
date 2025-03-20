<?php

namespace App\Core;

/**
 * Classe Controller responsable du rendu des vues et de la gestion du modele
 * 
 * @package App\Core
 */
abstract class Controller
{
  /**
   * Rend une vue PHP avec les données fournies
   * 
   * @param string $view Le chemin de la vue à partir du dossier Views (sans l'extension .php)
   * @param string $title Le titre de la page (affiché dans l'onglet du navigateur)
   * @param array $data Un tableau associatif de données à extraire et rendre disponible dans la vue
   */
  public static function render(string $view, string $title, $data = [])
  {
    extract($data);

    // On requiert le contenu de la vue demandée
    $viewPath = dirname(__DIR__) . "/Views/$view.php";
    require $viewPath;

    // On découpe le chemin de la vue en parties
    $viewParts = explode('/', $view);

    // On regarde récursivement s'il y a un layout dans les dossiers où se trouve la vue
    // et on le requiert si on le trouve
    for ($i = count($viewParts) - 2; $i >= 0; $i--) {
      $currentPath = implode('/', array_slice($viewParts, 0, $i + 1));

      $layoutPath = dirname(__DIR__) . "/Views/{$currentPath}/layout.php";

      if (file_exists($layoutPath)) {
        require $layoutPath;
      }
    }

    // On requiert le layout principal pour afficher la page
    require dirname(__DIR__) . '/Views/layout.php';
  }

  /**
   * Envoie une réponse JSON au client
   * 
   * @param array $data Les données à encoder en JSON
   */
  public static function json($data, $code = 200)
  {
    http_response_code($code);
    header('Content-Type: application/json;');
    echo json_encode($data);
  }

  /**
   * Shows a 404 error page
   */
  public static function notFound()
  {
    http_response_code(404);
    require_once dirname(__DIR__) . '/Views/404.php';
  }
}
