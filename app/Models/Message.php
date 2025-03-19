<?php
namespace App\Models;

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

  public static function findById(int $id): Message
  {
    $query = self::$pdo->prepare('SELECT * FROM messages WHERE id = :id');
    $query->execute(['id' => $id]);

    return $query->fetchObject(self::class);
  }

  /**
   * @return Message[]
   */
  public static function findAll(): array
  {
    $query = self::$pdo->query('SELECT * FROM messages');

    return $query->fetchAll(\PDO::FETCH_CLASS, self::class);
  }

  public static function create(Model $data): Message
  {
    if (!($data instanceof self)) {
      throw new \InvalidArgumentException('Invalid data type');
    }

    $query = self::$pdo->prepare(
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
      id: (int) self::$pdo->lastInsertId(),
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

    $query = self::$pdo->prepare(
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
    $query = self::$pdo->prepare('DELETE FROM messages WHERE id = :id');
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