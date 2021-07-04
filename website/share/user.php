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
  try
  {
    $DB = new PDO("mysql:dbname=".getenv('DB_MYSQL_DATABASE').";host=admin-db;port=3306",
        getenv('DB_MYSQL_USER'), getenv('DB_MYSQL_PASSWORD'));
  }
  catch (PDOException $exception)
  {
    echo "<div class=\"container alert alert-danger\" role=\"alert\">";
    echo "Could not connect to database: ".$exception->getMessage();
    echo "</div>";
    return;
  }

  // Get number of users
  $Statement = $DB->prepare("SELECT count('uuid') as number FROM USERS;");
  if (!$Statement->execute()) {
  ?>
    <div class="container alert alert-danger" role="alert">
      <p>Could not connect fetch user count</p>
    </div>

  <?php
    return;
  }
  $row = $Statement->fetch(PDO::FETCH_ASSOC);

  include 'meta/toolbar.php';
  
  echo "<div class=\"container\">";
  echo "<p class=\"fs-3\">Listing ".$row["number"]." accounts</p>";

  // Get all users
  $Statement = $DB->prepare("SELECT * FROM USERS;");
  if (!$Statement->execute()) {
  ?>
    <div class="container alert alert-danger" role="alert">
      <p>Could not connect fetch users</p>
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
  while($row = $Statement->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr><td><p class=\"fw-normal\">".$row["name"]."</p></td><td>";
        $tags = $row["tags"];
        $tags = json_decode($tags);
        foreach ($tags as $tag) {
          echo "<div class=\"badge rounded-pill bg-primary tag\">".$tag."</div>";
        }
    ?>
    </td>
    <td>
      <form action="edit_user.php" method="get">
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
