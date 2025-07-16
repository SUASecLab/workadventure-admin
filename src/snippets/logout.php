<?php
session_start();
?>
<!doctype html>
<html lang="en">

<body>
  <?php
require_once ('../util/database_operations.php');
require_once ('../util/web_login_functions.php');
// Connect to database
$DB = getDatabaseHandleOrPrintError();
if (!isLoggedIn()) {
?>
    <main class="alert alert-danger" role="alert">
      You have not been logged in!
    </main>
  <?php
} else {
    // Check CSRF data
    isCSRFDataValidOrDie();

    // Check token
    if (!isTokenValid($_POST["token"])) {
      die("Invalid data received");
    }

    // Delete cookie
    setcookie("csrf_cookie", $_SESSION["csrf_token"] /* @phpstan-ignore argument.type */, ["samesite" => "Strict", "expires" => time() - 300]);

    // Delete session
    session_unset();
    session_destroy();
?>
    <main class="alert alert-success" role="alert">
      You have been logged out.
    </main>
    <script>
      // change nav bar to logout
      adjustNavbar();
    </script>
  <?php
}
?>
</body>

</html>

<?php $DB = NULL; ?>