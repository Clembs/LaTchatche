<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Channel;
use App\Models\Message;
use App\Models\MessageType;
use App\Models\User;

class ChatController extends Controller
{
  /**
   * Affiche la page d'accueil.
   */
  public static function home(): void
  {
    $channels = Channel::findAll();

    self::render('chat/home', 'Accueil', ['channels' => $channels]);
  }

  /**
   * Montre des infos pour le salon sélectionné. (WIP)
   */
  public static function channel(int $id): void
  {
    $channel = Channel::findById($id);

    if (!$channel) {
      self::notFound();
      return;
    }
    $channels = Channel::findAll();
    $messages = Message::findAllForChannel($channel->id);

    self::render('chat/channel', $channel->name, [
      'channel' => $channel,
      'channels' => $channels,
      'messages' => array_reverse($messages)
    ]);
  }

  public static function sendMessage(string $channelId, array $data): void
  {
    $channel = Channel::findById($channelId);

    if (!$channel) {
      self::notFound();
      return;
    }

    $authorId = 1;

    try {
      $message = Message::create(
        messageType: MessageType::default ,
        content: $data['content'],
        authorId: $authorId,
        channelId: $channelId,
        author: User::findById($authorId),
      );

      // On récupère notre composant de message et on le renvoie
      // plutôt que de renvoyer le message et de l'afficher via JS
      // pour éviter les injections HTML et attaques XSS
      include __DIR__ . '/../Views/chat/Message.php';
    } catch (\Exception $e) {
      self::json(['error' => 'argh'], 500);
    }
  }
}
