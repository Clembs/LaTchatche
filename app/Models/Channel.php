<?php
namespace App\Models;

use App\Core\Database;
use App\Core\Model;

enum ChannelType: string
{
  case public = "public";
  case private = "private";
  case direct = "direct";
}

class Channel extends Model
{
  public function __construct(
    public int $id,
    public string $name,
    public \DateTime $createdAt,
    public ChannelType $type,
    public int $ownerId,
  ) {
  }

  public static function findById(int $id): ?Channel
  {
    $pdo = Database::getPDO();
    $query = $pdo->prepare('SELECT * FROM channels WHERE id = :id');
    $query->execute(['id' => $id]);
    $res = $query->fetch();

    if (!$res) {
      return null;
    }

    return new Channel(
      id: $res['id'],
      name: $res['name'],
      createdAt: new \DateTime($res['created_at']),
      type: ChannelType::from($res['type']),
      ownerId: $res['owner_id'],
    );
  }

  /**
   * Fetch all public channels
   * @return Channel[]
   */
  public static function findAll(): array
  {
    $pdo = Database::getPDO();
    $query = $pdo->query('SELECT * FROM channels WHERE public = true');

    $res = $query->fetchAll();

    return array_reduce(
      $res,
      function ($acc, $channel) {
        $acc[$channel['id']] = new Channel(
          id: $channel['id'],
          name: $channel['name'],
          createdAt: new \DateTime($channel['created_at']),
          type: ChannelType::from($channel['type']),
          ownerId: $channel['owner_id'],
        );
        return $acc;
      },
      []
    );
  }

  /**
   * Fetch all channels a user is a member of
   * @return Channel[]
   */
  public static function findAllForUser(string $userId): array
  {
    $pdo = Database::getPDO();
    $query = $pdo->prepare(
      "SELECT c.* FROM channels c
      JOIN members m ON c.id = m.channel_id
      WHERE m.user_id = :userid"
    );

    $query->execute([
      'userid' => $userId,
    ]);
    $res = $query->fetchAll();

    return array_reduce(
      $res,
      function ($acc, $channel) {
        $acc[$channel['id']] = new Channel(
          id: $channel['id'],
          name: $channel['name'],
          createdAt: new \DateTime($channel['created_at']),
          type: ChannelType::from($channel['type']),
          ownerId: $channel['owner_id'],
        );
        return $acc;
      },
      []
    );
  }

  public static function create(
    string $name,
    ChannelType $type,
    int $ownerId
  ): Channel {
    $pdo = Database::getPDO();
    $query = $pdo->prepare(
      "INSERT INTO channels
      (name, type, owner_id)
      VALUES 
      (:name, :type, :owner_id)"
    );
    $query->execute([
      'name' => $name,
      'type' => $type->value,
      'owner_id' => $ownerId,
    ]);

    return new Channel(
      id: (int) $pdo->lastInsertId(),
      name: $name,
      type: $type,
      createdAt: new \DateTime(),
      ownerId: $ownerId,
    );
  }

  // TODO
  public static function update(Model $data): void
  {
  }

  public static function delete(int $id): void
  {
    $pdo = Database::getPDO();
    $query = $pdo->prepare('DELETE FROM channels WHERE id = :id');
    $query->execute(['id' => $id]);
  }

  public function jsonSerialize(): array
  {
    return [
      'id' => $this->id,
      'name' => $this->name,
      'createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
      'type' => $this->type->value,
      'ownerId' => $this->ownerId,
    ];
  }
}