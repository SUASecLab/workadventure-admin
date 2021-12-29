window.onload = function () {
    $("#login").load("/snippets/login.php", addListener);
}

function addListener() {
    const loginButton = document.getElementById("loginButton");
    if (loginButton != null) {
        loginButton.addEventListener("click", login);
    }
}

function login() {
    const username = document.getElementById("username").value;
    const password = document.getElementById("password").value;

    $("#login").load("/snippets/login.php", {
        "username": username,
        "password": password
    }, addListener);
}