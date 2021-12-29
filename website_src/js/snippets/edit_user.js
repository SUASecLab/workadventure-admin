window.onload = function () {
    const uuid = window.location.href.split("/").pop();

    $("#edit_user").load("/snippets/edit_user.php", {
        "uuid": uuid
    });
}

function updateUserData(uuid) {
    const name = document.getElementById("name").value;
    const email = document.getElementById("email").value;
    const visitCardUrl = document.getElementById("visitCardUrlLabel").value;

    $("#edit_user").load("/snippets/edit_user.php", {
        "action": "updateUserData",
        "uuid": uuid,
        "name": name,
        "email": email,
        "visitCardUrl": visitCardUrl
    });
}

function removeTag(uuid, tag) {
    $("#edit_user").load("/snippets/edit_user.php", {
        "action": "removeTag",
        "uuid": uuid,
        "tag": tag
    });
}

function addTag(uuid) {
    const tag = document.getElementById("newTag").value;

    $("#edit_user").load("/snippets/edit_user.php", {
        "action": "addTag",
        "uuid": uuid,
        "tag": tag
    });
}

function removeMessage(uuid, messageId) {
    $("#edit_user").load("/snippets/edit_user.php", {
        "action": "removeMessage",
        "uuid": uuid,
        "messageId": messageId
    });
}

function sendMessage(uuid) {
    const message = document.getElementById("messageInput").value;

    $("#edit_user").load("/snippets/edit_user.php", {
        "action": "sendMessage",
        "uuid": uuid,
        "message": message
    });
}

function unban(uuid) {
    $("#edit_user").load("/snippets/edit_user.php", {
        "action": "unban",
        "uuid": uuid
    });
}

function ban(uuid) {
    const reason = document.getElementById("banReason").value;

    $("#edit_user").load("/snippets/edit_user.php", {
        "action": "ban",
        "uuid": uuid,
        "reason": reason
    });
}