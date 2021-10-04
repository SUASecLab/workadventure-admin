var alertBox;
var UUID;


//TODO: add button functionality for messages
window.onload = function () {
    alertBox = document.getElementById("alert");
    document.getElementById("buttonUpdateMainInformation").addEventListener("click", updateMainInformation);
    document.getElementById("buttonTag").addEventListener("click", addTag);
    document.getElementById("sendMessage").addEventListener("click", addMessage);
    document.getElementById("banButton").addEventListener("click", ban);

    UUID = document.getElementById("uuid").value;

    for (let button of document.getElementsByName("removeTag")) {
        button.addEventListener("click", function () {
            removeTag(button);
        });
    };

    updateUserInformation();
}

function updateUserInformation() {
    $.get("ajax_user.php?userId=" + UUID, function (data, status) {
        document.getElementById("username").value = data.userData.name;
        document.getElementById("email").value = data.userData.email;
        if (data.userData.visitCardUrl) {
            document.getElementById("visitCardUrlLabel").value = data.userData.visitCardUrl;
        } else {
            document.getElementById("visitCardUrlLabel").value = "";
        }

        //clear tags section, add tags
        var tagsSection = document.getElementById("tagsSection");
        tagsSection.innerHTML = "";
        var tagsParagraph = document.createElement('p');

        if (data.tags.length > 0) {
            tagsParagraph.innerText = "Tags (click to remove):";
            tagsSection.appendChild(tagsParagraph);
            data.tags.forEach(tag => {
                var form = document.createElement('form');
                form.action = "javascript:void(0)";
                form.method = "post";
                form.classList.add("sameline-form");

                var tagInput = document.createElement('input');
                tagInput.classList.add("tag", "btn", "btn-primary");
                tagInput.type = "submit";
                tagInput.value = tag;
                tagInput.name = "removeTag";
                tagInput.addEventListener("click", function () {
                    removeTag(tagInput);
                });

                var uuidInput = document.createElement('input');
                uuidInput.type = "hidden";
                uuidInput.name = "uuid";
                uuidInput.value = data.userData.uuid;

                form.appendChild(tagInput);
                form.appendChild(uuidInput);
                tagsSection.appendChild(form);
            });
        } else {
            tagsParagraph.innerText = "Tags:";
            tagsSection.appendChild(tagsParagraph);
        }

        //clear messages section, add existing messages
        var messagesSection = document.getElementById("messagesSection");
        messagesSection.innerHTML = "";
        $("#messagesSection").hide();
        if (data.messages.length > 0) {
            var warningDiv = document.createElement('div');
            var messagesHeader = document.createElement('p');

            messagesSection.appendChild(warningDiv);
            messagesSection.appendChild(messagesHeader);

            messagesHeader.outerHTML = '<p class="fs-3">User messages:</p>';
            warningDiv.outerHTML = '<div class="container alert alert-warning" role="alert"><p>Only the top message will be shown to the user. If the user also receives a global message, the global message will be shown instead of the private one!</p></div>';

            var messagesTable = document.createElement('table');
            messagesSection.appendChild(messagesTable);
            messagesTable.outerHTML = '<table class="table"><thead><tr><th>Message</th><th>Action</th></tr></thead><tbody id="tableBody"></tbody></table>';

            var tableBody = document.getElementById("tableBody");
            data.messages.forEach(message => {
                var tableRow = document.createElement('tr');
                var messageColumn = document.createElement('td');
                var messageParagraph = document.createElement('p');

                messageParagraph.classList.add("fw-normal");
                messageParagraph.innerText = message.text;
                messageColumn.appendChild(messageParagraph);
                tableRow.appendChild(messageColumn);

                var buttonColumn = document.createElement('td');
                var removeButton = document.createElement('button');
                removeButton.classList.add("tag", "btn", "btn-danger");
                removeButton.innerText = "Remove";
                removeButton.addEventListener("click", function () {
                    removeMessage(message.id);
                });
                buttonColumn.appendChild(removeButton);
                tableRow.appendChild(buttonColumn);

                tableBody.appendChild(tableRow);
            });
        }
        $("#messagesSection").show(750, "swing");

        var banButton = document.getElementById("banButton");
        if (data.banned) {
            document.getElementById("banHeader").innerText = "This user has been banned!";
            var banReason = document.getElementById("banReason");
            banReason.value = data.banReason;
            banReason.disabled = true;
            banButton.value = "Lift ban";
        } else {
            document.getElementById("banHeader").innerText = "Ban this user:";
            var banReason = document.getElementById("banReason");
            banReason.value = "";
            banReason.disabled = false;
            banButton.value = "Ban";
        }
    });
}

function setAlertBoxText(text, error) {
    alertBox.classList.remove("alert-danger");
    alertBox.classList.remove("alert-success");
    if (error) {
        alertBox.classList.add("alert-danger");
    } else {
        alertBox.classList.add("alert-success");
    }
    alertBox.innerHTML = "<p>" + text + "</p>";
}

function updateMainInformation() {
    var name = document.getElementById("username").value;
    var email = document.getElementById("email").value;
    var visitCardUrl = document.getElementById("visitCardUrlLabel").value;
    $.get("ajax_user.php?userId=" + UUID +
        "&name=" + encodeURI(name) +
        "&email=" + encodeURI(email) +
        "&visitCardUrl=" + encodeURI(visitCardUrl), function (data, status) {
            if (data.success) {
                setAlertBoxText(data.message, false);
            } else {
                setAlertBoxText(data.message, true);
            }
        });
    updateUserInformation();
}

function addTag() {
    var tag = document.getElementById("tagInput").value;
    $.get("ajax_user.php?userId=" + UUID +
        "&addTag=" + encodeURI(tag), function (data, status) {
            if (data.success) {
                setAlertBoxText(data.message, false);
                document.getElementById("tagInput").value = "";
            } else {
                setAlertBoxText(data.message, true);
            }
        });
    updateUserInformation();
}

function removeTag(button) {
    var tag = button.value;
    $.get("ajax_user.php?userId=" + UUID +
        "&removeTag=" + encodeURI(tag), function (data, status) {
            if (data.success) {
                setAlertBoxText(data.message, false);
            } else {
                setAlertBoxText(data.message, true);
            }
        });
    updateUserInformation();
}

function ban() {
    var reason = document.getElementById("banReason").value;
    $.get("ajax_user.php?userId=" + UUID +
        "&banReason=" + encodeURI(reason), function (data, status) {
            if (data.success) {
                setAlertBoxText(data.message, false);
            } else {
                setAlertBoxText(data.message, true);
            }
        });
    updateUserInformation();
}

function addMessage() {
    var messageInput = document.getElementById("messageInput");
    var message = messageInput.value;
    $.get("ajax_user.php?userId=" + UUID +
        "&storeMessage=" + encodeURI(message), function (data, status) {
            if (data.success) {
                setAlertBoxText(data.message, false);
                messageInput.value = "";
            } else {
                setAlertBoxText(data.message, true);
            }
        });
    updateUserInformation();
}

function removeMessage(messageId) {
    $.get("ajax_user.php?userId=" + UUID +
        "&removeMessage=" + encodeURI(messageId), function (data, status) {
            if (data.success) {
                setAlertBoxText(data.message, false);
            } else {
                setAlertBoxText(data.message, true);
            }
        });
    updateUserInformation();
}