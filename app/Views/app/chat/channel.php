<?php

use App\Models\ChannelType;

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
  <div class="channel-header">
    <div class="channel-info">
      <div class="channel-name">
        <div class="icon">
          <?php include __DIR__ . '/../../../Icons/Hashtag.php' ?>
        </div>
        <h2>
          <?= $channel->name ?>
        </h2>
      </div>

      <p>
        <?= $channel->memberCount ?> membre<?= $channel->memberCount > 1 ? 's' : '' ?>
        •
        Opéré par <?= $channel->ownerId === $currentUser->id ? 'vous' : $channel->ownerUsername ?>
      </p>
    </div>

    <?php if ($channel->type === ChannelType::public || $currentUser->id === $channel->ownerId): ?>
      <button id="copy-invite" class="button primary">
        <!-- Link -->
        <svg width="18" height="19" viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path
            d="M16.875 6.70367C16.8468 7.68288 16.4442 8.61399 15.75 9.30523L13.3067 11.75C12.9508 12.1078 12.5275 12.3915 12.0613 12.5846C11.5951 12.7777 11.0951 12.8764 10.5905 12.875H10.587C10.0737 12.8746 9.56575 12.7714 9.09306 12.5714C8.62037 12.3715 8.19254 12.0788 7.83486 11.7107C7.47719 11.3426 7.1969 10.9065 7.01058 10.4283C6.82425 9.95004 6.73566 9.4393 6.75004 8.92625C6.75423 8.77706 6.81752 8.63566 6.92597 8.53314C7.03443 8.43061 7.17917 8.37537 7.32836 8.37957C7.47754 8.38377 7.61895 8.44705 7.72147 8.55551C7.82399 8.66396 7.87923 8.80871 7.87504 8.95789C7.86479 9.32065 7.92737 9.68179 8.05906 10.02C8.19076 10.3581 8.3889 10.6665 8.64178 10.9267C8.89467 11.187 9.19716 11.394 9.53138 11.5354C9.86561 11.6768 10.2248 11.7498 10.5877 11.75C10.9444 11.7509 11.2978 11.6811 11.6274 11.5446C11.957 11.4081 12.2563 11.2076 12.5079 10.9548L14.9513 8.51141C15.4554 8.00094 15.7371 7.31177 15.7348 6.59434C15.7326 5.87692 15.4466 5.18952 14.9393 4.68222C14.432 4.17492 13.7446 3.88893 13.0272 3.88668C12.3098 3.88444 11.6206 4.16612 11.1101 4.67023L10.3367 5.44367C10.2303 5.54471 10.0887 5.6002 9.94204 5.59832C9.79536 5.59645 9.65522 5.53734 9.55149 5.43362C9.44777 5.32989 9.38867 5.18975 9.38679 5.04307C9.38491 4.89639 9.4404 4.75478 9.54144 4.64844L10.3149 3.875C10.6717 3.51804 11.0954 3.23488 11.5617 3.04169C12.028 2.8485 12.5277 2.74907 13.0325 2.74907C13.5372 2.74907 14.037 2.8485 14.5033 3.04169C14.9695 3.23488 15.3932 3.51804 15.75 3.875C16.1196 4.24552 16.41 4.68737 16.6034 5.17366C16.7968 5.65996 16.8892 6.18052 16.875 6.70367ZM7.6641 13.5542L6.89066 14.3277C6.6384 14.5816 6.33817 14.7829 6.00743 14.9197C5.67669 15.0566 5.32204 15.1264 4.9641 15.125C4.42712 15.1246 3.90232 14.965 3.45601 14.6664C3.0097 14.3678 2.6619 13.9437 2.45655 13.4475C2.25121 12.9513 2.19754 12.4055 2.30233 11.8788C2.40711 11.3521 2.66565 10.8684 3.04527 10.4886L5.48441 8.04523C5.86859 7.65903 6.36002 7.39726 6.89487 7.29394C7.42973 7.19061 7.9833 7.25049 8.48368 7.46581C8.98407 7.68113 9.40815 8.04193 9.70085 8.50136C9.99355 8.96078 10.1413 9.49761 10.125 10.0421C10.1208 10.1913 10.1761 10.336 10.2786 10.4445C10.3811 10.5529 10.5225 10.6162 10.6717 10.6204C10.8209 10.6246 10.9656 10.5694 11.0741 10.4669C11.1826 10.3643 11.2458 10.2229 11.25 10.0737C11.2635 9.55142 11.1708 9.03179 10.9774 8.54639C10.784 8.06099 10.494 7.61994 10.125 7.25C9.40446 6.52974 8.42734 6.12512 7.40851 6.12512C6.38969 6.12512 5.41256 6.52974 4.69199 7.25L2.25004 9.69336C1.71301 10.2302 1.34716 10.9141 1.19873 11.6588C1.0503 12.4034 1.12594 13.1754 1.41609 13.8771C1.70625 14.5788 2.1979 15.1787 2.82891 15.6011C3.45992 16.0234 4.20197 16.2492 4.96128 16.25C5.46604 16.2515 5.96607 16.1528 6.43242 15.9597C6.89878 15.7666 7.32219 15.4829 7.67816 15.125L8.4516 14.3516C8.54263 14.2443 8.59018 14.1069 8.58489 13.9663C8.5796 13.8258 8.52184 13.6923 8.423 13.5922C8.32416 13.4922 8.19142 13.4328 8.05094 13.4257C7.91046 13.4187 7.77244 13.4645 7.6641 13.5542Z"
            fill="currentColor" />
        </svg>

        Inviter
      </button>
    <?php endif ?>
  </div>

  <div class="message-groups">
    <?php foreach ($groupedMessages as $messages): ?>
      <div class="message-group <?= $messages[0]->author->id === $currentUser->id ? 'me' : 'not-me' ?>">
        <?php foreach ($messages as $index => $message) {
          include '../../components/Message.php';
        } ?>
      </div>
    <?php endforeach; ?>
  </div>

  <form id="chat-form" method="post" action="/chats/<?= $channel->id ?>/send-message">
    <input id="chatbox" type="text" name="content" minlength="1" maxlength="255"
      placeholder="Envoyer un message sur #<?= $channel->name ?>" required />
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

  main .channel-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem;
    background-color: var(--color-surface);
    border-bottom: 1px solid var(--color-outline);
  }

  .channel-info {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
  }

  .channel-name {
    display: flex;
    gap: 0.75rem;
    align-items: center;
  }

  .channel-name h2 {
    font-size: 1.25rem;
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