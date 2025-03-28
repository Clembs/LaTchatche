<?php
// Routeur pour l'API
require '../../vendor/autoload.php';

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

$router->set404(function () {
  ApiController::notFound();
});

$router->run();