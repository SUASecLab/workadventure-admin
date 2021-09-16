<?php
session_start();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
  <script src="js/bootstrap.min.js"></script>
  <title>Workadventure Administration</title>
</head>
<body>
<?php
try {
    $DB = new PDO("mysql:dbname=".getenv('DB_MYSQL_DATABASE').";host=admin-db;port=3306",
        getenv('DB_MYSQL_USER'), getenv('DB_MYSQL_PASSWORD'));    
}
catch (PDOException $exception) {
    echo "<aside class=\"container alert alert-danger\" role=\"alert\">";
    echo "Could not connect to database: ".$exception->getMessage();
    echo "</aside>";
    return;
}
require_once 'api/database_operations.php';
require_once 'login_functions.php';

$triedToLogin = false;

$validLogin = false;
if ((isset($_POST["username"])) && (isset($_POST["password"]))) {
    $username = htmlspecialchars($_POST["username"]);
    $password = htmlspecialchars($_POST["password"]);
    $validLogin = login($username, $password);
    $triedToLogin = true;
} else {
    $validLogin = false;
}

require 'meta/toolbar.php';

if ($validLogin) {
?>
  <main class="container">
    <section class="alert alert-success" role="alert">
       Welcome back, <?php echo htmlspecialchars($_POST["username"]); ?>
    </section>
    <br>
    <a class="btn btn-primary" href="index.php" role="button">Go to the administration panel</a> 
  </main>
<?php
} else {
    if ($triedToLogin) {
    ?>
      <aside class="container alert alert-danger" role="alert">
        Invalid credentials.
      </aside>
    <?php
    }
?>
  <main class="container">
    <form action="login.php" method="post">
      <label for="username" class="form-label">Username:</label>
      <input type="text" class="form-control" id="username" name="username"><br>
      <label for="password" class="form-label">Password:</label>
      <input class="form-control" type="password" id="password" name="password"><br>
      <input class="btn btn-primary" type="submit" value="Log in">
    </form>
  </main>
<?php
}
?>
</body>
