<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Session;
use App\Models\User;

class AuthController extends Controller
{

  /**
   * Affiche la page de connexion.
   */
  public static function loginPage(?string $error): void
  {
    // Si l'utilisateur est déjà connecté, redirige vers la page d'accueil
    if (Session::isLoggedIn()) {
      header('Location: /');
      exit;
    }

    self::render('auth/login', 'Connexion', ['error' => $error]);
  }

  /**
   * Affiche la page de création de compte.
   */
  public static function registerPage(?string $error): void
  {
    // Si l'utilisateur est déjà connecté, redirige vers la page d'accueil
    if (Session::isLoggedIn()) {
      header('Location: /');
      exit;
    }

    self::render('auth/register', 'Inscription', ['error' => $error]);
  }

  /**
   * Connecte l'utilisateur.
   */
  public static function login(array $data): void
  {
    // Si l'utilisateur est déjà connecté, redirige vers la page d'accueil
    if (Session::isLoggedIn()) {
      header('Location: /');
      exit;
    }

    // Vérifie que les données sont valides
    if (!isset($data['username']) || !isset($data['password'])) {
      header('Location: /login?error=Veuillez remplir tous les champs.');
      return;
    }

    // Vérifie que l'utilisateur existe
    $user = User::findByUsernamePassword($data['username'], $data['password']);

    if (!$user) {
      header('Location: /login?error=Identifiant ou mot de passe incorrect.');
      return;
    }

    // Connecte l'utilisateur
    Session::create($user);

    header('Location: /');
  }

  /**
   * Crée un compte utilisateur.
   */
  public static function register(array $data): void
  {
    // Si l'utilisateur est déjà connecté, redirige vers la page d'accueil
    if (Session::isLoggedIn()) {
      header('Location: /');
      exit;
    }

    // Vérifie que les données sont valides
    if (!isset($data['username']) || !isset($data['password'])) {
      header('Location: /register?error=Veuillez remplir tous les champs.');
      return;
    }

    // Vérifie que le mot de passe correspond au pattern
    if (!preg_match('/(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9]).{8,}/', $data['password'])) {
      header('Location: /register?error=Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule et un chiffre.');
      return;
    }

    // Vérifie que l'utilisateur n'existe pas déjà
    if (User::findByUsername($data['username'])) {
      header('Location: /register?error=Cet utilisateur existe déjà.');
      return;
    }

    // Crée l'utilisateur
    $user = User::create($data['username'], $data['password']);

    // Connecte l'utilisateur
    Session::create($user);

    header('Location: /');
  }
}
