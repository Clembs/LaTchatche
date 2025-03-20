<?php
// Routeur basique

require '../vendor/autoload.php';
use App\Controllers\ChatController;

// On retire le premier et dernier slash puis on explose l'URI en parties
$cleanUri = trim(rtrim($_SERVER['REQUEST_URI'], '/'), '/');
$uriParts = explode('/', $cleanUri);

switch ($uriParts[0]) {
  case '':
  case 'chats': {
    $channelId = $uriParts[1];

    if (isset($channelId) && $channelId !== '') {
      ChatController::channel((int) $channelId);
    } else {
      ChatController::home();
    }
    break;
  }
  default:
    require_once '../app/Views/404.php';
}