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
      <?php if ((int) $message->createdAt->format('d') === (int) date('d') - 1): ?>
        Hier à
      <?php elseif ($message->createdAt->format('D') === date('D')): ?>
        Aujourd'hui à
      <?php else: ?>
        Le <?= $message->createdAt->format('d/m') ?>
      <?php endif ?>
      <?= $message->createdAt->format('H:i') ?>
    </time>
  </div>

</div>