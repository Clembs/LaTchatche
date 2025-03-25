<?php
namespace App\Models;

use App\Core\Database;
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

  public static function findById(int $id): ?User
  {
    $pdo = Database::getPDO();
    $query = $pdo->prepare('SELECT * FROM users WHERE id = :id');
    $query->execute(['id' => $id]);
    $res = $query->fetch();

    if (!$res) {
      return null;
    }

    return new User(
      id: $res['id'],
      username: $res['username'],
      password: $res['password'],
      createdAt: new \DateTime($res['created_at']),
    );
  }

  public static function findByUsername(string $username): ?User
  {
    $pdo = Database::getPDO();
    $query = $pdo->prepare('SELECT * FROM users WHERE username = :username');
    $query->execute(['username' => $username]);
    $res = $query->fetch();

    if (!$res) {
      return null;
    }

    return new User(
      id: $res['id'],
      username: $res['username'],
      password: $res['password'],
      createdAt: new \DateTime($res['created_at']),
    );
  }

  /**
   * @return User[]
   */
  public static function findAll(): array
  {

    $pdo = Database::getPDO();
    $query = $pdo->query('SELECT * FROM users');
    $res = $query->fetchAll();

    return array_reduce(
      $res,
      function ($acc, $user) {
        $acc[$user['id']] = new User(
          id: $user['id'],
          username: $user['username'],
          password: $user['password'],
          createdAt: new \DateTime($user['created_at']),
        );
        return $acc;
      },
      []
    );
  }

  public static function findByUsernamePassword(string $username, string $password): ?User
  {
    $pdo = Database::getPDO();
    $query = $pdo->prepare('SELECT * FROM users WHERE username = :username');
    $query->execute(['username' => $username]);
    $res = $query->fetch();

    if (!$res || !password_verify($password, $res['password'])) {
      return null;
    }

    return new User(
      id: $res['id'],
      username: $res['username'],
      password: $res['password'],
      createdAt: new \DateTime($res['created_at']),
    );
  }

  public static function create(string $username, string $password): User
  {
    $pdo = Database::getPDO();
    $query = $pdo->prepare('INSERT INTO users (username, password) VALUES (:username, :password)');
    $query->execute([
      'username' => $username,
      'password' => password_hash($password, PASSWORD_DEFAULT),
    ]);

    return new User(
      id: (int) $pdo->lastInsertId(),
      username: $username,
      password: $password,
      createdAt: new \DateTime(),
    );
  }

  public static function update(Model $data): void
  {
    if (!($data instanceof self)) {
      throw new \InvalidArgumentException('Invalid data type');
    }

    $pdo = Database::getPDO();
    $query = $pdo->prepare(
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

    $pdo = Database::getPDO();
    $query = $pdo->prepare('DELETE FROM users WHERE id = :id');
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