<?php

require '../vendor/autoload.php';
use App\Controllers\HomeController;

// Routeur basique
// Les liens se font vers /?page=nom-de-la-page
$_GET['page'] ??= 'accueil';

switch ($_GET['page']) {
  case 'accueil':
    HomeController::home();
    break;
  default:
    require_once '../app/Views/404.php';
}