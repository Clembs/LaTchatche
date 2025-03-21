<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Channel;
use App\Models\Member;
use App\Models\Message;
use App\Models\MessageType;
use App\Models\Session;
use App\Models\User;

class ChatController extends Controller
{
  /**
   * Affiche la page d'accueil.
   */
  public static function home(): void
  {
    $currentUser = Session::getCurrentUser();

    if (!$currentUser) {
      header('Location: /login');
      return;
    }

    $channels = Channel::findAllForUser($currentUser->id);

    self::render('chat/home', 'Accueil', [
      'channel' => null,
      'channels' => $channels,
      'currentUser' => $currentUser
    ]);
  }

  /**
   * Montre des infos pour le salon sélectionné. (WIP)
   */
  public static function channel(int $id): void
  {
    $currentUser = Session::getCurrentUser();

    if (!$currentUser) {
      header('Location: /login');
      return;
    }

    $channel = Channel::findById($id);

    if (!$channel) {
      self::notFound();
      return;
    }

    $members = Member::findAllForChannel($channel->id);

    if (array_search($currentUser->id, array_column($members, 'userId')) === false) {
      header('Location: /404');
      return;
    }

    $channels = Channel::findAllForUser($currentUser->id);
    $messages = Message::findAllForChannel($channel->id, null);

    self::render('chat/channel', "#$channel->name", [
      'channel' => $channel,
      'channels' => $channels,
      'messages' => array_reverse($messages),
      'currentUser' => $currentUser,
    ]);
  }

  /**
   * Récupère les derniers messages pour un salon donné et les renvoie en un tableau de chaînes HTML.
   */
  public static function getLastMessages(string $channelId, ?string $lastMessageId): void
  {
    $currentUser = Session::getCurrentUser();

    if (!$currentUser) {
      header('Location: /login');
      exit;
    }

    $messages = Message::findAllForChannel($channelId, $lastMessageId ? (int) $lastMessageId : null);
    $messages = array_reverse($messages);

    $messageHtmls = [];

    foreach ($messages as $message) {
      ob_start();

      include __DIR__ . '/../Views/chat/Message.php';

      array_push($messageHtmls, ob_get_clean());
    }

    self::json($messageHtmls);
  }

  public static function sendMessage(string $channelId, array $data): void
  {
    $currentUser = Session::getCurrentUser();

    if (!$currentUser) {
      header('Location: /login');
      exit;
    }

    $authorId = $currentUser->id;

    $channel = Channel::findById($channelId);

    if (!$channel) {
      self::notFound();
      return;
    }

    $members = Member::findAllForChannel($channel->id);

    if (array_search($currentUser->id, array_column($members, 'userId')) === false) {
      self::json(['error' => 'Vous n\'êtes pas dans ce salon !'], 500);
      return;
    }

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
