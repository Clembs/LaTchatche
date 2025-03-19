<?php
namespace App\Models;

use App\Core\Model;

class Member extends Model
{
  public function __construct(
    public int $id,
    public int $userId,
    public int $channelId,
  ) {
  }

  public static function findById(int $id): Member
  {
    $query = self::$pdo->prepare('SELECT * FROM members WHERE id = :id');
    $query->execute(['id' => $id]);

    return $query->fetchObject(self::class);
  }

  public static function findByUserChannel(int $userId, int $channelId): Member
  {
    $query = self::$pdo->prepare('SELECT * FROM members WHERE user_id = :userId AND channel_id = :channelId');
    $query->execute(['userId' => $userId, 'channelId' => $channelId]);

    return $query->fetchObject(self::class);
  }

  /**
   * @return Member[]
   */
  public static function findAll(): array
  {
    $query = self::$pdo->query('SELECT * FROM members');

    return $query->fetchAll(\PDO::FETCH_CLASS, self::class);
  }

  public static function findAllByChannel(int $channelId): array
  {
    $query = self::$pdo->prepare('SELECT * FROM members WHERE channel_id = :channelId');
    $query->execute(['channelId' => $channelId]);

    return $query->fetchAll(\PDO::FETCH_CLASS, self::class);
  }

  public static function create(Model $data): Member
  {
    if (!($data instanceof self)) {
      throw new \InvalidArgumentException('Invalid data type');
    }

    $query = self::$pdo->prepare(
      "INSERT INTO members
      (user_id, channel_id)
      VALUES 
      (:userId, :channelId)"
    );
    $query->execute([
      'userId' => $data->userId,
      'channelId' => $data->channelId,
    ]);

    return new Member(
      id: (int) self::$pdo->lastInsertId(),
      userId: $data->userId,
      channelId: $data->channelId,
    );
  }

  public static function update(Model $data): void
  {
  }

  public static function delete(int $id): void
  {
    $query = self::$pdo->prepare('DELETE FROM members WHERE id = :id');
    $query->execute(['id' => $id]);
  }

  public function jsonSerialize(): array
  {
    return [
      'id' => $this->id,
      'userId' => $this->userId,
      'channelId' => $this->channelId,
    ];
  }
}