// @ts-check

const chatbox = $("#chatbox");
const chatForm = $("#chat-form");
const messageGroups = $(".message-groups");
/**
 * L'ID de l'utilisateur connecté
 * @type {string}
 */
const currentUserId = $("#chat-content").data("user-id");
/**
 * L'ID du salon actuel
 * @type {string}
 */
const channelId = $("main").data("channel-id");

/**
 * @param {string} messageHtml
 */
function addMessage(messageHtml) {
  /**
   * L'ID de l'auteur du message à ajouter
   * @type {string}
   */
  const messageAuthorId = $(messageHtml).data("author-id");
  const isFromMe = messageAuthorId === currentUserId;

  let lastMessageGroup = messageGroups.children().last();
  let lastMessage = lastMessageGroup.children().last();

  // si le dernier message n'est pas envoyé par le même auteur que le message actuel
  // on crée un nouveau groupe et on l'ajoute aux groupes de messages
  if (lastMessage.data("author-id") !== messageAuthorId) {
    lastMessageGroup = $(
      `<div class='message-group ${isFromMe ? "me" : "not-me"}'></div>`
    );
    messageGroups.append(lastMessageGroup);
  }

  // on ajoute le message au groupe
  lastMessageGroup.append(messageHtml);

  // on anime le scroll pour descendre
  messageGroups.animate({
    scrollTop: messageGroups.prop("scrollHeight"),
  });
}

// Le fait d'utiliser un formulaire permet d'utiliser l'amélioration progressive
// Donc on peut quand même envoyer des messages sans JavaScript sur le client
chatForm.on("submit", (ev) => {
  // on empêche le formulaire de se soumettre
  ev.preventDefault();

  // on envoie le message au serveur de manière asynchrone
  $.ajax({
    url: chatForm.attr("action"),
    method: chatForm.attr("method"),
    data: {
      content: chatbox.val(),
    },
    // data est de l'HTML, on l'ajoute à la page
    success(data) {
      // on vide la zone de texte
      chatbox.val("");

      addMessage(data);
    },
  });
});

messageGroups.scrollTop(messageGroups.prop("scrollHeight"));

const FETCH_INTERVAL = 2000;

setInterval(() => {
  const lastMessageId = messageGroups
    .children()
    .last()
    .children()
    .last()
    .data("message-id");

  const fetchUrl =
    `/channels/${channelId}/messages` +
    (lastMessageId ? `?lastMessageId=${lastMessageId}` : "");

  $.ajax({
    url: fetchUrl,
    method: "GET",
    // tableau de messages en HTML
    success(
      /** @type {string[]} data */
      data
    ) {
      data.forEach((messageHtml) => {
        addMessage(messageHtml);
      });
    },
  });
}, FETCH_INTERVAL);

const copyInviteButton = $("#copy-invite");

