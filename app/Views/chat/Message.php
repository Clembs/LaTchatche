<?php

/**
 * Composant de message pour le chat.
 * @var \App\Models\Message $message
 * @var \App\Models\User $currentUser
 */

?>

<div class="message" data-message-id="<?= $message->id ?>" data-author-id="<?= $message->author->id ?>">

  <?php if ($message->author->id !== $currentUser->id): ?>
    <div class="message-author">
      <?= htmlspecialchars($message->author->username) ?>
    </div>
  <?php endif ?>

  <div class="message-content">
    <?= htmlspecialchars($message->content) ?>
    <time datetime="<?= $message->createdAt->format('Y-m-d H:i:s') ?>">
      <?php if ($message->createdAt->format('D') !== date('D')): ?>
        Le <?= $message->createdAt->format('d/m/Y') ?> à
      <?php endif ?>
      <?= $message->createdAt->format('H:i') ?>
    </time>
  </div>

</div>