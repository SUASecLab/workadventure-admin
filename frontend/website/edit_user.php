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
    include 'meta/toolbar.php';
  
    // Connect to database
    $mysqli = mysqli_connect("admin-db", getenv('DB_MYSQL_USER'), getenv('DB_MYSQL_PASSWORD'), getenv('DB_MYSQL_DATABASE'));
    
    // Get user details
    $uuid = $_GET["uuid"];
    $result = $mysqli->query("SELECT * FROM USERS WHERE uuid='$uuid';");
    $row = mysqli_fetch_assoc($result);
    
        
    // Get new tag
    if (isset($_GET["newtag"])) {
        $newtag = $_GET["newtag"];
        if (!empty($newtag)) {
            $tags = json_decode($row["tags"]);
            if (!in_array($newtag, $tags, false)) {
                array_push($tags, $newtag);
                $tagsString = json_encode($tags);
                $update = $mysqli->query("UPDATE USERS SET tags='$tagsString' WHERE uuid='$uuid';");
                if ($update === TRUE) {
                    ?>
                    <div class="container alert alert-success" role="alert">
                      The tag <? echo $newtag; ?> has been added.
                    </div>
                    <?
                } else {
                    ?>
                    <div class="container alert alert-danger" role="alert">
                      Could not add the tag <? echo $newtag; ?>.
                    </div>
                    <?
                 }
             } else {
                ?>
                <div class="container alert alert-danger" role="alert">
                    The user has already the <? echo $newtag; ?> tag.
                </div>
                <?
             }
        }
    }
    
    // Get tag to remote
    if (isset($_GET["remtag"])) {
        $remtag = $_GET["remtag"];
        if (!empty($remtag)) {
            $tags = json_decode($row["tags"]);
            if (in_array($remtag, $tags, false)) {
                $oldTags = $tags;
                $tags = array();
                foreach($oldTags as $tag) {
                    if (!($tag == $remtag)) {
                        array_push($tags, $tag);
                    }
                }
            }
            $tagsJson = json_encode($tags);
            $update = $mysqli->query("UPDATE USERS SET tags='$tagsJson' WHERE uuid='$uuid';");
            if ($update === TRUE) {
                ?>
                <div class="container alert alert-success" role="alert">
                    The tag <? echo $remtag; ?> has been removed.
                </div>
                <?
            } else {
                ?>
                <div class="container alert alert-danger" role="alert">
                    Could not remove the tag <? echo $remtag; ?>.
                </div>
                <?
            }
        }
    }
  ?>
<div class="container">
  <div class="mb-3">
    <label for="name" class="form-label">Name</label>
    <input type="text" class="form-control" id="name" value="<? echo $row["name"]; ?>" readonly>
  </div>
  <div class="mb-3">
    <label for="email" class="form-label">Email Address</label>
    <input type="email" class="form-control" id="email" value="<? echo $row["email"]; ?>" readonly>
  </div>
  <div class="mb-3">
    <label for="name" class="form-label">Access Link</label>
    <input type="text" class="form-control" id="name" value="<? echo "https://".getenv('DOMAIN')."/register/".$row["uuid"]; ?>" readonly>
  </div>
  <div class="mb-3">
    <p>Tags (click to remove):</p>
    <? 
      $result = $mysqli->query("SELECT * FROM USERS WHERE uuid='$uuid';");
      $row = mysqli_fetch_assoc($result);
      $tags = json_decode($row["tags"]);
      foreach ($tags as $tag) {
        echo "<form action=\"edit_user.php\" method=\"get\"><input class=\"tag btn btn-primary\" type=\"submit\" value=\"".$tag."\" name=\"remtag\"><input type=\"hidden\" name=\"uuid\" value=\"".$uuid."\"></form>";
      }
    ?>
    <br>
    <p>Add tag:</p>
    <form action="edit_user.php" method="get">
      <input class="form-control" type="text" name="newtag"><br>
      <input class="btn btn-primary" type="submit" value="Add tag">
      <input type="hidden" name="uuid" value="<? echo $uuid; ?>">
    </form>
  </div>
  <input type="hidden" name="uuid" value="<? echo $uuid; ?>">
  <a class="btn btn-primary" href="user.php" role="button">Go back</a> 
</div>
</body>
</html>
