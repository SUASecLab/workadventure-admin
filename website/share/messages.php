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

    // remove message if requested
    if ((isset($_POST["removemessage"])) && (isset($_POST["message_id"]))) {
        $id = htmlspecialchars($_POST["message_id"]);
        if (deleteGlobalMessage($id)) { ?>
          <div class="container alert alert-success" role="alert">
            <p>Removed message</p>
          </div>
        <?php } else { ?>
            <div class="container alert alert-danger" role="alert">
              <p>Could not remove message</p>
            </div>
        <?php
        }
    }

    // create new message if requested
    if (isset($_POST["message"])) {
        $message = htmlspecialchars($_POST["message"]);
        if (createNewGlobalMessage($message)) { ?>
          <div class="container alert alert-success" role="alert">
            <p>Created new message</p>
          </div>
        <?php } else { ?>
            <div class="container alert alert-danger" role="alert">
              <p>Could not create new message</p>
            </div>
          <?php
        }
    }
    
    echo "<div class=\"container\">";

    // Get all messages
    $messages = getGlobalMessages();
    if ($messages == NULL) {
  ?>
    <div class="container alert alert-danger" role="alert">
      <p>Could not fetch global messages</p>
    </div>
  <?php die(); }
   if (globalMessagesExist()) {
    echo "<p class=\"fs-3\">Global messages:</p>";
  ?>
    <div class="container alert alert-warning" role="alert">
        <p>Only the top message will be shown to the user. If the user also receives a private message, the global message will be shown instead of the private one!</p>
    </div>
    <table class="table">
      <tr>
        <th scope="col">Message</th>
        <th scope="col">Actions</th>
      </tr>
    <?php
    while($row = $messages->fetch(PDO::FETCH_ASSOC)) {
      echo "<tr><td><p class=\"fw-normal\">".$row["message"]."</p></td>";
      echo "<td><form action=\"messages.php\" method=\"post\"><input class=\"tag btn btn-danger\" type=\"submit\" value=\"Remove\" name=\"removemessage\"><input type=\"hidden\" name=\"message_id\" value=\"".$row["message_id"]."\"></form></td></tr>";
    }
    echo "</table>";
    }
  ?>
  <p class="fs-3">Create new global message:</p>
  <form action="messages.php" method="post">
    <div class="mb-3">
      <label for="messageInput" class="form-label">Message:</label>
      <textarea class="form-control" id="messageInput" name="message" rows="3"></textarea>
    </div>
    <input type="submit" class="btn btn-primary" value="Create">
  </form>
  <?php $DB = NULL; ?>
</body>
</html>
