<?php
session_start();
?>
<!doctype html>
<html lang="en">

<body>
  <?php
require_once ('../util/database_operations.php');
require_once ('../util/web_login_functions.php');
function showGoToAdmin() {
  echo "<br>";
  echo "<a class=\"btn btn-primary\" href=\"./\" role=\"button\" id=\"goToAdminButton\">Go to the administration panel</a>";
}
// Connect to database
$DB = getDatabaseHandleOrPrintError();
$triedToLogin = false;
$validLogin = false;
$alreadyLoggedIn = isLoggedIn();
if ((isset($_POST["username"])) && (isset($_POST["password"]))) {
    $username = htmlspecialchars($_POST["username"]);
    $password = htmlspecialchars($_POST["password"]);
    $validLogin = login($username, $password);
    $triedToLogin = true;
} else {
    $validLogin = false;
}
if ($alreadyLoggedIn) { ?>
<section class="alert alert-warning" role="alert">
  You are already logged in
</section>
<?php
showGoToAdmin();
} else if ($validLogin) {
?>
  <section class="alert alert-success" role="alert">
    Welcome back, <?php echo htmlspecialchars($_POST["username"]); ?>
  </section>
  <?php
  showGoToAdmin();
} else {
    if ($triedToLogin) {
?>
      <aside class="alert alert-danger" role="alert">
        Invalid credentials.
      </aside>
    <?php
    }
?>
    <main>
      <form action="javascript:void(0);">
        <label for="username" class="form-label">Username:</label>
        <input type="text" class="form-control" id="username"><br>
        <label for="password" class="form-label">Password:</label>
        <input class="form-control" type="password" id="password"><br>
        <button class="btn btn-primary" id="loginButton">
          Log in
        </button>
      </form>
    </main>
  <?php
}
?>
</body>

</html>
<?php $DB = NULL; ?>