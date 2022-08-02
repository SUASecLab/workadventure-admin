<?php
require_once ('database_operations.php');

function isLoggedIn() {
    GLOBAL $DB;

    if (!isset($_SESSION["user"])) {
        return false;
    }
    $user = htmlspecialchars($_SESSION["user"]);
    if (!isset($_SESSION["password"])) {
        return false;
    }
    
    // the password is already stored as hash
    $password = htmlspecialchars($_SESSION["password"]);

    return websiteUserExists($user, $password);
}
function showLogin() {
    echo "<aside class=\"container alert alert-danger\" role=\"alert\">";
    echo "You are not logged in. Please log in to continue.";
    echo "</aside>";
    echo "<a href=\"login\" class=\"element btn btn-primary\">Log in</a>";
    echo "</body></html>";
}
function getHash($data) {
    $salt = getenv('ADMIN_API_SALT');
    $saltWithAlgorithm = '$6$rounds=5000$' . $salt . '$';
    return htmlspecialchars(crypt($data, $saltWithAlgorithm));
}
function login($user, $password) {
    GLOBAL $DB;
    $passwordHash = getHash($password);
    $_SESSION["user"] = $user;
    $_SESSION["password"] = $passwordHash;
    if (websiteUserExists($user, $passwordHash)) {
        return true;
    } else {
        if (getNumberOfWebsiteUsers() == 0) {
            return createWebsiteUser($user, $passwordHash);
        }
    }
    return false;
}
?>