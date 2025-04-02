<?php

namespace App\Controllers\Api;

use App\Core\ApiController;
use App\Models\Channel;
use App\Models\ChannelType;
use App\Models\Member;
use App\Models\Message;
use App\Models\MessageType;

class ChannelApiController extends ApiController
{

  /**
   * Renvoie la liste des salons publics en JSON.
   */
  public static function publicChannels(): void
  {
    $channels = Channel::findAllPublic();

    self::json(array_values($channels));
  }

  /**
   * Renvoie la liste des salons de l'utilisateur connecté en JSON.
   */
  public static function userChannels(): void
  {
    $currentUser = self::getCurrentUser();

    if (!$currentUser) {
      self::unauthorized();
    }

    $channels = Channel::findAllForUser($currentUser->id);

    if (!$channels) {
      self::notFound();
    }

    self::json(array_values($channels));
  }

  /**
   * Renvoie un salon en JSON.
   */
  public static function channel(int $id): void
  {
    $channel = Channel::findById($id);

    if (!$channel) {
      self::notFound();
    }

    if ($channel->type === ChannelType::public) {
      self::json($channel);
    }

    $currentUser = self::getCurrentUser();

    if (!$currentUser) {
      self::error('Unauthorized', 'You must be logged in to access this channel', 401);
    }

    if (!Member::isMember($channel->id, $currentUser->id)) {
      self::error('Unauthorized', 'You must be a member of this channel to access it', 403);
    }

    self::json($channel);
  }

  /**
   * Renvoie les messages d'un salon en JSON.
   */
  public static function getMessages(int $id, ?int $lastMessageId): void
  {
    $channel = Channel::findById($id);

    if (!$channel) {
      self::notFound();
    }

    $currentUser = self::getCurrentUser();

    if ($channel->type !== ChannelType::public && !Member::isMember($channel->id, $currentUser->id)) {
      self::error('Unauthorized', 'You must be a member of this channel to access it', 403);
    }

    $messages = Message::findAllForChannel($id, $lastMessageId);
    $messages = array_reverse($messages);

    self::json($messages);
  }

  /**
   * Crée un message dans un salon donné et le renvoie en JSON
   */
  public static function createMessage(int $channelId, ?array $data): void
  {
    $currentUser = self::getCurrentUser();

    if (!$currentUser) {
      self::unauthorized();
    }

    if (!isset($data['content'])) {
      self::error('Bad Request', 'Content is required', 400);
    }

    $channel = Channel::findById($channelId);

    if (!$channel) {
      self::notFound();
    }

    if ($channel->type !== ChannelType::public && !Member::isMember($channel->id, $currentUser->id)) {
      self::error('Unauthorized', 'You must be a member of this channel to access it', 403);
    }

    $message = Message::create(
      type: MessageType::default ,
      content: $data['content'],
      channelId: $channelId,
      author: $currentUser
    );

    self::json($message);
  }
}