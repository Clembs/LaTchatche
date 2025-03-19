<?php
namespace App\Models;

use App\Core\Model;

class User extends Model
{
  public function __construct(
    public int $id,
    public string $username,
    public string $password,
    public \DateTime $createdAt,
  ) {
  }

  public static function findById(int $id): User
  {

    $query = self::$pdo->prepare('SELECT * FROM users WHERE id = :id');
    $query->execute(['id' => $id]);

    return $query->fetchObject(self::class);
  }

  /**
   * @return User[]
   */
  public static function findAll(): array
  {

    $query = self::$pdo->query('SELECT * FROM users');

    return $query->fetchAll(\PDO::FETCH_CLASS, self::class);
  }

  public static function create(Model $data): User
  {
    if (!($data instanceof self)) {
      throw new \InvalidArgumentException('Invalid data type');
    }


    $query = self::$pdo->prepare('INSERT INTO users (username, password) VALUES (:username, :password)');
    $query->execute([
      'username' => $data->username,
      'password' => password_hash($data->password, PASSWORD_DEFAULT),
    ]);

    return new User(
      id: (int) self::$pdo->lastInsertId(),
      username: $data->username,
      password: $data->password,
      createdAt: new \DateTime(),
    );
  }

  public static function update(Model $data): void
  {
    if (!($data instanceof self)) {
      throw new \InvalidArgumentException('Invalid data type');
    }

    $query = self::$pdo->prepare(
      "UPDATE users
      SET username = :username, password = :password
      WHERE id = :id"
    );

    $query->execute([
      'id' => $data->id,
      'username' => $data->username,
      'password' => password_hash($data->password, PASSWORD_DEFAULT),
    ]);
  }

  public static function delete(int $id): void
  {

    $query = self::$pdo->prepare('DELETE FROM users WHERE id = :id');
    $query->execute(['id' => $id]);
  }

  public function jsonSerialize(): array
  {
    return [
      'id' => $this->id,
      'username' => $this->username,
      'createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
    ];
  }
}