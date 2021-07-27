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
    require 'meta/toolbar.php';

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

    if(!isLoggedIn()) {
        showLogin();
        die();
    }
    
    // Get user details
    if (!isset($_GET["uuid"])) {
    ?>
        <div class="container alert alert-danger" role="alert">
          <p>User not specified</p>
        </div>
    <?php
    }

    $uuid = htmlspecialchars($_GET["uuid"]);
    $userData = getUserData($uuid);
    if ($userData == NULL) {
    ?>
        <div class="container alert alert-danger" role="alert">
          <p>Could not connect fetch user details</p>
        </div>
    
        <?php
        return;
    }
        
    // Get new tag
    if (isset($_GET["newtag"])) {
        $newTag = htmlspecialchars($_GET["newtag"]);
        if (!empty($newTag)) {
            $addTagResult = addTag($uuid, $newTag);
            if ($addTagResult == true) {
                ?>
                <div class="container alert alert-success" role="alert">
                  The tag <?php echo "\"".$newTag."\""; ?> has been added.
                </div>
              <?php
            }
            else
            {
                ?>
                <div class="container alert alert-danger" role="alert">
                  Could not add the tag <?php echo "\"".$newTag."\""; ?>
                </div>
                <?php
            }
        }
    }
    
    // Get tag to remove
    if (isset($_GET["remtag"])) {
        $remTag = htmlspecialchars($_GET["remtag"]);
        if (!empty($remTag)) {
            $remTagResult = removeTag($uuid, $remTag);
            if ($remTagResult == true) {
                ?>
                <div class="container alert alert-success" role="alert">
                    The tag <?php echo "\"".$remTag."\""; ?> has been removed.
                </div>
                <?php
            } else {
                ?>
                <div class="container alert alert-danger" role="alert">
                    Could not remove the tag <?php echo "\"".$remTag."\""; ?>.
                </div>
                <?php
            }
        }
    }

    // Ban user if requested
    if (isset($_GET["ban"])) {
      if ((isset($_GET["message"])) && (!empty($_GET["message"])) && (htmlspecialchars($_GET["ban"]) == "true")) {
        if (banUser($uuid, htmlspecialchars($_GET["message"]))) {
          ?>
            <div class="container alert alert-success" role="alert">
              This user has been banned.
            </div>
          <?php
        } else {
          ?>
            <div class="container alert alert-danger" role="alert">
              Could not ban this user.
            </div>
          <?php
        }
      } else if (htmlspecialchars($_GET["ban"]) == "false") {
        if (liftBan($uuid)) {
          ?>
            <div class="container alert alert-success" role="alert">
              This user's ban has been lifted.
            </div>
          <?php
        } else {
          ?>
            <div class="container alert alert-danger" role="alert">
              Could not lift this user's ban.
            </div>
          <?php
        }
      } else {
        ?>
            <div class="container alert alert-danger" role="alert">
              Ban message required.
            </div>
          <?php
      }
    }
  ?>
<div class="container">
  <div class="mb-3">
    <label for="name" class="form-label">Name</label>
    <input type="text" class="form-control" id="name" value="<?php echo $userData["name"]; ?>" readonly>
  </div>
  <div class="mb-3">
    <label for="email" class="form-label">Email Address</label>
    <input type="email" class="form-control" id="email" value="<?php echo $userData["email"]; ?>" readonly>
  </div>
  <div class="mb-3">
    <label for="name" class="form-label">Access Link</label>
    <input type="text" class="form-control" id="name" value="<?php echo "https://".getenv('DOMAIN')."/register/".$userData["uuid"]; ?>" readonly>
  </div>
  <div class="mb-3">
    <p>Tags (click to remove):</p>
    <?php
      $tags = getTags($uuid);
      foreach ($tags as $currentTag) {
        echo "<form action=\"edit_user.php\" method=\"get\"><input class=\"tag btn btn-primary\" type=\"submit\" value=\"".$currentTag."\" name=\"remtag\"><input type=\"hidden\" name=\"uuid\" value=\"".$uuid."\"></form>";
      }
    ?>
    <br>
    <p>Add tag:</p>
    <form action="edit_user.php" method="get">
      <input class="form-control" type="text" name="newtag"><br>
      <input class="btn btn-primary" type="submit" value="Add tag">
      <input type="hidden" name="uuid" value="<?php echo $uuid; ?>">
    </form>
    <?php
        if (isBanned($uuid)) {
    ?>
      <br>
      <p>This user has been banned!</p>
      <div class="mb-3">
        <label for="ban_reason" class="form-label">Reason:</label>
        <input type="text" class="form-control" id="ban_reason" value="<?php echo getBanMessage($uuid); ?>" readonly>
      </div>
      <form action="edit_user.php" method="get">
        <input class="btn btn-danger" type="submit" value="Lift ban">
        <input type="hidden" name="uuid" value="<?php echo $uuid; ?>">
        <input type="hidden" name="ban" value="false">
      </form>
    <?php
        } else {
    ?>
      <br>
      <p>Ban this user:</p>
      <form action="edit_user.php" method="get">
        <div class="mb-3">
          <label for="ban_reason" class="form-label">Reason:</label>
          <input type="text" class="form-control" id="ban_reason" name="message">
        </div>
        <input class="btn btn-danger" type="submit" value="Ban">
        <input type="hidden" name="uuid" value="<?php echo $uuid; ?>">
        <input type="hidden" name="ban" value="true">
      </form>
    <?php
        }
    ?>
  </div>
  <input type="hidden" name="uuid" value="<?php echo $uuid; ?>">
  <a class="btn btn-primary" href="user.php" role="button">Go back</a> 
  <?php $DB = NULL; ?>
</div>
</body>
</html>
