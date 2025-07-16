window.onload = function () {
    const uuid = window.location.href.split("/").pop().split("?")[0];

    $("#edit_user").load("../snippets/edit_user.php", {
        "uuid": uuid,
        "create": new URLSearchParams(window.location.search).get("create"),
        "token": new URLSearchParams(window.location.search).get("token")
    }, function() {
        const navbarMain = document.getElementById("navbarMain");

        const divider = document.createElement('a')
        navbarMain.appendChild(divider);
        divider.outerHTML = '<a class="navbar-brand" href="#">/</a>';

        const users = document.createElement('a');
        navbarMain.appendChild(users);
        users.outerHTML = '<a class="navbar-brand" href="../user">Users</a>';
    });
}

function updateUserData(uuid, csrf_token) {
    const name = document.getElementById("name").value;
    const email = document.getElementById("email").value;
    const visitCardUrl = document.getElementById("visitCardUrlLabel").value;

    $("#edit_user").load("../snippets/edit_user.php", {
        "action": "updateUserData",
        "uuid": uuid,
        "name": name,
        "email": email,
        "visitCardUrl": visitCardUrl,
        "csrf_token": csrf_token
    });
}

function removeTag(uuid, tag, csrf_token) {
    $("#edit_user").load("../snippets/edit_user.php", {
        "action": "removeTag",
        "uuid": uuid,
        "tag": tag,
        "csrf_token": csrf_token
    });
}

function addTag(uuid, csrf_token) {
    const tag = document.getElementById("newTag").value;

    $("#edit_user").load("../snippets/edit_user.php", {
        "action": "addTag",
        "uuid": uuid,
        "tag": tag,
        "csrf_token": csrf_token
    });
}

function updateMapSelect(map) {
    const dropdown = document.getElementById("mapsDropdown");
    dropdown.innerHTML = map;
}

function updateStartMap(uuid, csrf_token) {
    const dropdown = document.getElementById("mapsDropdown");
    startMap = dropdown.innerHTML;

    $("#edit_user").load("../snippets/edit_user.php", {
        "action": "updateStartMap",
        "uuid": uuid,
        "startMap": startMap,
        "csrf_token": csrf_token
    });
}

function removeMessage(uuid, message, csrf_token) {
    $("#edit_user").load("../snippets/edit_user.php", {
        "action": "removeMessage",
        "uuid": uuid,
        "message": message,
        "csrf_token": csrf_token
    });
}

function sendMessage(uuid, csrf_token) {
    const message = document.getElementById("messageInput").value;

    $("#edit_user").load("../snippets/edit_user.php", {
        "action": "sendMessage",
        "uuid": uuid,
        "message": message,
        "csrf_token": csrf_token
    });
}

function unban(uuid, csrf_token) {
    $("#edit_user").load("../snippets/edit_user.php", {
        "action": "unban",
        "uuid": uuid,
        "csrf_token": csrf_token
    });
}

function ban(uuid, csrf_token) {
    const reason = document.getElementById("banReason").value;

    $("#edit_user").load("../snippets/edit_user.php", {
        "action": "ban",
        "uuid": uuid,
        "reason": reason,
        "csrf_token": csrf_token
    });
}