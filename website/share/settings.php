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
  // Connect to database
  try {
    $DB = new PDO(
      "mysql:dbname=" . getenv('DB_MYSQL_DATABASE') . ";host=admin-db;port=3306",
      getenv('DB_MYSQL_USER'),
      getenv('DB_MYSQL_PASSWORD')
    );
  } catch (PDOException $exception) {
    echo "<aside class=\"container alert alert-danger\" role=\"alert\">";
    echo "Could not connect to database: " . $exception->getMessage();
    echo "</aside>";
    return;
  }
  require_once 'api/database_operations.php';
  require_once 'login_functions.php';
  require 'meta/toolbar.php';

  if (!isLoggedIn()) {
    showLogin();
    die();
  }

  if (isset($_GET["edit"])) {
    if (isset($_POST["anonymousAccountCreation"])) {
      setAllowedToCreateNewUser(true);
    } else {
      setAllowedToCreateNewUser(false);
    }
  }

  ?>
  <main>
    <form class="container" action="settings.php?edit=true" method="post">
      <p class="fs-3">Settings</p>
      <div class="form-check form-switch">
        <?php if (allowedToCreateNewUser()) { ?>
          <div class="alert alert-warning" role="alert">
            This setting is insecure: creating accounts for anonymous users automatically works around the 'member' access restriction, as these users are members then!
          </div>
          <input class="form-check-input" type="checkbox" id="createAccountCheckBox" name="anonymousAccountCreation" checked>
          <label class="form-check-label" for="createAccountCheckBox">Create accounts for anonymous users automatically</label>
        <?php } else { ?>
          <input class="form-check-input" type="checkbox" id="createAccountCheckBox" name="anonymousAccountCreation">
          <label class="form-check-label" for="createAccountCheckBox">Create accounts for anonymous users automatically</label>
        <?php } ?>
      </div>
      <br>
      <input class="btn btn-primary" type="submit" value="Update settings">
    </form>
  </main>
  <?php
  $DB = NULL;
  ?>
</body>

</html>