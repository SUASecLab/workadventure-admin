window.onload = function () {
    $("#logout").load("snippets/logout.php");
}

function adjustNavbar() {
    // login successful -> change navigation bar's login button to logout
    const loginLogoutForm = document.getElementById("navLoginLogout");
    loginLogoutForm.innerText = "Log in";
    loginLogoutForm.href = "login";
}