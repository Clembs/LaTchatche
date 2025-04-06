<?php

namespace App\Models;

use App\Core\Database;
use App\Models\User;

class Session
{
  // Durée de vie de la session en secondes (7 jours)
  private const int SESSION_DURATION = 7 * 24 * 60 * 60;
  // l'identifiant de session et le nom du cookie
  private const string SESSION_COOKIE_NAME = 'session_token';

  public static function getSessionToken(): ?string
  {
    $sessionToken = $_COOKIE[self::SESSION_COOKIE_NAME];

    if (!isset($sessionToken)) {
      return null;
    }

    return $sessionToken;
  }

  /**
   * Crée une nouvelle session pour un utilisateur donné
   */
  public static function create(User $user): string
  {
    // self::initSession();
    $sessionToken = session_create_id();

    $linkpdo = Database::getPDO();

    // On supprime les sessions expirées
    $req = $linkpdo->prepare(
      "DELETE FROM sessions 
        WHERE user_id = :id 
        AND expires_at <= CURRENT_TIMESTAMP"
    );
    $req->execute(['id' => $user->id]);

    // On crée une nouvelle session qui expire dans SESSION_TEMPS_VIE
    $expiresAt = new \DateTime('@' . (time() + self::SESSION_DURATION));
    $req = $linkpdo->prepare(
      "INSERT INTO sessions (token, expires_at, user_id) 
                 VALUES (:token, :expires_at, :user_id)"
    );

    $req->execute([
      'token' => $sessionToken,
      'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
      'user_id' => $user->id,
    ]);

    // On crée un cookie de session
    setcookie(
      self::SESSION_COOKIE_NAME,
      $sessionToken,
      [
        'expires' => time() + self::SESSION_DURATION,
        'path' => '/',
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Strict'
      ]
    );

    return $sessionToken;
  }

  /**
   * Retourne l'utilisateur actuellement connecté
   */
  public static function getCurrentUser(): ?User
  {
    $sessionToken = self::getSessionToken();

    if (!$sessionToken) {
      return null;
    }

    $pdo = Database::getPDO();
    $query = $pdo->prepare(
      "SELECT 
      u.id as user_id,
      u.username as user_username,
      u.password as user_password,
      u.created_at as user_created_at,
      s.expires_at as session_expires_at
      FROM users AS u 
        JOIN sessions AS s ON u.id = s.user_id 
        WHERE s.token = :token 
        AND s.expires_at > CURRENT_TIMESTAMP"
    );

    $query->execute(['token' => $sessionToken]);
    $res = $query->fetch();

    if (!$res) {
      self::destroy();
      return null;
    }

    // si la session expire dans moins d'un jour, on la prolonge
    if (new \DateTime($res['session_expires_at']) < new \DateTime('now +1 day')) {
      self::extend();
    }

    return new User(
      $res['user_id'],
      $res['user_username'],
      $res['user_password'],
      new \DateTime($res['user_created_at']),
    );
  }

  /**
   * Retourne vrai si l'utilisateur est connecté
   */
  public static function isLoggedIn(): bool
  {
    return self::getCurrentUser() !== null;
  }

  /*
   * Prolonge la session de SESSION_DURATION secondes
   */
  private static function extend(): void
  {
    $sessionToken = self::getSessionToken();

    if (!$sessionToken) {
      return;
    }

    $newExpiresAt = new \DateTime('@' . (time() + self::SESSION_DURATION));

    $pdo = Database::getPDO();
    $query = $pdo->prepare(
      'UPDATE sessions 
        SET expires_at = :expires_at 
        WHERE token = :token'
    );

    $query->execute([
      'expires_at' => $newExpiresAt->format('Y-m-d H:i:s'),
      'token' => $sessionToken
    ]);

    setcookie(
      self::SESSION_COOKIE_NAME,
      $sessionToken,
      [
        'expires' => time() + self::SESSION_DURATION,
        'path' => '/',
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Strict'
      ]
    );
  }

  /**
   * Détruit la session actuelle
   */
  public static function destroy(): void
  {
    $sessionToken = self::getSessionToken();

    if (!$sessionToken) {
      return;
    }

    $pdo = Database::getPDO();
    $query = $pdo->prepare('DELETE FROM sessions WHERE token = :token');
    $query->execute(['token' => $sessionToken]);

    setcookie(self::SESSION_COOKIE_NAME, '', [
      'expires' => time() - 3600,
      'path' => '/',
      'secure' => true,
      'httponly' => true,
      'samesite' => 'Strict'
    ]);
  }

  /**
   * Retourne l'utilisateur correspondant au jeton de session
   */
  public static function findByToken(string $token): ?User
  {
    $pdo = Database::getPDO();
    $query = $pdo->prepare(
      "SELECT u.id as user_id,
        u.username as user_username,
        u.password as user_password,
        u.created_at as user_created_at
        FROM users AS u 
        JOIN sessions AS s ON u.id = s.user_id 
        WHERE s.token = :token"
    );

    $query->execute(['token' => $token]);
    $res = $query->fetch();

    if (!$res) {
      return null;
    }

    return new User(
      $res['user_id'],
      $res['user_username'],
      $res['user_password'],
      new \DateTime($res['user_created_at']),
    );
  }

  /**
   * Supprime la session à partir d'un jeton
   */
  public static function delete(int $token): void
  {
    $pdo = Database::getPDO();
    $query = $pdo->prepare('DELETE FROM sessions WHERE token = :token');
    $query->execute(['token' => $token]);
  }
}