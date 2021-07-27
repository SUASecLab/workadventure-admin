<?php
function isLoggedIn() {
    if (!isset($_SESSION["user"])) {
        return false;
    }
    $user = $_SESSION["user"];
    if (!isset($_SESSION["password"])) {
        return false;
    }
    $password = $_SESSION["password"];
    if (!websiteUserValid($user, $password)) {
        return false;
    }
    return true;
}

function loginValid($user, $password) {
    GLOBAL $DB;
    // check whether stored session data is valid
    return true;
}

function showLogin() {
    echo "<div class=\"container alert alert-danger\" role=\"alert\">";
    echo "You are not logged in. Please log in to continue.";
    echo "</div>";
    echo "<a href=\"login.php\" class=\"element btn btn-primary\">Log in</a>";
}

function getHash($data) {
    $salt = getenv('ADMIN_API_SALT');
    $saltWithAlgorithm = '$6$rounds=5000$'.$salt.'$';
    return crypt($data, $saltWithAlgorithm);
}

function login($user, $password) {
    GLOBAL $DB;
    $passwordHash = getHash($password);
    $_SESSION["user"] = $user;
    $_SESSION["password"] = $passwordHash;
    
    if (websiteUserExists()) {
        return websiteUserValid($user, $passwordHash);
    } else {
        return createWebsiteUser($user, $passwordHash);
    }    
    return false;
}

?>
