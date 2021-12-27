window.onload = function () {
    $("#login").load("snippets/login.php");
}

function login() {
    const username = document.getElementById("username").value;
    const password = document.getElementById("password").value;
    
    $("#login").load("snippets/login.php",
        {
            "username": username,
            "password": password
        });
}