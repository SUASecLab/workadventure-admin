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
        $DB = new PDO("mysql:dbname=".getenv('DB_MYSQL_DATABASE').";host=admin-db;port=3306",
        getenv('DB_MYSQL_USER'), getenv('DB_MYSQL_PASSWORD'));
    }
    catch (PDOException $exception) {
        echo "<div class=\"container alert alert-danger\" role=\"alert\">";
        echo "Could not connect to database: ".$exception->getMessage();
        echo "</div>";
        return;
    }
    require_once 'api/database_operations.php';
    require_once 'login_functions.php';
    require 'meta/toolbar.php';

    if(!isLoggedIn()) {
        showLogin();
        die();
    }
  ?>
  
  <div class="container">
    <div class="row">
      <div class="col-sm-6">
        <div class="card" style="width: 18 rem; height: 13rem;">
          <div class="card-body">
            <h5 class="card-title">Room Management</h5>
            <p class="card-text">Here, you can adjust the room settings. Furthermore, you can restrict the access to the rooms.</p>
            <a href="rooms.php" class="btn btn-primary">Go to Room Management</a>
          </div>
        </div>
      </div>
      <div class="col-sm-6">
        <div class="card" style="width: 18 rem; height: 13rem;">
          <div class="card-body">
            <h5 class="card-title">User Management</h5>
            <p class="card-text">Here, you can manage the user accounts. You can also set user tags here and ban users or lift the bans.</p>
            <a href="user.php" class="btn btn-primary">User Management</a>
          </div>
        </div>
      </div>
      <div class="col-sm-6">
        <div class="card" style="width: 18 rem; height: 13rem; margin-top: 1rem;">
          <div class="card-body">
            <h5 class="card-title">Settings</h5>
            <p class="card-text">Here, you can change some settings of the Admin API.</p>
            <a href="settings.php" class="btn btn-primary">Go to Settings</a>
          </div>
        </div>
      </div>
</body>
</html>
