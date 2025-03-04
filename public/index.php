<?php
// Routeur basique
// Les liens se font vers /?page=nom-de-la-page
$_GET['page'] ??= 'accueil';

require_once '../src/controllers/accueil.php';

switch ($_GET['page']) {
  case 'accueil':
    ControleurAccueil::accueil();
    break;
  default:
    require_once '../src/views/404.php';
}