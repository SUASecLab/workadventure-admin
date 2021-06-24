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
  <?
  // Connect to database
  $mysqli = mysqli_connect("admin-db", getenv('DB_MYSQL_USER'), getenv('DB_MYSQL_PASSWORD'), getenv('DB_MYSQL_DATABASE'));

  // Get number of users
  $result = $mysqli->query("SELECT count('uuid') as number FROM USERS;");
  $row = mysqli_fetch_assoc($result);

  include 'meta/toolbar.php';
  ?>
  
  <div class="container">
  
  <?
  echo "<p class=\"fs-3\">Listing ".$row["number"]." accounts</p>";

  // Get all users
  $result = $mysqli->query("SELECT * FROM USERS;");
  $rows = $result->fetch_all(MYSQLI_ASSOC);
  
  // Display all accounts
  ?>
  <table class="table">
    <tr>
      <th scope="col">Name</th>
      <th scope="col">Tags</th>
      <th scope="col">Actions</th>
    </tr>
  <?
  foreach ($rows as $row) {
    echo "<tr><td><p class=\"fw-normal\">".$row["name"]."</p></td><td>";
    //if ($row["tags"]) {
    ?>
      <? 
        $tags = $row["tags"];
        $tags = json_decode($tags);
        foreach ($tags as $tag) {
          echo "<div class=\"badge rounded-pill bg-primary tag\">".$tag."</div>";
        }
      ?>
    <?
   // }
    ?>
    </td>
    <td>
    <form action="edit_user.php" method="get">
    <input type="hidden" name="uuid" value="<? echo $row["uuid"]; ?>">
    <input type="submit" class="btn btn-dark" value="Edit">
    </form>
    </td>
    <?
  }
  ?>
    </div>
  </table>
</body>
</html>
