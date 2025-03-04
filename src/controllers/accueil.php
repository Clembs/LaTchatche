<?php
class ControleurAccueil
{
  /**
   * Affiche la page d'accueil.
   */
  public static function accueil(): void
  {
    // On requiert la page de connexion
    require __DIR__ . '/../views/accueil.php';

    // On définit le titre de la page (pour le layout)
    $title = 'Accueil';

    // On requiert le layout pour afficher la page
    require __DIR__ . '/../views/layout.php';
  }
}
