<?php
// Routeur basique

require '../vendor/autoload.php';
use App\Controllers\AuthController;
use App\Controllers\ChatController;

// On retire le premier et dernier slash puis on explose l'URI en parties
$cleanUri = trim(rtrim($_SERVER['REQUEST_URI'], '/'), '/');
// On retire les paramètres de requête, les ancres, etc
$cleanUri = explode('?', $cleanUri)[0];
$cleanUri = explode('#', $cleanUri)[0];
$uriParts = explode('/', $cleanUri);

switch ($uriParts[0]) {
  case '':
  case 'chats': {
    $channelId = $uriParts[1];

    if (isset($channelId) && $channelId !== '') {
      if (isset($uriParts[2]) && $uriParts[2] === 'send-message' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        ChatController::sendMessage((int) $channelId, $_POST);
        break;
      }

      if (isset($uriParts[2]) && $uriParts[2] === 'messages' && $_SERVER['REQUEST_METHOD'] === 'GET') {
        ChatController::getLastMessages($channelId, $_GET['lastMessageId'] ?? null);
        break;
      }

      ChatController::channel((int) $channelId);
    } else {
      ChatController::home();
    }
    break;
  }
  case 'login':
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      AuthController::login($_POST);
    } else {
      AuthController::loginPage($_REQUEST['error'] ?? null);
    }
    break;
  case 'register':
    AuthController::registerPage();
    break;
  default:
    require_once '../app/Views/404.php';
}