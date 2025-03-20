<?php
namespace App\Models;

use App\Core\Database;
use App\Core\Model;

class Channel extends Model
{
  public function __construct(
    public int $id,
    public string $name,
    public \DateTime $createdAt,
    public bool $public,
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
      public: $res['public'],
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
          public: $channel['public'],
          ownerId: $channel['owner_id'],
        );
        return $acc;
      },
      []
    );
  }

  public static function create(Model $data): Channel
  {
    if (!($data instanceof self)) {
      throw new \InvalidArgumentException('Invalid data type');
    }

    $pdo = Database::getPDO();
    $query = $pdo->prepare(
      "INSERT INTO channels
      (name, public, owner_id)
      VALUES 
      (:name, :public, :owner_id)"
    );
    $query->execute([
      'name' => $data->name,
      'public' => $data->public,
      'owner_id' => $data->ownerId,
    ]);

    return new Channel(
      id: (int) $pdo->lastInsertId(),
      name: $data->name,
      createdAt: new \DateTime(),
      public: $data->public,
      ownerId: $data->ownerId,
    );
  }

  public static function update(Model $data): void
  {
    if (!($data instanceof self)) {
      throw new \InvalidArgumentException('Invalid data type');
    }

    $pdo = Database::getPDO();
    $query = $pdo->prepare(
      "UPDATE channels
      SET name = :name, public = :public
      WHERE id = :id"
    );

    $query->execute([
      'id' => $data->id,
      'name' => $data->name,
      'public' => $data->public,
    ]);
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
      'public' => $this->public,
      'ownerId' => $this->ownerId,
    ];
  }
}