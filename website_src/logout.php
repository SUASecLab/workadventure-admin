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

  if (!isLoggedIn()) {
    require 'meta/toolbar.php';
  ?>
    <main class="container alert alert-danger" role="alert">
      You have not been logged in!
    </main>
  <?php
  } else {
    session_unset();
    session_destroy();
    require 'meta/toolbar.php';
  ?>
    <main class="container alert alert-success" role="alert">
      You have been logged out.
    </main>
  <?php
  }
  ?>
</body>

</html>