copyInviteButton.on("click", () => {
  $.ajax({
    url: `/channels/${channelId}/invite`,
    // data est du JSON
    success(data) {
      console.log(JSON.stringify(data));
      navigator.clipboard.writeText(
        window.location.origin + "/join/" + data.token
      );

      copyInviteButton.removeClass("primary");
      copyInviteButton.addClass("success");
      copyInviteButton.html(`
<!-- Check -->
<svg width="18" height="19" viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg">
  <path d="M16.1479 5.96047L7.14792 14.9605C7.09567 15.0128 7.03364 15.0543 6.96535 15.0826C6.89706 15.1109 6.82387 15.1254 6.74995 15.1254C6.67602 15.1254 6.60283 15.1109 6.53454 15.0826C6.46626 15.0543 6.40422 15.0128 6.35198 14.9605L2.41448 11.023C2.30893 10.9174 2.24963 10.7743 2.24963 10.625C2.24963 10.4757 2.30893 10.3326 2.41448 10.227C2.52003 10.1215 2.66318 10.0622 2.81245 10.0622C2.96171 10.0622 3.10487 10.1215 3.21042 10.227L6.74995 13.7673L15.352 5.16453C15.4575 5.05898 15.6007 4.99969 15.7499 4.99969C15.8992 4.99969 16.0424 5.05898 16.1479 5.16453C16.2535 5.27008 16.3128 5.41323 16.3128 5.5625C16.3128 5.71177 16.2535 5.85492 16.1479 5.96047Z" fill="currentColor"/>
</svg>
Copié !
`);

      setTimeout(() => {
        copyInviteButton.removeClass("success");
        copyInviteButton.addClass("primary");
        copyInviteButton.html(`
<!-- Link -->
<svg width="18" height="19" viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg">
  <path d="M16.875 6.70367C16.8468 7.68288 16.4442 8.61399 15.75 9.30523L13.3067 11.75C12.9508 12.1078 12.5275 12.3915 12.0613 12.5846C11.5951 12.7777 11.0951 12.8764 10.5905 12.875H10.587C10.0737 12.8746 9.56575 12.7714 9.09306 12.5714C8.62037 12.3715 8.19254 12.0788 7.83486 11.7107C7.47719 11.3426 7.1969 10.9065 7.01058 10.4283C6.82425 9.95004 6.73566 9.4393 6.75004 8.92625C6.75423 8.77706 6.81752 8.63566 6.92597 8.53314C7.03443 8.43061 7.17917 8.37537 7.32836 8.37957C7.47754 8.38377 7.61895 8.44705 7.72147 8.55551C7.82399 8.66396 7.87923 8.80871 7.87504 8.95789C7.86479 9.32065 7.92737 9.68179 8.05906 10.02C8.19076 10.3581 8.3889 10.6665 8.64178 10.9267C8.89467 11.187 9.19716 11.394 9.53138 11.5354C9.86561 11.6768 10.2248 11.7498 10.5877 11.75C10.9444 11.7509 11.2978 11.6811 11.6274 11.5446C11.957 11.4081 12.2563 11.2076 12.5079 10.9548L14.9513 8.51141C15.4554 8.00094 15.7371 7.31177 15.7348 6.59434C15.7326 5.87692 15.4466 5.18952 14.9393 4.68222C14.432 4.17492 13.7446 3.88893 13.0272 3.88668C12.3098 3.88444 11.6206 4.16612 11.1101 4.67023L10.3367 5.44367C10.2303 5.54471 10.0887 5.6002 9.94204 5.59832C9.79536 5.59645 9.65522 5.53734 9.55149 5.43362C9.44777 5.32989 9.38867 5.18975 9.38679 5.04307C9.38491 4.89639 9.4404 4.75478 9.54144 4.64844L10.3149 3.875C10.6717 3.51804 11.0954 3.23488 11.5617 3.04169C12.028 2.8485 12.5277 2.74907 13.0325 2.74907C13.5372 2.74907 14.037 2.8485 14.5033 3.04169C14.9695 3.23488 15.3932 3.51804 15.75 3.875C16.1196 4.24552 16.41 4.68737 16.6034 5.17366C16.7968 5.65996 16.8892 6.18052 16.875 6.70367ZM7.6641 13.5542L6.89066 14.3277C6.6384 14.5816 6.33817 14.7829 6.00743 14.9197C5.67669 15.0566 5.32204 15.1264 4.9641 15.125C4.42712 15.1246 3.90232 14.965 3.45601 14.6664C3.0097 14.3678 2.6619 13.9437 2.45655 13.4475C2.25121 12.9513 2.19754 12.4055 2.30233 11.8788C2.40711 11.3521 2.66565 10.8684 3.04527 10.4886L5.48441 8.04523C5.86859 7.65903 6.36002 7.39726 6.89487 7.29394C7.42973 7.19061 7.9833 7.25049 8.48368 7.46581C8.98407 7.68113 9.40815 8.04193 9.70085 8.50136C9.99355 8.96078 10.1413 9.49761 10.125 10.0421C10.1208 10.1913 10.1761 10.336 10.2786 10.4445C10.3811 10.5529 10.5225 10.6162 10.6717 10.6204C10.8209 10.6246 10.9656 10.5694 11.0741 10.4669C11.1826 10.3643 11.2458 10.2229 11.25 10.0737C11.2635 9.55142 11.1708 9.03179 10.9774 8.54639C10.784 8.06099 10.494 7.61994 10.125 7.25C9.40446 6.52974 8.42734 6.12512 7.40851 6.12512C6.38969 6.12512 5.41256 6.52974 4.69199 7.25L2.25004 9.69336C1.71301 10.2302 1.34716 10.9141 1.19873 11.6588C1.0503 12.4034 1.12594 13.1754 1.41609 13.8771C1.70625 14.5788 2.1979 15.1787 2.82891 15.6011C3.45992 16.0234 4.20197 16.2492 4.96128 16.25C5.46604 16.2515 5.96607 16.1528 6.43242 15.9597C6.89878 15.7666 7.32219 15.4829 7.67816 15.125L8.4516 14.3516C8.54263 14.2443 8.59018 14.1069 8.58489 13.9663C8.5796 13.8258 8.52184 13.6923 8.423 13.5922C8.32416 13.4922 8.19142 13.4328 8.05094 13.4257C7.91046 13.4187 7.77244 13.4645 7.6641 13.5542Z" fill="currentColor" />
</svg>

Inviter
        `);
      }, 1000);
    },
  });
});
