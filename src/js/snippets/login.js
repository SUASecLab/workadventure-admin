window.onload = function () {
    $("#login").load("snippets/login.php", addListener);
}

function addListener() {
    const loginButton = document.getElementById("loginButton");
    if (loginButton !== null) {
        loginButton.addEventListener("click", login);
    }
}

function login() {
    const username = document.getElementById("username").value;
    const password = document.getElementById("password").value;
    const csrf_token = document.getElementById("csrf_token").value;

    $("#login").load("snippets/login.php", {
        "username": username,
        "password": password,
        "csrf_token": csrf_token,
    }, function() {
        addListener();

        const nonce = document.getElementById("nonce").value;
        const adminButton = document.getElementById("goToAdminButton");
        if (adminButton !== null) {
            // login successful -> change navigation bar's login button to logout
            const loginLogoutForm = document.getElementById("navLoginLogout");
            loginLogoutForm.innerText = "Log out";
            loginLogoutForm.href = "logout?token=" + nonce;
        }
    });
}