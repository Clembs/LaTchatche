<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Channel;
use App\Models\ChannelType;
use App\Models\Invite;
use App\Models\Member;
use App\Models\Message;
use App\Models\Session;

class ChannelController extends Controller
{
  /**
   * Affiche la page des salons publics.
   */
  public static function publicChannelsPage(): void
  {
    $currentUser = Session::getCurrentUser();

    if (!$currentUser) {
      header('Location: /login');
      exit;
    }

    $channels = Channel::findAllPublic();
    $userChannels = Channel::findAllForUser($currentUser->id);

    self::render('app/channels/public-channels', 'Salons publics', [
      'channels' => $channels,
      'userChannels' => $userChannels,
      'currentUser' => $currentUser
    ]);
  }

  /**
   * Affiche la page d'un salon sélectionné, avec les derniers messages et de l'info sur le salon.
   */
  public static function channelPage(int $id): void
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


    if (
      $channel->type !== ChannelType::public &&
      array_search($currentUser->id, array_column($members, 'userId')) === false
    ) {
      header('Location: /404');
      return;
    }

    $userChannels = Channel::findAllForUser($currentUser->id);
    $messages = Message::findAllForChannel($channel->id, null);

    self::render('app/channels/channel', "#$channel->name", [
      'channel' => $channel,
      'userChannels' => $userChannels,
      'messages' => array_reverse($messages),
      'currentUser' => $currentUser,
    ]);
  }

  /**
   * Crée un salon, fait rejoindre l'utilisateur et redirige vers le salon.
   */
  public static function create(array $data): void
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
      type: ChannelType::public ,
      ownerId: $currentUser->id
    );

    if (!$channel) {
      header("Location: $currentUrl?error=Une erreur est survenue lors de la création du salon.");
      return;
    }

    // on rejoint son propre salon
    Member::create(
      userId: $currentUser->id,
      channelId: $channel->id
    );

    header("Location: /channels/{$channel->id}");
  }

  /**
   * Crée ou récupère une invitation pour un salon.
   */
  public static function invite(int $channelId): void
  {
    $currentUser = Session::getCurrentUser();

    if (!$currentUser) {
      header('Location: /login');
      exit;
    }

    $channel = Channel::findById($channelId);

    // si le salon n'existe pas ou que c'est un MP
    if (!$channel || $channel->type === ChannelType::direct) {
      self::notFound();
      return;
    }

    // si le salon n'est pas public et qu'il n'appartient pas à l'utilisateur
    if ($channel->type !== ChannelType::public && $channel->ownerId !== $currentUser->id) {
      self::json(['error' => 'Vous n\'avez pas les droits pour inviter des membres dans ce salon.'], 403);
      return;
    }

    // on nettoie les invitations expirées
    Invite::cleanUp();

    // on récupère les invitations
    $invites = Invite::findAllForChannel($channelId);

    // s'il n'y a aucune invitation valide, on en crée une
    if (empty($invites)) {
      $invites = [Invite::create($channelId)];
    }

    self::json($invites[0]);
  }

  /**
   * Rejoint un salon public (sans besoin d'une invitation)
   */
  public static function join(int $channelId): void
  {
    $currentUser = Session::getCurrentUser();

    if (!$currentUser) {
      header('Location: /login');
      exit;
    }

    $channel = Channel::findById($channelId);

    if (!$channel || $channel->type !== ChannelType::public) {
      self::notFound();
      return;
    }

    $members = Member::findAllForChannel($channel->id);

    // si l'utilisateur est déjà membre du salon
    if (array_reduce($members, fn($acc, $member) => $acc || $member->userId === $currentUser->id, false)) {
      header("Location: /channels/{$channel->id}");
      return;
    }

    Member::create(
      userId: $currentUser->id,
      channelId: $channel->id
    );

    header("Location: /channels/{$channel->id}");
  }

  /**
   * Rejoint un salon via une invitation.
   */
  public static function joinWithToken(string $token): void
  {
    $currentUser = Session::getCurrentUser();

    if (!$currentUser) {
      header('Location: /login');
      exit;
    }

    $invite = Invite::findByToken($token);

    if (!$invite) {
      self::notFound();
      return;
    }

    $channel = Channel::findById($invite->channelId);

    if (!$channel) {
      self::notFound();
      return;
    }

    $members = Member::findAllForChannel($channel->id);

    // si l'utilisateur est déjà membre du salon
    if (array_reduce($members, fn($acc, $member) => $acc || $member->userId === $currentUser->id, false)) {
      header("Location: /channels/{$channel->id}");
      return;
    }

    Member::create(
      userId: $currentUser->id,
      channelId: $channel->id
    );

    header("Location: /channels/{$channel->id}");
  }

  /**
   * Récupère les derniers messages pour un salon donné et les renvoie en un tableau de chaînes HTML.
   */
  public static function getMessages(string $channelId, ?string $lastMessageId, bool $json = false): void
  {
    $currentUser = Session::getCurrentUser();

    if (!$currentUser) {
      header('Location: /login');
      exit;
    }

    $messages = Message::findAllForChannel($channelId, $lastMessageId ? (int) $lastMessageId : null);
    $messages = array_reverse($messages);

    if ($json) {
      self::json($messages);
      return;
    }

    $messageHtmls = [];

    foreach ($messages as $message) {
      ob_start();

      include __DIR__ . '/../Views/components/Message.php';

      array_push($messageHtmls, ob_get_clean());
    }

    self::json($messageHtmls);
  }
}