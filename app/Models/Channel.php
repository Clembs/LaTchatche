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
    public string $ownerUsername,
    public int $memberCount,
    public int $messageCount,
  ) {
  }

  public static function findById(int $id): ?Channel
  {
    $pdo = Database::getPDO();
    $query = $pdo->prepare(
      'SELECT c.id, c.name, c.created_at, c.type, c.owner_id,
        COUNT(DISTINCT mb.user_id) AS member_count,
        COUNT(msg.id) AS message_count,
        u.username AS owner_username
      FROM channels AS c
      LEFT JOIN members AS mb ON c.id = mb.channel_id
      LEFT JOIN messages AS msg ON c.id = msg.channel_id
      LEFT JOIN users AS u ON c.owner_id = u.id
      WHERE c.id = :id
      GROUP BY c.id, c.name, c.created_at, c.type, c.owner_id, u.username'
    );
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
      ownerUsername: $res['owner_username'],
      memberCount: $res['member_count'],
      messageCount: $res['message_count'],
    );
  }

  /**
   * Fetch all public channels
   * @return Channel[]
   */
  public static function findAllPublic(): array
  {
    $pdo = Database::getPDO();
    $query = $pdo->query(
      'SELECT c.id, c.name, c.created_at, c.type, c.owner_id,
        COUNT(DISTINCT mb.user_id) AS member_count,
        COUNT(msg.id) AS message_count,
        u.username AS owner_username
      FROM channels AS c
      LEFT JOIN members AS mb ON c.id = mb.channel_id
      LEFT JOIN messages AS msg ON c.id = msg.channel_id
      LEFT JOIN users AS u ON c.owner_id = u.id
      WHERE c.type = "public"
      GROUP BY c.id, c.name, c.created_at, c.type, c.owner_id, u.username'
    );

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
          ownerUsername: $channel['owner_username'],
          memberCount: $channel['member_count'],
          messageCount: $channel['message_count'],
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
      'SELECT c.id, c.name, c.created_at, c.type, c.owner_id,
        COUNT(DISTINCT mb.user_id) AS member_count,
        COUNT(msg.id) AS message_count,
        u.username AS owner_username
      FROM channels AS c
      LEFT JOIN members AS mb ON c.id = mb.channel_id
      LEFT JOIN messages AS msg ON c.id = msg.channel_id
      LEFT JOIN users AS u ON c.owner_id = u.id
      WHERE mb.user_id = :userid
      GROUP BY c.id, c.name, c.created_at, c.type, c.owner_id, u.username'
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
          ownerUsername: $channel['owner_username'],
          memberCount: $channel['member_count'],
          messageCount: $channel['message_count'],
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
      ownerUsername: '',
      memberCount: 1,
      messageCount: 0,
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
      'owner' => [
        'id' => $this->ownerId,
        'username' => $this->ownerUsername,
      ],
      'memberCount' => $this->memberCount,
      'messageCount' => $this->messageCount,
    ];
  }
}