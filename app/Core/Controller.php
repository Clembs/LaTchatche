<?php

namespace App\Core;

/**
 * Classe Controller responsable du rendu des vues et de la gestion du modele
 * 
 * @package App\Core
 */
class Controller
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
    for ($i = count($viewParts) - 1; $i > 0; $i--) {
      $layoutPath = dirname(__DIR__) . "/Views/{$viewParts[$i]}/layout.php";

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
}
