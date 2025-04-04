<?php

namespace App\Controllers\Api;

use App\Core\ApiController;
use App\Models\Session;
use App\Models\User;

class AuthApiController extends ApiController
{

  /**
   * Authentifie l'utilisateur avec un nom d'utilisateur et un mot de passe et renvoie un jeton de session.
   */
  public static function login(?array $data): void
  {
    if (!isset($data['username']) || !isset($data['password'])) {
      self::error('Bad Request', 'Username and password are required', 400);
    }

    // Vérifie que l'utilisateur existe et que le mot de passe est correct
    $user = User::findByUsernamePassword($data['username'], $data['password']);

    if (!$user) {
      self::error('Unauthorized', 'Invalid username or password', 401);
    }

    $sessionToken = Session::create($user);

    self::json(['token' => $sessionToken]);
  }

  /**
   * Déconnecte l'utilisateur en supprimant le jeton de session.
   */
  public static function logout(): void
  {
    $currentUser = self::getCurrentUser();

    if (!$currentUser) {
      self::unauthorized();
    }

    Session::delete($currentUser->id);

    self::json(['message' => 'Logged out successfully']);
  }

  /**
   * Renvoie les informations de l'utilisateur connecté.
   */
  public static function me(): void
  {
    $currentUser = self::getCurrentUser();

    if (!$currentUser) {
      self::unauthorized();
    }

    self::json($currentUser);
  }


  /**
   * Crée un compte utilisateur.
   */
  public static function register(array $data): void
  {
    // Vérifie que les données sont valides
    if (!isset($data['username']) || !isset($data['password'])) {
      self::error('Bad Request', 'Missing fields', 400);
    }
    
    // Vérifie que le mot de passe correspond au pattern
    if (!preg_match('/(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9]).{8,}/', $data['password'])) {
      self::error('Bad Request', 'Password must contain at least 8 characters including a lowercase and uppercase letter and one digit.', 400);
    }
    
    // Vérifie que l'utilisateur n'existe pas déjà
    if (User::findByUsername($data['username'])) {
      self::error('Bad Request', 'This username is already taken.', 400);
    }

    // Crée l'utilisateur
    $user = User::create($data['username'], $data['password']);
    
    $sessionToken = Session::create($user);

    self::json(['token' => $sessionToken]);
  }
}