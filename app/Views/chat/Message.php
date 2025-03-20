<?php

/**
 * Composant de message pour le chat.
 * @var \App\Models\Message $message
 */

?>

<div class="message <?= $message->author->id === 1 ? 'me' : '' ?>">
  <!-- <?= htmlspecialchars($message->author->username) ?> -->
  <?= htmlspecialchars($message->content) ?>
  <time datetime="<?= $message->createdAt->format('Y-m-d H:i:s') ?>">
    <?php if ($message->createdAt->format('D') !== date('D')): ?>
      Le <?= $message->createdAt->format('d/m/Y') ?> à
    <?php endif ?>
    <?= $message->createdAt->format('H:i') ?>
  </time>
</div>