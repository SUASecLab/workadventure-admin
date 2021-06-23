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
  ?>
  <nav class="container navbar navbar-expant-lg navbar-light bg-light">
    <div class="container-fluid">
      <a class="navbar-brand href="#">WorkAdventure Administration</a>
    </div>
  </nav>
  
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
      <th scope="col">UUID</th>
      <th scope="col">Tags</th>
      <th scope="col">Edit tags</th>
    </tr>
  <?
  foreach ($rows as $row) {
    echo "<tr><td><p class=\"fw-normal\">".$row["uuid"]."</p></td><td>";
    if ($row["isadmin"] == "1") {
    ?>
      <span class="badge rounded-pill bg-primary">Admin</span>
    <?
    }
    ?>
    </td>
    <td>
    <form action="promote.php" method="get">
    <input type="hidden" name="promote" value="admin">
    <input type="hidden" name="uuid" value="<? echo $row["uuid"]; ?>">
    <input type="submit" class="btn btn-dark" value="Promote to Admin">
    </form>
    </td>
    <?
  }
  ?>
    </div>
  </table>
</body>
</html>
