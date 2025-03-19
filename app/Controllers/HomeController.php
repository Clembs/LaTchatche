<?php

namespace App\Controllers;

use App\Core\Controller;

class HomeController extends Controller
{
  /**
   * Affiche la page d'accueil.
   */
  public static function home(): void
  {
    self::render('home', 'Accueil');
  }
}
