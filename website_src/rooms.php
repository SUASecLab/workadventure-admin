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
  <script src="js/ajax/jquery-3.6.0.min.js"></script>
  <script src="js/ajax/rooms.js"></script>

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
    die();
  }
  require_once 'api/database_operations.php';
  require_once 'login_functions.php';
  require 'meta/toolbar.php';

  if (!isLoggedIn()) {
    showLogin();
    die();
  }

  ?>

  <!-- ajax -->
  <aside id="alert" class="container alert" role="alert">
  </aside>

  <main class="container">

    <!-- ajax -->
    <article id="maps">
    </article>

    <article>
      <form action="javascript:void(0)">
        <p class="fs-3" id="add-map-paragraph">Add a new room</p>
        <label for="mapURL" class="form-label">Map URL</label>
        <div class="input-group mb-3">
          <span class="input-group-text" id="map_url_prefix"><?php echo "https://" . getenv('DOMAIN') . "/@/org/" . getenv('DOMAIN') . "/"; ?></span>
          <input type="text" class="form-control" id="mapURL" aria-describedby="map_url_prefix" name="map_url">
        </div>
        <label for="mapURLFile" class="form-label">Map File URL</label>
        <div class="input-group mb-3">
          <span class="input-group-text" id="map_url_file_prefix">https://</span>
          <input type="text" class="form-control" id="mapURLFile" aria-describedby="map_url_file_prefix" name="map_file_url">
        </div>
        <p class="fs-6">Access restriction</p>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="radio" id="radioPublic" value="public" />
          <label class="form-check-label" for="radioPublic">Public</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="radio" id="radioMembers" value="members" checked />
          <label class="form-check-label" for="radioMembers">Members</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="radio" id="radioMembersTags" value="tags" />
          <label class="form-check-label" for="radioMembersTags">Members with tags</label>
        </div>
        <div id="tagsArea" class="input-group mb-3">
        </div>
        <button class="btn btn-primary" id="addMapButton" style="margin-top: 1rem;">Add map</button>
      </form>
    </article>
  </main>
</body>

</html>