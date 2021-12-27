window.onload = function () {
    const uuid = document.getElementById("userId").innerText.trim();

    $("#user").load("snippets/edit_user.php",
        {
            "uuid" : uuid
        });
}

function updateUserData(uuid) {
    const name = document.getElementById("name").value;
    const email = document.getElementById("email").value;
    const visitCardUrl = document.getElementById("visitCardUrlLabel").value;

    $("#user").load("snippets/edit_user.php",
        {
            "uuid": uuid,
            "name": name,
            "email": email,
            "visitCardUrl": visitCardUrl
        });
}

function removeTag(uuid, tag) {
    $("#user").load("snippets/edit_user.php",
        {
            "uuid": uuid,
            "remtag": tag
        });
}

function addTag(uuid) {
    const tag = document.getElementById("newtag").value;

    $("#user").load("snippets/edit_user.php",
        {
            "uuid": uuid,
            "newtag": tag
        });
}

function removeMessage(uuid, messageId) {
    $("#user").load("snippets/edit_user.php",
        {
            "uuid": uuid,
            "removemessage": "1",
            "message_id" : messageId
        });
}

function sendMessage(uuid) {
    const message = document.getElementById("messageInput").value;

    $("#user").load("snippets/edit_user.php",
        {
            "uuid": uuid,
            "sendmessage": "1",
            "message": message
        });
}

function unban(uuid) {
    $("#user").load("snippets/edit_user.php",
        {
            "uuid": uuid,
            "ban": "false"
        });
}

function ban(uuid) {
    const  reason = document.getElementById("banReason").value;
    
    $("#user").load("snippets/edit_user.php",
    {
        "uuid": uuid,
        "ban": "true",
        "message": reason
    });
}