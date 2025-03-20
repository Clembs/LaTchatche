<?php

namespace App\Models;

use App\Core\Database;
use App\Models\User;

class Session
{
  // Durée de vie de la session en secondes (7 jours)
  private const int SESSION_TEMPS_VIE = 7 * 24 * 60 * 60;
  // l'identifiant de session et le nom du cookie
  private const string SESSION_NOM_COOKIE = 'session_token';

  /**
   * Initialise la session PHP si elle n'est pas déjà démarrée
   */
  private static function initSession(): void
  {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }
  }

  /**
   * Crée une nouvelle session pour un utilisateur donné
   */
  public static function create(User $user): void
  {
    self::initSession();
    $sessionToken = session_id();

    $linkpdo = Database::getPDO();

    // On supprime les sessions expirées
    $req = $linkpdo->prepare(
      "DELETE FROM sessions 
        WHERE user_id = :id 
        OR expires_at <= CURRENT_TIMESTAMP"
    );
    $req->execute(['id' => $user->id]);

    // On crée une nouvelle session qui expire dans SESSION_TEMPS_VIE
    $expiresAt = new \DateTime('@' . (time() + self::SESSION_TEMPS_VIE));
    $req = $linkpdo->prepare(
      "INSERT INTO sessions (token, expires_at, user_id) 
                 VALUES (:token, :expires_at, :user_id)"
    );

    $req->execute([
      'token' => $sessionToken,
      'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
      'user_id' => $user->id,
    ]);

    // On ajoute les informations de session à la session PHP
    $_SESSION['user_id'] = $user->id;
    $_SESSION['expires_at'] = $expiresAt->format('Y-m-d H:i:s');

    // On crée un cookie de session
    setcookie(
      self::SESSION_NOM_COOKIE,
      $sessionToken,
      [
        'expires' => time() + self::SESSION_TEMPS_VIE,
        'path' => '/',
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Strict'
      ]
    );

  }

  /**
   * Retourne l'utilisateur actuellement connecté
   */
  public static function getCurrentUser(): ?User
  {
    self::initSession();

    if (!isset($_SESSION['user_id']) || !isset($_SESSION['expires_at'])) {
      return null;
    }

    $linkpdo = Database::getPDO();
    $req = $linkpdo->prepare(
      'SELECT u.* FROM users AS u 
        JOIN sessions AS s ON u.id = s.user_id 
        WHERE s.token = :token 
        AND s.expires_at > CURRENT_TIMESTAMP'
    );

    $req->execute(['token' => session_id()]);
    $res = $req->fetch();

    if (!$res) {
      self::destroy();
      return null;
    }

    // si la session expire dans moins d'un jour, on la prolonge
    if (new \DateTime($res['expires_at']) < new \DateTime('now +1 day')) {
      self::extend();
    }

    return new User(
      $res['id'],
      $res['username'],
      $res['password'],
      new \DateTime($res['created_at']),
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
   * Prolonge la session de SESSION_TEMPS_VIE secondes
   */
  private static function extend(): void
  {
    $newExpiresAt = new \DateTime('@' . (time() + self::SESSION_TEMPS_VIE));
    $sessionToken = session_id();

    $linkpdo = Database::getPDO();
    $req = $linkpdo->prepare(
      'UPDATE sessions 
          SET expires_at = :expires_at 
          WHERE token = :token'
    );

    $req->execute([
      'expires_at' => $newExpiresAt->format('Y-m-d H:i:s'),
      'token' => $sessionToken
    ]);

    $_SESSION['expires_at'] = $newExpiresAt->format('Y-m-d H:i:s');

    setcookie(
      self::SESSION_NOM_COOKIE,
      $sessionToken,
      [
        'expires' => time() + self::SESSION_TEMPS_VIE,
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
    self::initSession();

    $linkpdo = Database::getPDO();
    $req = $linkpdo->prepare('DELETE FROM sessions WHERE token = :token');
    $req->execute(['token' => session_id()]);

    session_unset();
    session_destroy();

    setcookie(self::SESSION_NOM_COOKIE, '', [
      'expires' => time() - 3600,
      'path' => '/',
      'secure' => true,
      'httponly' => true,
      'samesite' => 'Strict'
    ]);
  }
}