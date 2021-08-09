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

    // remove texture if requested
    if ((isset($_POST["removetexture"])) && (isset($_POST["texture_table_id"]))) {
        $textureToRemoveId = htmlspecialchars($_POST["texture_table_id"]);
        if(removeTexture($textureToRemoveId)) { ?>
            <div class="container alert alert-success" role="alert">
              <p>Texture has been removed</p>
            </div> 
        <?php } else { ?>
            <div class="container alert alert-danger" role="alert">
              <p>Could not remove texture</p>
            </div> 
        <?php }
    }

    // add texture if requested
    if (isset($_POST["addtexture"])) {
        // check whether url has been added
        if ((isset($_POST["textureUrl"])) && (strlen(htmlspecialchars($_POST["textureUrl"])) > 0) && (htmlspecialchars($_POST["textureUrl"]) != "https://")) {
            $textureId = htmlspecialchars($_POST["textureId"]);
            $textureLevel = htmlspecialchars($_POST["textureLevel"]);
            $textureUrl = htmlspecialchars($_POST["textureUrl"]);
            $textureRights = htmlspecialchars($_POST["textureRights"]);
            $textureNotice = htmlspecialchars($_POST["textureNotice"]);
            if (storeTexture($textureId, $textureLevel, $textureUrl, $textureRights, $textureNotice)) { ?>
              <div class="container alert alert-success" role="alert">
                <p>Stored texture</p>
              </div> 
            <?php } else { ?>
              <div class="container alert alert-danger" role="alert">
                <p>Could not store texture</p>
              </div> 
            <?php }
        } else { ?>
            <div class="container alert alert-danger" role="alert">
              <p>No URL specified</p>
            </div> 
          <?php
        }
    }

    if (customTexturesStored()) {
        $textures = getCustomTextures();
        if ($textures == NULL) { ?>
            <div class="container alert alert-danger" role="alert">
              <p>Could not fetch custom textures</p>
            </div> 
        <?php } else {
            echo "<div class=\"container\"><p class=\"fs-3\">Custom textures:</p>"; ?>
           <table class="table">
             <tr>
              <th scope="col">ID</th>
              <th scope="col">Level</th>
              <th scope="col">Url</th>
              <th scope="col">Rights</th>
              <th scope="col">Notice</th>
              <th scope="col">Action</th>
             </tr>
        <?php
            while($row = $textures->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr><td><p class=\"fw-normal\">".$row["texture_id"]."</p></td>";
                echo "<td><p class=\"fw-normal\">".$row["texture_level"]."</p></td>";
                echo "<td><p class=\"fw-normal\">https://".$row["url"]."</p></td>";
                echo "<td><p class=\"fw-normal\">".$row["rights"]."</p></td>";
                echo "<td><p class=\"fw-normal\">".$row["notice"]."</p></td>";
                echo "<td><form action=\"textures.php\" method=\"post\"><input class=\"tag btn btn-danger\" type=\"submit\" value=\"Remove\" name=\"removetexture\"><input type=\"hidden\" name=\"texture_table_id\" value=\"".$row["texture_table_id"]."\"></form></td></tr>";
            }
            echo "</table></div>";
        }
    }
    ?>
    <div class="container">
      <p class="fs-3">Add custom texture</p>
      <form action="textures.php" method="post" style="margin-bottom: 1rem;">
      <div class="mb-3">
        <label for="textureId" class="form-label">Position ID</label>
        <input type="number" min="0" step="1" class="form-control" id="textureId" name="textureId" value="0">
      </div>
      <div class="mb-3">
        <label for="textureLevel" class="form-label">Texture Level</label>
        <input type="number" min="0" max="5" step="1" class="form-control" id="textureLevel" name="textureLevel" value="0">
      </div>
      <label for="textureUrl" class="form-label">Texture URL</label>
      <div class="input-group mb-3">
        <span class="input-group-text" id="textureUrlPrefix">https://</span>
        <input type="text" class="form-control" id="textureUrl" aria-describedby="textureUrlPrefix" name="textureUrl">
      </div>
      <div class="mb-3">
        <label for="rights" class="form-label">Rights</label>
        <input type="text" class="form-control" id="rights" name="textureRights">
      </div>
      <div class="mb-3">
        <label for="notice" class="form-label">Notice</label>
        <input type="text" class="form-control" id="notice" name="textureNotice">
      </div>
      <input class="btn btn-primary" type="submit" value="Add texture" name="addtexture">
    </div>
  </body>
</html>
