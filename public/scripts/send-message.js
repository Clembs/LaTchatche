// @ts-check

const chatbox = $("#chatbox");
const chatForm = $("#chat-form");
const messages = $(".messages");

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

      messages.append(data);
      // on anime le scroll pour descendre
      messages.animate({
        scrollTop: messages.prop("scrollHeight"),
      });
    },
  });
});

messages.scrollTop(messages.prop("scrollHeight"));
