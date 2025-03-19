<?php
namespace App\Models;

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

  public static function findById(int $id): Channel
  {
    $query = self::$pdo->prepare('SELECT * FROM channels WHERE id = :id');
    $query->execute(['id' => $id]);

    return $query->fetchObject(self::class);
  }

  /**
   * @return Channel[]
   */
  public static function findAll(): array
  {
    $query = self::$pdo->query('SELECT * FROM channels');

    return $query->fetchAll(\PDO::FETCH_CLASS, self::class);
  }

  public static function create(Model $data): Channel
  {
    if (!($data instanceof self)) {
      throw new \InvalidArgumentException('Invalid data type');
    }

    $query = self::$pdo->prepare(
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
      id: (int) self::$pdo->lastInsertId(),
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

    $query = self::$pdo->prepare(
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
    $query = self::$pdo->prepare('DELETE FROM channels WHERE id = :id');
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