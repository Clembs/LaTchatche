<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Channel;

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
    $channels = Channel::findAll();
    $channel = Channel::findById($id);

    if (!$channel) {
      self::notFound();
      return;
    }

    self::render('chat/channel', $channel->name, ['channel' => $channel, 'channels' => $channels]);
  }
}
