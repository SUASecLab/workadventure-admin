<?php
require_once ('database_operations.php');

function isLoggedIn(): bool {
    GLOBAL $DB;

    if (!isset($_SESSION["user"])) {
        return false;
    }

    // Get user name
    $user = $_SESSION["user"];
    if (gettype($user) !== "string") {
        return false;
    }
    $user = htmlspecialchars($user);
    if (!isset($_SESSION["password"])) {
        return false;
    }
    
    // the password is already stored as hash
    $password = htmlspecialchars($_SESSION["password"]);

    return websiteUserExists($user, $password);
}
function showLogin(bool $webroot=true): void {
    echo "<aside class=\"container alert alert-danger\" role=\"alert\">";
    echo "You are not logged in. Please log in to continue.";
    echo "</aside>";
    if ($webroot == true) {
        echo "<a href=\"login\" class=\"element btn btn-primary\">Log in</a>";
    } else {
        echo "<a href=\"../login\" class=\"element btn btn-primary\">Log in</a>";
    }
    echo "</body></html>";
}
function getHash(string $data): string {
    $salt = getenv('ADMIN_API_SALT');
    $saltWithAlgorithm = '$6$rounds=5000$' . $salt . '$';
    return htmlspecialchars(crypt($data, $saltWithAlgorithm));
}
function login(string $user, string $password): bool {
    GLOBAL $DB;
    $passwordHash = getHash($password);
    $_SESSION["user"] = $user;
    $_SESSION["password"] = $passwordHash;
    if (websiteUserExists($user, $passwordHash)) {
        return true;
    } else {
        if (getNumberOfWebsiteUsers() === 0) {
            return createWebsiteUser($user, $passwordHash);
        }
    }
    return false;
}
function isCSRFDataValidOrDie(): void {
    if (!isset($_COOKIE["csrf_cookie"])) {#
        session_unset();
        session_destroy();
        die("Your session expired");
    }
    if ((!isset($_SESSION["csrf_token"])) || ($_SESSION["csrf_token"] !== $_COOKIE["csrf_cookie"])) {
        die("Invalid request");
    }
}
?>
