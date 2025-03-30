<?php
// Routeur pour l'API
require '../../vendor/autoload.php';

use App\Controllers\Api\AuthApiController;
use App\Controllers\Api\ChannelApiController;
use App\Core\ApiController;
use Bramus\Router\Router;

$router = new Router();

$router->get('/channels', function () {
  ChannelApiController::publicChannels();
});

$router->get('/channels/(\d+)', function (int $id) {
  ChannelApiController::channel($id);
});

$router->get('/channels/(\d+)/messages', function (int $id) {
  $lastMessageId = $_GET['lastMessageId'] ?? null;

  ChannelApiController::getMessages($id, $lastMessageId);
});

$router->post('/login', function () {
  $data = json_decode(file_get_contents('php://input'), true);

  if (json_last_error() !== JSON_ERROR_NONE) {
    ApiController::error('Bad Request', 'Invalid JSON', 400);
  }

  AuthApiController::login($data);
});

$router->post('/logout', function () {
  AuthApiController::logout();
});

$router->get('/me', function () {
  AuthApiController::me();
});

$router->set404(function () {
  ApiController::notFound();
});

$router->run();