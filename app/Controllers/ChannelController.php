<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Channel;
use App\Models\ChannelType;
use App\Models\Invite;
use App\Models\Member;
use App\Models\Session;

class ChannelController extends Controller
{
  /**
   * Crée un salon, fait rejoindre l'utilisateur et redirige vers le salon.
   */
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

    header("Location: /chats/{$channel->id}");
  }


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

    // si le salon n'est pas public ou qu'il n'appartient pas à l'utilisateur
    if ($channel->type !== ChannelType::public || $channel->ownerId !== $currentUser->id) {
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
}