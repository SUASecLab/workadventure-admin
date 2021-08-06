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

        if ((empty($mapUrl)) || (empty($mapFileUrl))) {
            ?>
              <div class="container alert alert-danger" role="alert">
                The map data must not be empty.
              </div>
            <?php
        } else {
            $invalidData = false;
            $tags = array();
            $mapUrlPrefix = "/@/org/".getenv('DOMAIN')."/";
            if (!str_starts_with($mapUrl, $mapUrlPrefix)) {
                $mapUrl = $mapUrlPrefix.$mapUrl;
            }
            $policyNumber = 2;
            $policy = htmlspecialchars($_POST["radio"]);
            if ($policy == "public") {
              $policyNumber = 1;
            } else if ($policy == "members") {
              $policyNumber = 2;
            } else {
              $policyNumber = 3;
              if ((isset($_POST["tags"])) && (!strlen(htmlspecialchars($_POST["tags"])) == 0)) {
                    $tagsList = htmlspecialchars($_POST["tags"]);
                    $tagsArray = explode(",", $tagsList);
                    if (sizeof($tagsArray) > 0) {
                        foreach ($tagsArray as $tag) {
                            array_push($tags, trim($tag));
                        }
                    } else {
                        $invalidData = true;
                    }
              } else {
                $invalidData = true;
              }
            }
            if (!$invalidData) {
                $storedMap = storeMapFileUrl($mapUrl, $mapFileUrl, $policyNumber);
                foreach ($tags as $tag) {
                    addMapTag($mapUrl, $tag);
                }
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
            } else {
                ?>
                <div class="container alert alert-danger" role="alert">
                  No tags specified.
                </div>
                <?php
            }
        }
    }

    // checkout whether map should be removed
    if ((isset($_POST["removemap"])) && (isset($_POST["map_url"]))) {
        $mapUrl = htmlspecialchars($_POST["map_url"]);
        removeMapTags($mapUrl);
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
              <th scope="col">Access restriction</th>
              <th scope="col">Actions</th>
            </tr>
        <?php
        while($row = $maps->fetch(PDO::FETCH_ASSOC)) {
            $restriction = $row["policy"];
            if ($restriction == 1) {
                $restriction = "Public";
            } else if ($restriction == 2) {
                $restriction = "Members";
            } else if ($restriction == 3) {
                $tags = getMapTags($row["map_url"]);
                $tagsAsString = "";
                foreach ($tags as $tag) {
                    $tagsAsString = $tagsAsString."<div class=\"badge rounded-pill bg-primary tag\">".$tag."</div>";
                }
                $restriction = "Members with tags ".$tagsAsString;
            } else {
                $restriction = "Undefined value";
            }

            echo "<tr><td><p class=\"fw-normal\">https://".getenv('DOMAIN').$row["map_url"]."</p></td>";
            echo "<td><p class=\"fw-normal\">https://".$row["map_file_url"]."</p></td>";
            echo "<td><p class=\"fw-normal\">".$restriction."</p></td>";
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
            <p class="fs-6">Access restriction</p>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="radio" id="radioPublic" value="public"/>
              <label class="form-check-label" for="radioPublic">Public</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="radio" id="radioMembers" value="members" checked/>
              <label class="form-check-label" for="radioMembers">Members</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="radio" id="radioMembersTags" value="tags"/>
              <label class="form-check-label" for="radioMembersTags">Members with tags</label>
            </div>
            <div id="tagsArea" class="input-group mb-3">
            </div>
            <input class="btn btn-primary" type="submit" style="margin-top: 1rem;" value="Add map">
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
          <p class="fs-6">Access restriction</p>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="radio" id="radioPublic" value="public"/>
            <label class="form-check-label" for="radioPublic">Public</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="radio" id="radioMembers" value="members" checked/>
            <label class="form-check-label" for="radioMembers">Members</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="radio" id="radioMembersTags" value="tags"/>
            <label class="form-check-label" for="radioMembersTags">Members with tags</label>
          </div>
          <div id="tagsArea" class="input-group mb-3">
          </div>
          <input type="hidden" name="map_url" value="<?php echo getenv('START_ROOM_URL'); ?>">
          <input class="btn btn-primary" type="submit" style="margin-top: 1rem;" value="Set URL">
        </form>
      <?php
    }
  ?>
  <script>
    var tagsElementsShown = false;

    function addTagsElements() {
        if (!tagsElementsShown) {
            // create tags section
            var container = document.getElementById('tagsArea');
            var tagsArea = document.createElement('div');
            tagsArea.id = 'tagsAreaDiv';
            tagsArea.innerHTML = '<div class="mb-3" style="margin-top: 1rem;"><label for="tagsInput" class="form-label">Tags (comma separated): </label><input type="text" class="form-control" id="tagsInput" name="tags"></div>';
            container.appendChild(tagsArea);
        }
        tagsElementsShown = true;
    }

    function removeTagsElements() {
        if (tagsElementsShown) {
            // remove tags section
            var tagsArea = document.getElementById('tagsAreaDiv');
            tagsArea.remove();
        }
        tagsElementsShown = false;
    }

    document.getElementById('radioPublic').onclick = function() {
        removeTagsElements()
    };

    document.getElementById('radioMembers').onclick = function() {
        removeTagsElements()
    };
    document.getElementById('radioMembersTags').onclick = function() {
        addTagsElements()
    };
  </script>
</body>
</html>
