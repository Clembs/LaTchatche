<?php ob_start(); ?>

<div class="header">
  <h1>Se connecter à La Tchatche</h1>

  <p>Connectez-vous pour discuter avec vos amis.</p>
</div>


<form action="/login" method="post">
  <label for="username" class="input full">
    <div class="label">
      Nom d'utilisateur
    </div>
    <input type="text" id="username" name="username" required>
  </label>

  <label for="password" class="input full">
    <div class="label">
      Mot de passe
    </div>
    <input type="password" id="password" name="password" required>
  </label>


  <button class="button primary" type="submit">
    Se connecter
  </button>
</form>

<p class="alternative">
  Pas de compte ? <a href="/register">Inscrivez-vous</a> !
</p>

<?php if (isset($error)): ?>
  <p class="error"><?= $error ?></p>
<?php endif; ?>

<?php $slot = ob_get_clean(); ?>