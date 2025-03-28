<?php
require '../vendor/autoload.php';

use App\Controllers\AuthController;
use App\Controllers\ChannelController;
use App\Controllers\ChatController;
use Bramus\Router\Router;

$router = new Router();

$router->get('/', function () {
  ChatController::homePage();
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
  ChannelController::publicChannelsPage();
});

$router->post('/channels', function () {
  ChannelController::create($_POST);
});

$router->get('/channels/(\d+)', function (int $channelId) {
  ChannelController::channelPage($channelId);
});

$router->post('/channels/(\d+)/messages', function (int $channelId) {
  ChatController::create($channelId, $_POST);
});

$router->get('/channels/(\d+)/messages', function (int $channelId) {
  $lastMessageId = $_GET['lastMessageId'] ?? null;
  $jsonMode = isset($_GET['json']) && $_GET['json'] === 'true';

  ChannelController::getMessages(
    $channelId,
    $lastMessageId,
    $jsonMode
  );
});

$router->get('/channels/(\d+)/invite', function (int $channelId) {
  ChannelController::invite($channelId);
});

$router->get('/channels/(\d+)/join', function (int $channelId) {
  ChannelController::join($channelId);
});

$router->get('/join/(\w+)', function (string $inviteToken) {
  ChannelController::joinWithToken($inviteToken);
});

$router->set404(function () {
  require_once '../app/Views/404.php';
});

$router->run();