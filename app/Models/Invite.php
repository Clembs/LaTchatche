<?php
namespace App\Models;

use App\Core\Database;
use App\Core\Model;

class Invite extends Model
{
  // Durée de vie d'une invitation en secondes (3 jours)
  private const int INVITE_DURATION = 3 * 24 * 60 * 60;

  public function __construct(
    public int $id,
    public int $channelId,
    public string $token,
    public \DateTime $expiresAt,
  ) {
  }

  public static function findByToken(string $token): ?Invite
  {
    $pdo = Database::getPDO();
    $query = $pdo->prepare('SELECT * FROM invites WHERE token = :token AND expires_at > CURRENT_TIMESTAMP');
    $query->execute(['token' => $token]);
    $res = $query->fetch();

    if (!$res) {
      return null;
    }

    return new Invite(
      id: $res['id'],
      channelId: $res['channel_id'],
      token: $res['token'],
      expiresAt: new \DateTime($res['expires_at']),
    );
  }

  /**
   * Supprime les invites expirées
   */
  public static function cleanUp(): void
  {
    $pdo = Database::getPDO();
    $query = $pdo->prepare('DELETE FROM invites WHERE expires_at < CURRENT_TIMESTAMP');
    $query->execute();
  }

  /**
   * Récupère tous les invites valides pour un salon donné
   * @return Invite[]
   */
  public static function findAllForChannel(int $channelId): array
  {
    $pdo = Database::getPDO();
    $query = $pdo->prepare('SELECT * FROM invites WHERE channel_id = :channel_id AND expires_at > CURRENT_TIMESTAMP');
    $query->execute(['channel_id' => $channelId]);
    $res = $query->fetchAll();

    return array_map(
      fn($invite) => new Invite(
        id: $invite['id'],
        channelId: $invite['channel_id'],
        token: $invite['token'],
        expiresAt: new \DateTime($invite['expires_at']),
      ),
      $res
    );
  }

  public static function create(int $channelId): Invite
  {
    $token = bin2hex(random_bytes(16));
    $expiresAt = new \DateTime('@' . (time() + self::INVITE_DURATION));

    $pdo = Database::getPDO();
    $query = $pdo->prepare('INSERT INTO invites (channel_id, token, expires_at) VALUES (:channel_id, :token, :expires_at)');
    $query->execute([
      'channel_id' => $channelId,
      'token' => $token,
      'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
    ]);

    return new Invite(
      id: (int) $pdo->lastInsertId(),
      channelId: $channelId,
      token: $token,
      expiresAt: $expiresAt,
    );
  }

  public function jsonSerialize(): array
  {
    return [
      'id' => $this->id,
      'channelId' => $this->channelId,
      'token' => $this->token,
      'expiresAt' => $this->expiresAt->format('Y-m-d H:i:s'),
    ];
  }
}