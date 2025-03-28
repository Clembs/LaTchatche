<?php
require '../vendor/autoload.php';

use App\Controllers\AuthController;
use App\Controllers\ChannelController;
use App\Controllers\ChatController;
use Bramus\Router\Router;

$router = new Router();

$router->get('/', function () {
  ChatController::home();
});

$router->get('/chats/(\d+)', function (int $channelId) {
  ChatController::channel($channelId);
});

$router->post('/chats/(\d+)/send-message', function (int $channelId) {
  ChatController::sendMessage($channelId, $_POST);
});

$router->get('/chats/(\d+)/messages', function (int $channelId) {
  $json = isset($_GET['json']) && $_GET['json'] === 'true';

  ChatController::getLastMessages(
    $channelId,
    $_GET['lastMessageId'] ?? null,
    $json
  );
});

$router->get('/login', function () {
  AuthController::loginPage($_GET['error'] ?? null);
});

$router->post('/login', function () {
  AuthController::login($_POST);
});

$router->get('/register', function () {
  AuthController::registerPage($_GET['error'] ?? null);
});

$router->post('/register', function () {
  AuthController::register($_POST);
});

$router->get('/channels', function () {
  ChannelController::publicChannels();
});

$router->post('/channels/create', function () {
  ChannelController::createChannel($_POST);
});

$router->get('/channels/(\d+)/invite', function (int $channelId) {
  ChannelController::invite($channelId);
});

$router->get('/channels/(\d+)/join', function (int $channelId) {
  ChannelController::joinChannelById($channelId);
});

$router->get('/join/(\w+)', function (string $inviteToken) {
  ChannelController::joinChannelByToken($inviteToken);
});

$router->set404(function () {
  require_once '../app/Views/404.php';
});

$router->run();