window.onload = function () {
    $("#settings").load("/snippets/settings.php", addListener);
}

function addListener() {
    const updateButton = document.getElementById("updateButton");

    updateButton.addEventListener("click", updateSettings);
}

function updateSettings() {
    const createAccountsOnLogin = document.getElementById("createAccountCheckBox").checked;

    $("#settings").load("/snippets/settings.php", {
        "anonymousAccountCreation": createAccountsOnLogin
    }, addListener);
}