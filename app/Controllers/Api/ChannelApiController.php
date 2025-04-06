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

  /**
   * Crée un salon et le renvoie en JSON
   */
  public static function createChannel(?array $data): void
  {
    $currentUser = self::getCurrentUser();

    if (!$currentUser) {
      self::unauthorized();
    }

    if (!isset($data['name']) || empty(trim($data['name']))) {
      self::error('Bad Request', 'Channel name is required', 400);
    }

    if (strlen($data['name']) > 30) {
      self::error('Bad Request', 'Channel name must be less than 30 characters', 400);
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
      self::error('Internal Server Error', 'Failed to create channel', 500);
    }

    // on rejoint son propre salon
    Member::create(
      userId: $currentUser->id,
      channelId: $channel->id
    );

    self::json($channel);
  }
}