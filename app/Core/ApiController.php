<?php

namespace App\Core;

use App\Models\Session;
use App\Models\User;

abstract class ApiController extends Controller
{
  /**
   * Envoie une réponse JSON contenant une erreur au client
   */
  public static function error(string $error, ?string $details, int $code = 400): void
  {
    header('Content-Type: application/json', true, $code);
    echo json_encode(['error' => $error, 'details' => $details]);
    exit;
  }

  /**
   * Envoie une réponse JSON contenant une erreur 404 au client
   */
  public static function notFound(): void
  {
    self::error('Not found', null, 404);
    exit;
  }

  /**
   * Envoie une erreur 401 Unauthorized
   */
  public static function unauthorized(): void
  {
    self::error('Unauthorized', 'You must be logged in to access this resource', 401);
    exit;
  }

  /**
   * Récupère l'utilisateur connecté à partir du jeton de session
   */
  public static function getCurrentUser(): ?User
  {
    // on récupère le jeton de session dans l'en-tête Authorization
    $authToken = self::getAuthToken();
    if (!$authToken) {
      return null;
    }
    return Session::findByToken($authToken);
  }

  /**
   * Récupère le jeton de session
   */
  public static function getAuthToken(): ?string
  {
    $headers = getallheaders();
    $authToken = $headers['Authorization'] ?? null;
    return $authToken;
  }
}