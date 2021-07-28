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

    // check whether map should be stored
    if ((isset($_POST["map_url"])) && (isset($_POST["map_file_url"]))) {
        $mapUrl = htmlspecialchars($_POST["map_url"]);
        $mapFileUrl = htmlspecialchars($_POST["map_file_url"]);
        $mapUrlPrefix = "/@/org/".getenv('DOMAIN')."/";
        if (!str_starts_with($mapUrl, $mapUrlPrefix)) {
            $mapUrl = $mapUrlPrefix.$mapUrl;
        }
        $storedMap = storeMapFileUrl($mapUrl, $mapFileUrl);
        if (!$storedMap) {
          ?>
            <div class="container alert alert-danger" role="alert">
              The map file url for the map <?php echo $mapUrl; ?> could not be stored.
            </div>
            <?php
        } else {
          ?>
          <div class="container alert alert-success" role="alert">
            The map file url for the map <?php echo $mapUrl; ?> has been stored.
          </div>
          <?php
        }
    }

    // checkout whether map should be removed
    if ((isset($_POST["removemap"])) && (isset($_POST["map_url"]))) {
        $mapUrl = htmlspecialchars($_POST["map_url"]);
        $removedMap = removeMap($mapUrl);
        if ($removedMap) {
          ?>
          <div class="container alert alert-success" role="alert">
            The map <?php echo $mapUrl; ?> has been removed.
          </div>
          <?php
        } else {
          ?>
          <div class="container alert alert-danger" role="alert">
            The map <?php echo $mapUrl; ?> could not be removed.
          </div>
          <?php
        }
    }
    
    // check whether start map entry exists
    if (getMapFileUrl(getenv('START_ROOM_URL'))) {
        ?>
          <div class="container">
          <p class="fs-3">Enumeration of existing rooms</p>
        <?php
        $maps = getAllMaps();
        ?>
          <table class="table">
            <tr>
              <th scope="col">Map URL</th>
              <th scope="col">Map File URL</th>
              <th scope="col">Actions</th>
            </tr>
        <?php
        while($row = $maps->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr><td><p class=\"fw-normal\">https://".getenv('DOMAIN').$row["map_url"]."</p></td>";
            echo "<td><p class=\"fw-normal\">https://".$row["map_file_url"]."</p></td>";
            echo "<td><form action=\"rooms.php\" method=\"post\"><input class=\"tag btn btn-danger\" type=\"submit\" value=\"Remove\" name=\"removemap\"><input type=\"hidden\" name=\"map_url\" value=\"".$row["map_url"]."\"></form></td></tr>";
        }
        echo "</table></div>";
        ?>
          <form class="container" action="rooms.php" method="post">
            <p class="fs-3">Add a new room</p>
            <label for="map_url" class="form-label">Map URL</label>
            <div class="input-group mb-3">
              <span class="input-group-text" id="map_url_prefix"><?php echo "https://".getenv('DOMAIN')."/@/org/".getenv('DOMAIN')."/"; ?></span>
              <input type="text" class="form-control" id="map_url" aria-describedby="map_url_prefix" name="map_url">
            </div>
            <label for="map_url_file" class="form-label">Map URL File</label>
            <div class="input-group mb-3">
              <span class="input-group-text" id="map_url_file_prefix">https://</span>
              <input type="text" class="form-control" id="map_url_file" aria-describedby="map_url_file_prefix" name="map_file_url">
            </div>
            <input class="btn btn-primary" type="submit" value="Add map">
          </form>
        <?php
    } else {
      ?>
        <div class="container alert alert-danger" role="alert">
            The map file for the start room has not been set so far!
        </div>
        <form class="container" action="rooms.php" method="post">
          <label for="map-file-url" class="form-label">URL for the start room map (<?php echo getenv('START_ROOM_URL'); ?>):</label>
          <div class="input-group mb-3">
            <span class="input-group-text" id="map_file_url">https://</span>
            <input type="text" class="form-control" id="map-file-url" aria-describedby="map_file_url" name="map_file_url">
          </div>
          <input type="hidden" name="map_url" value="<?php echo getenv('START_ROOM_URL'); ?>">
          <input class="btn btn-primary" type="submit" value="Set URL">
        </form>
      <?php
    }
  ?>
</body>
</html>
