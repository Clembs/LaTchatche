<?php
namespace App\Models;

use App\Core\Database;
use App\Core\Model;

class Member extends Model
{
  public function __construct(
    public int $id,
    public int $userId,
    public int $channelId,
  ) {
  }

  public static function findById(int $id): ?Member
  {
    $pdo = Database::getPDO();
    $query = $pdo->prepare('SELECT * FROM members WHERE id = :id');
    $query->execute(['id' => $id]);
    $res = $query->fetch();

    if (!$res) {
      return null;
    }

    return new Member(
      id: $res['id'],
      userId: $res['user_id'],
      channelId: $res['channel_id'],
    );
  }

  public static function findByUserChannel(int $userId, int $channelId): ?Member
  {
    $pdo = Database::getPDO();
    $query = $pdo->prepare('SELECT * FROM members WHERE user_id = :userId AND channel_id = :channelId');
    $query->execute(['userId' => $userId, 'channelId' => $channelId]);
    $res = $query->fetch();

    if (!$res) {
      return null;
    }

    return new Member(
      id: $res['id'],
      userId: $res['user_id'],
      channelId: $res['channel_id'],
    );
  }

  /**
   * @return Member[]
   */
  public static function findAll(): array
  {
    $pdo = Database::getPDO();
    $query = $pdo->query('SELECT * FROM members');
    $res = $query->fetchAll();

    return array_reduce(
      $res,
      function ($acc, $member) {
        $acc[$member['id']] = new Member(
          id: $member['id'],
          userId: $member['user_id'],
          channelId: $member['channel_id'],
        );
        return $acc;
      },
      []
    );
  }

  /**
   * @return Member[]
   */
  public static function findAllForChannel(int $channelId): array
  {
    $pdo = Database::getPDO();
    $query = $pdo->prepare('SELECT * FROM members WHERE channel_id = :channelId');
    $query->execute(['channelId' => $channelId]);
    $res = $query->fetchAll();

    return array_reduce(
      $res,
      function ($acc, $member) {
        $acc[$member['id']] = new Member(
          id: $member['id'],
          userId: $member['user_id'],
          channelId: $member['channel_id'],
        );
        return $acc;
      },
      []
    );
  }

  public static function create(int $userId, int $channelId): Member
  {
    $pdo = Database::getPDO();
    $query = $pdo->prepare(
      "INSERT INTO members
      (user_id, channel_id)
      VALUES 
      (:userId, :channelId)"
    );
    $query->execute([
      'userId' => $userId,
      'channelId' => $channelId,
    ]);

    return new Member(
      id: (int) $pdo->lastInsertId(),
      userId: $userId,
      channelId: $channelId,
    );
  }

  // A member object can't be updated
  public static function update(Model $data): void
  {
  }

  public static function delete(int $id): void
  {
    $pdo = Database::getPDO();
    $query = $pdo->prepare('DELETE FROM members WHERE id = :id');
    $query->execute(['id' => $id]);
  }

  public function jsonSerialize(): array
  {
    return [
      'id' => $this->id,
      'user_id' => $this->userId,
      'channel_id' => $this->channelId,
    ];
  }
}