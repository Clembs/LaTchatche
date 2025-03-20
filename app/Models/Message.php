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
  ) {
  }

  public static function findById(int $id): ?Message
  {
    $pdo = Database::getPDO();
    $query = $pdo->prepare('SELECT * FROM messages WHERE id = :id');
    $query->execute(['id' => $id]);
    $res = $query->fetch();

    if (!$res) {
      return null;
    }

    return new Message(
      id: $res['id'],
      type: MessageType::tryFrom($res['type']),
      content: $res['content'],
      createdAt: new \DateTime($res['created_at']),
      authorId: $res['author_id'],
      channelId: $res['channel_id'],
    );
  }

  /**
   * @return Message[]
   */
  public static function findAll(): array
  {
    $pdo = Database::getPDO();
    $query = $pdo->query('SELECT * FROM messages');
    $res = $query->fetchAll();

    return array_reduce(
      $res,
      function ($acc, $message) {
        $acc[$message['id']] = new Message(
          id: $message['id'],
          type: MessageType::tryFrom($message['type']),
          content: $message['content'],
          createdAt: new \DateTime($message['created_at']),
          authorId: $message['author_id'],
          channelId: $message['channel_id'],
        );
        return $acc;
      },
      []
    );
  }

  public static function create(Model $data): Message
  {
    if (!($data instanceof self)) {
      throw new \InvalidArgumentException('Invalid data type');
    }

    $pdo = Database::getPDO();
    $query = $pdo->prepare(
      "INSERT INTO messages
      (type, content, created_at, author_id, channel_id)
      VALUES 
      (:type, :content, :created_at, :author_id, :channel_id)"
    );
    $query->execute([
      'type' => $data->type->value,
      'content' => $data->content,
      'created_at' => $data->createdAt->format('Y-m-d H:i:s'),
      'author_id' => $data->authorId,
      'channel_id' => $data->channelId,
    ]);

    return new Message(
      id: (int) $pdo->lastInsertId(),
      type: $data->type,
      content: $data->content,
      createdAt: new \DateTime(),
      authorId: $data->authorId,
      channelId: $data->channelId,
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
    ];
  }
}