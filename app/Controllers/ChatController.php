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
  public static function homePage(): void
  {
    $currentUser = Session::getCurrentUser();

    if (!$currentUser) {
      header('Location: /login');
      return;
    }

    header('Location: /channels');
  }

  /**
   * Crée un message dans un salon.
   */
  public static function create(string $channelId, array $data): void
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
        type: MessageType::default ,
        content: $data['content'],
        channelId: $channelId,
        author: User::findById($authorId),
      );

      // On récupère notre composant de message et on le renvoie
      // plutôt que de renvoyer le message et de l'afficher via JS
      // pour éviter les injections HTML et attaques XSS
      include __DIR__ . '/../Views/components/Message.php';
    } catch (\Exception $e) {
      self::json(['error' => 'argh'], 500);
    }
  }
}
