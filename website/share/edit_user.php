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
    include 'meta/toolbar.php';
  
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
    
    // Get user details
    if (!isset($_GET["uuid"])) {
    ?>
        <div class="container alert alert-danger" role="alert">
          <p>User not specified</p>
        </div>
    <?php
    }

    $uuid = $_GET["uuid"];
    $Statement = $DB->prepare("SELECT * FROM users WHERE uuid=:uuid;");
    $Statement->bindParam(":uuid", $uuid, PDO::PARAM_STR);
    try {
        $Statement->execute();
    }
    catch (PDOException $exception) {
    ?>
        <div class="container alert alert-danger" role="alert">
          <p>Could not connect fetch user details</p>
        </div>
    
        <?php
        return;
    }
    $row = $Statement->fetch(PDO::FETCH_ASSOC);
        
    // Get new tag
    if (isset($_GET["newtag"])) {
        $newtag = $_GET["newtag"];
        if (!empty($newtag)) {
            $Statement = $DB->prepare("INSERT INTO tags (uuid, tag) VALUES (:uuid, :tag);");
            $Statement->bindParam(":uuid", $uuid, PDO::PARAM_STR);
            $Statement->bindParam(":tag", $newtag, PDO::PARAM_STR);
            try {
                $Statement->execute();
                ?>
                <div class="container alert alert-success" role="alert">
                  The tag <?php echo "\"".$newtag."\""; ?> has been added.
                </div>
              <?php
            }
            catch (PDOException $exception)
            {
                ?>
                <div class="container alert alert-danger" role="alert">
                  Could not add the tag <?php echo "\"".$newtag."\""; ?>
                </div>
                <?php
            }
        }
    }
    
    // Get tag to remote
    if (isset($_GET["remtag"])) {
        $remtag = $_GET["remtag"];
        if (!empty($remtag)) {
            $Statement = $DB->prepare("DELETE FROM tags WHERE uuid=:uuid AND tag=:tag;");
            $Statement->bindParam(":uuid", $uuid, PDO::PARAM_STR);
            $Statement->bindParam(":tag", $remtag, PDO::PARAM_STR);
            try {
                $Statement->execute();
                ?>
                <div class="container alert alert-success" role="alert">
                    The tag <?php echo "\"".$remtag."\""; ?> has been removed.
                </div>
                <?php
            } catch (PDOException $exception) {
                ?>
                <div class="container alert alert-danger" role="alert">
                    Could not remove the tag <?php echo "\"".$remtag."\""; ?>.
                </div>
                <?php
            }
        }
    }
  ?>
<div class="container">
  <div class="mb-3">
    <label for="name" class="form-label">Name</label>
    <input type="text" class="form-control" id="name" value="<?php echo $row["name"]; ?>" readonly>
  </div>
  <div class="mb-3">
    <label for="email" class="form-label">Email Address</label>
    <input type="email" class="form-control" id="email" value="<?php echo $row["email"]; ?>" readonly>
  </div>
  <div class="mb-3">
    <label for="name" class="form-label">Access Link</label>
    <input type="text" class="form-control" id="name" value="<?php echo "https://".getenv('DOMAIN')."/register/".$row["uuid"]; ?>" readonly>
  </div>
  <div class="mb-3">
    <p>Tags (click to remove):</p>
    <?php

      $Statement = $DB->prepare("SELECT tag FROM tags WHERE uuid=:uuid;");
      $Statement->bindParam(":uuid", $uuid, PDO::PARAM_STR);
      try {
          $Statement->execute();
          while ($tagRow = $Statement->fetch(PDO::FETCH_ASSOC)) {
              echo "<form action=\"edit_user.php\" method=\"get\"><input class=\"tag btn btn-primary\" type=\"submit\" value=\"".$tagRow["tag"]."\" name=\"remtag\"><input type=\"hidden\" name=\"uuid\" value=\"".$uuid."\"></form>";
          }
      }
      catch (PDOException $exception) {
      }
    ?>
    <br>
    <p>Add tag:</p>
    <form action="edit_user.php" method="get">
      <input class="form-control" type="text" name="newtag"><br>
      <input class="btn btn-primary" type="submit" value="Add tag">
      <input type="hidden" name="uuid" value="<?php echo $uuid; ?>">
    </form>
  </div>
  <input type="hidden" name="uuid" value="<?php echo $uuid; ?>">
  <a class="btn btn-primary" href="user.php" role="button">Go back</a> 
  <?php $DB = NULL; ?>
</div>
</body>
</html>