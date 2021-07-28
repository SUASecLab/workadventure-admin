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

  // Get number of users
  $nrOfUsers = getNumberOfUsers();
  if ($nrOfUsers == NULL) {
    ?>
    <div class="container alert alert-danger" role="alert">
      <p>Could not fetch user count</p>
    </div>
    <?php
    return;
  }

  echo "<div class=\"container\">";
  echo "<p class=\"fs-3\">Listing ".$nrOfUsers." accounts</p>";

  // Get all users
  $users = getAllUsers();
  if ($users == NULL) {
    ?>
    <div class="container alert alert-danger" role="alert">
      <p>Could not fetch users</p>
    </div>
  <?php
    return;
  }
  
  // Display all accounts
  ?>
  <table class="table">
    <tr>
      <th scope="col">Name</th>
      <th scope="col">Tags</th>
      <th scope="col">Actions</th>
    </tr>

  <?php
  while($row = $users->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr><td><p class=\"fw-normal\">".$row["name"]."</p></td><td>";
        $tags = getTags($row["uuid"]);
        foreach ($tags as $currentTag) {
            echo "<div class=\"badge rounded-pill bg-primary tag\">".$currentTag."</div>";
        }
    ?>
    </td>
    <td>
      <form action="edit_user.php" method="post">
        <input type="hidden" name="uuid" value="<?php echo $row["uuid"]; ?>">
        <input type="submit" class="btn btn-dark" value="Edit">
      </form>
    </td>

    <?php
  }
    $DB = NULL;
    ?>
    </div>
  </table>
</body>
</html>
