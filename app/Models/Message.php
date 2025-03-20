<?php
namespace App\Models;

use App\Core\Database;
use App\Core\Model;

enum MessageType: string
{
  case default = "default";
  case user_add = "user_add";
  case user_remove = "user_remove";
  case channel_rename = "channel_rename";
}

class Message extends Model
{
  public function __construct(
    public int $id,
    public MessageType $type,
    public string $content,
    public \DateTime $createdAt,
    public ?int $authorId, // The authorId is null if the MessageType is not default (fot system messages)
    public int $channelId,
    public ?User $author = null,
  ) {
  }

  public static function findById(int $id): ?Message
  {
    $pdo = Database::getPDO();
    $query = $pdo->prepare(
      "SELECT 
      messages.id AS message_id,
      messages.type AS message_type,
      messages.content AS message_content,
      messages.created_at AS message_created_at,
      messages.author_id AS message_author_id,
      messages.channel_id AS message_channel_id,
      users.id AS user_id,
      users.username AS user_username,
      users.password AS user_password,
      users.created_at AS user_created_at
      FROM messages, users 
      WHERE messages.id = :id
      AND messages.author_id = users.id"
    );
    $query->execute(['id' => $id]);
    $res = $query->fetch();

    if (!$res) {
      return null;
    }

    return new Message(
      id: $res['message_id'],
      type: MessageType::tryFrom($res['message_type']),
      content: $res['message_content'],
      createdAt: new \DateTime($res['message_created_at']),
      authorId: $res['message_author_id'],
      channelId: $res['message_channel_id'],
      author: new User(
        id: $res['user_id'],
        username: $res['user_username'],
        password: $res['user_password'],
        createdAt: new \DateTime($res['user_created_at']),
      ),
    );
  }

  /**
   * Renvoie une liste de messages pour un ID de salon donné
   * @return Message[]
   */
  public static function findAllForChannel(int $channelId, int $page = 1): array
  {
    $pdo = Database::getPDO();
    $query = $pdo->prepare(
      "SELECT
      messages.id AS message_id,
      messages.type AS message_type,
      messages.content AS message_content,
      messages.created_at AS message_created_at,
      messages.author_id AS message_author_id,
      messages.channel_id AS message_channel_id,
      users.id AS user_id,
      users.username AS user_username,
      users.password AS user_password,
      users.created_at AS user_created_at
      FROM messages, users
      WHERE messages.channel_id = :channel_id
      AND messages.author_id = users.id
      ORDER BY messages.created_at DESC
      LIMIT 30 OFFSET :offset"
    );
    $offset = ($page - 1) * 10;
    // J'ignore pourquoi je dois utiliser bindParam au lieu d'execute
    // mais execute fonctionnait pas :(
    $query->bindParam('channel_id', $channelId);
    $query->bindParam('offset', $offset, \PDO::PARAM_INT);
    $query->execute();
    $res = $query->fetchAll();

    return array_reduce(
      $res,
      function ($acc, $message) {
        $acc[$message['message_id']] = new Message(
          id: $message['message_id'],
          type: MessageType::tryFrom($message['message_type']),
          content: $message['message_content'],
          createdAt: new \DateTime($message['message_created_at']),
          authorId: $message['message_author_id'],
          channelId: $message['message_channel_id'],
          author: new User(
            id: (int) $message['user_id'],
            username: $message['user_username'],
            password: $message['user_password'],
            createdAt: new \DateTime($message['user_created_at']),
          ),
        );
        return $acc;
      },
      []
    );
  }

  // Par souci de confidentialité, on ne peut pas lister tous les messages de tous les salons
  public static function findAll(): array
  {
    return [];
  }

  public static function create(MessageType $messageType, string $content, int $authorId, int $channelId, ?User $author): Message
  {
    $pdo = Database::getPDO();
    $query = $pdo->prepare(
      "INSERT INTO messages
      (type, content, created_at, author_id, channel_id)
      VALUES 
      (:type, :content, :created_at, :author_id, :channel_id)"
    );
    $createdAt = new \DateTime();

    $query->execute([
      'type' => $messageType->value,
      'content' => $content,
      'created_at' => $createdAt->format('Y-m-d H:i:s'),
      'author_id' => $authorId,
      'channel_id' => $channelId,
    ]);

    return new Message(
      id: (int) $pdo->lastInsertId(),
      type: $messageType,
      content: $content,
      createdAt: $createdAt,
      authorId: $authorId,
      channelId: $channelId,
      author: $author
    );
  }

  public static function update(Model $data): void
  {
    if (!($data instanceof self)) {
      throw new \InvalidArgumentException('Invalid data type');
    }

    $pdo = Database::getPDO();
    $query = $pdo->prepare(
      "UPDATE messages
      SET content = :content
      WHERE id = :id"
    );

    $query->execute([
      'id' => $data->id,
      'content' => $data->content,
    ]);
  }

  public static function delete(int $id): void
  {
    $pdo = Database::getPDO();
    $query = $pdo->prepare('DELETE FROM messages WHERE id = :id');
    $query->execute(['id' => $id]);
  }

  public function jsonSerialize(): array
  {
    return [
      'id' => $this->id,
      'type' => $this->type->value,
      'content' => $this->content,
      'createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
      'authorId' => $this->authorId,
      'channelId' => $this->channelId,
      'author' => $this->author,
    ];
  }
}