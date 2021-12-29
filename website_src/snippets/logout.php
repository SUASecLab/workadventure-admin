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
    session_unset();
    session_destroy();
?>
    <main class="alert alert-success" role="alert">
      You have been logged out.
    </main>
  <?php
}
?>
</body>

</html>

<?php $DB = NULL; ?>