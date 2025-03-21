<?php

/**
 * @var \App\Models\Channel $channel
 * @var \App\Models\Message[] $messages
 * @var \App\Models\User $currentUser
 */

// les messages sont regroupés par auteur lorsqu'ils se suivent
// pour les afficher dans des bulles de message groupées
$groupedMessages = [];

foreach ($messages as $message) {
  if (count($groupedMessages) === 0 || $groupedMessages[count($groupedMessages) - 1][0]->author->id !== $message->author->id) {
    $groupedMessages[] = [$message];
  } else {
    $groupedMessages[count($groupedMessages) - 1][] = $message;
  }
}

?>

<?php ob_start(); ?>

<main data-channel-id="<?= $channel->id ?>">
  <div class="channel-info">
    <h2>#<?= $channel->name ?></h2>
  </div>

  <div class="message-groups">
    <?php foreach ($groupedMessages as $messages): ?>
      <div class="message-group <?= $messages[0]->author->id === $currentUser->id ? 'me' : 'not-me' ?>">
        <?php foreach ($messages as $index => $message) {
          include 'Message.php';
        } ?>
      </div>
    <?php endforeach; ?>
  </div>

  <form id="chat-form" method="post" action="/chats/<?= $channel->id ?>/send-message">
    <input id="chatbox" type="text" name="content" minlength="1" maxlength="255"
      placeholder="Envoyer un message sur #<?= $channel->name ?>" autofocus required />
  </form>
</main>

<script src="/scripts/channel-chats.js"></script>

<?php $slot = ob_get_clean(); ?>

<?php ob_start(); ?>

<style data-file="chat/channel">
  main {
    height: 100dvh;
    display: flex;
    flex-direction: column;
  }

  main .channel-info {
    padding: 0.75rem 1rem;
    background-color: var(--color-surface);
    border-bottom: 1px solid var(--color-outline);
  }

  .message-groups {
    padding: 1rem 1rem 0 1rem;
    flex: 1;
    overflow-y: scroll;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
  }

  .message-group {
    display: flex;
    flex-direction: column;
    gap: 0.125rem;
  }

  .message-group.me {
    align-items: flex-end;
  }

  .message {
    display: flex;
    flex-direction: column;
    width: fit-content;
    padding: 0.5rem 0.75rem;
    gap: 0.5rem;
    max-width: 50svw;
    overflow-wrap: break-word;
    word-break: break-all;
  }

  .message-content {
    display: flex;
    align-items: flex-end;
    gap: 0.5rem;
  }

  .not-me .message {
    background-color: var(--color-surface);
    color: var(--color-on-surface);
    border-top-left-radius: 0.5rem;
    border-bottom-left-radius: 0.5rem;
    border-top-right-radius: 1.5rem;
    border-bottom-right-radius: 1.5rem;
  }

  .message-author {
    display: none;
  }

  .not-me .message:first-child .message-author {
    display: block;
  }

  .not-me .message:first-child {
    border-top-left-radius: 1.5rem;
  }

  .not-me .message:last-child {
    border-bottom-left-radius: 1.5rem;
  }

  .me .message {
    background-color: var(--color-primary);
    color: var(--color-on-primary);
    border-top-left-radius: 1.5rem;
    border-bottom-left-radius: 1.5rem;
    border-top-right-radius: 0.5rem;
    border-bottom-right-radius: 0.5rem;
  }

  .me .message:first-child {
    border-top-right-radius: 1.5rem;
  }

  .me .message:last-child {
    border-bottom-right-radius: 1.5rem;
  }

  .message-author,
  .message time {
    user-select: none;
    white-space: nowrap;
  }

  .me .message time {
    color: var(--color-primary-light);
  }

  .not-me .message time {
    color: var(--color-on-surface-light);
  }

  .message time {
    font-size: 0.75rem;
  }

  .message-author {
    font-weight: 500;
    font-size: 0.9rem;
  }

  #chat-form {
    display: flex;
    padding: 0.75rem 1rem;
  }

  #chatbox {
    width: 100%;
    border: none;
    border-radius: 99rem;
    padding: 0.75rem 1rem;
    background-color: var(--color-surface);
    color: var(--color-on-surface);
  }
</style>

<?php $head = !isset($head) ? ob_get_clean() : $head . ob_get_clean(); ?>