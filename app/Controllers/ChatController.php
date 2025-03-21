<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Channel;
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

    $channels = Channel::findAll();

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

    $channels = Channel::findAll();
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

  public static function createChannel(array $data): void
  {
    $currentUser = Session::getCurrentUser();
    $currentUrl = strtok($_SERVER['HTTP_REFERER'], '?');

    if (!$currentUser) {
      header('Location: /login');
      exit;
    }

    if (!isset($data['name']) || empty($data['name'])) {
      header("Location: $currentUrl?error=Veuillez renseigner un nom pour le salon.");
      return;
    }

    if (strlen($data['name']) > 30) {
      header("Location: $currentUrl?error=Le nom du salon ne peut pas dépasser 30 caractères.");
      return;
    }

    // le nom normalisé, càd sans caractères spéciaux et avec des tirets
    $normalizedName = strtolower($data['name']);
    // on retire les caractères spéciaux (en ne comptant pas les accents)
    $normalizedName = preg_replace('/[^a-z0-9éèàêëôöîïùûüç\-_\s]/', '', $normalizedName);
    // on remplace les espaces par des tirets
    $normalizedName = preg_replace('/\s/', '-', $normalizedName);
    // on retire les tirets en fin de chaîne
    $normalizedName = rtrim($normalizedName, '-');

    $channel = Channel::create(
      name: $normalizedName,
      public: true,
      ownerId: $currentUser->id
    );

    if (!$channel) {
      header("Location: $currentUrl?error=Une erreur est survenue lors de la création du salon.");
      return;
    }

    header("Location: /chats/{$channel->id}");
  }
}
