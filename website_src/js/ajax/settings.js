window.onload = function () {
    $("#settings").load("snippets/settings.php");
}

function updateSettings() {
    const createAccountsOnLogin = document.getElementById("createAccountCheckBox").checked;

    $("#settings").load("snippets/settings.php",
    {
        "anonymousAccountCreation": createAccountsOnLogin
    });
}