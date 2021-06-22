<head>
  <title>WorkAdventure administration</title>
</head>
<body>
  <?
    // Connect to database
    $mysqli = mysqli_connect("admin-db", getenv('DB_MYSQL_USER'), getenv('DB_MYSQL_PASSWORD'), getenv('DB_MYSQL_DATABASE'));
    
    // Output user details
    $uuid = $_GET["uuid"];
    echo "<p>UUID: $uuid</p>";
    $result = $mysqli->query("UPDATE USERS SET isadmin=1 WHERE uuid='$uuid';");
    if ($result === TRUE) {
      echo "<p>$uuid is now an administrator</p>";
    } else {
      echo "<p>Promoting $uuid to an admin failed: ".$mysqli->error;
    }
  ?>
  <a href="index.php">Go back</a> 
</body>
