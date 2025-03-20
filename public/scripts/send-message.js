// @ts-check

const chatbox = $("#chatbox");
const chatForm = $("#chat-form");
const messageGroups = $(".message-groups");

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

      let lastMessageGroup = messageGroups.children().last();
      if (lastMessageGroup.hasClass("me")) {
        lastMessageGroup.append(data);
      } else {
        lastMessageGroup = $("<div class='message-group me'></div>");
        lastMessageGroup.append(data);
        messageGroups.append(lastMessageGroup);
      }

      // on anime le scroll pour descendre
      messageGroups.animate({
        scrollTop: messageGroups.prop("scrollHeight"),
      });
    },
  });
});

messageGroups.scrollTop(messageGroups.prop("scrollHeight"));
