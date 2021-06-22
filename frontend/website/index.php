<head>
  <title>Workadventure Administration</title>
</head>
<body>
  <?
  // Connect to database
  $mysqli = mysqli_connect("admin-db", getenv('DB_MYSQL_USER'), getenv('DB_MYSQL_PASSWORD'), getenv('DB_MYSQL_DATABASE'));

  // Get number of users
  $result = $mysqli->query("SELECT count('uuid') as number FROM USERS;");
  $row = mysqli_fetch_assoc($result);
  echo "<h3>Listing ".$row["number"]." accounts</h3>";
  

  // Get all users
  $result = $mysqli->query("SELECT * FROM USERS;");
  $rows = $result->fetch_all(MYSQLI_ASSOC);
  
  // Display all accounts
  ?>
  <table>
    <tr>
      <th>UUID</th>
      <th>Is Admin</th>
      <th>Promote to Admin</th>
    </tr>
  <?
  foreach ($rows as $row) {
    echo "<tr><td>".$row["uuid"]."</td><td>";
    if ($row["isadmin"] == "1") {
      echo "Yes";
    } else {
      echo "No";
    }
    ?>
    </td>
    <td>
    <form action="promote.php" method="get">
    <input type="hidden" name="promote" value="admin">
    <input type="hidden" name="uuid" value="<? echo $row["uuid"]; ?>">
    <input type="submit" value="Promote to Admin">
    </form>
    </td>
    <?
  }
  ?>
  </table>
</body>
