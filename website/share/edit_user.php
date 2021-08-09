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
    
    // Get user's uuid
    if (!isset($_POST["uuid"])) {
    ?>
        <div class="container alert alert-danger" role="alert">
          <p>User not specified</p>
        </div>
    <?php
        die();
    }

    $uuid = htmlspecialchars($_POST["uuid"]);
    createAccountIfNotExistent($uuid);
        
    // Get new tag
    if (isset($_POST["newtag"])) {
        $newTag = htmlspecialchars($_POST["newtag"]);
        if (!empty($newTag)) {
            $addTagResult = addTag($uuid, $newTag);
            if ($addTagResult == true) {
                ?>
                <div class="container alert alert-success" role="alert">
                  The tag <?php echo "\"".$newTag."\""; ?> has been added.
                </div>
              <?php
            }
            else
            {
                ?>
                <div class="container alert alert-danger" role="alert">
                  Could not add the tag <?php echo "\"".$newTag."\""; ?>
                </div>
                <?php
            }
        }
    }
    
    // Get tag to remove
    if (isset($_POST["remtag"])) {
        $remTag = htmlspecialchars($_POST["remtag"]);
        if (!empty($remTag)) {
            $remTagResult = removeTag($uuid, $remTag);
            if ($remTagResult == true) {
                ?>
                <div class="container alert alert-success" role="alert">
                    The tag <?php echo "\"".$remTag."\""; ?> has been removed.
                </div>
                <?php
            } else {
                ?>
                <div class="container alert alert-danger" role="alert">
                    Could not remove the tag <?php echo "\"".$remTag."\""; ?>.
                </div>
                <?php
            }
        }
    }

    // Ban user if requested
    if (isset($_POST["ban"])) {
      if ((isset($_POST["message"])) && (!empty($_POST["message"])) && (htmlspecialchars($_POST["ban"]) == "true")) {
        if (banUser($uuid, htmlspecialchars($_POST["message"]))) {
          ?>
            <div class="container alert alert-success" role="alert">
              This user has been banned.
            </div>
          <?php
        } else {
          ?>
            <div class="container alert alert-danger" role="alert">
              Could not ban this user.
            </div>
          <?php
        }
      } else if (htmlspecialchars($_POST["ban"]) == "false") {
        if (liftBan($uuid)) {
          ?>
            <div class="container alert alert-success" role="alert">
              This user's ban has been lifted.
            </div>
          <?php
        } else {
          ?>
            <div class="container alert alert-danger" role="alert">
              Could not lift this user's ban.
            </div>
          <?php
        }
      } else {
        ?>
            <div class="container alert alert-danger" role="alert">
              Ban message required.
            </div>
          <?php
      }
    }

    // Update userdata if requested
    if (isset($_POST["update-data"])) {
        if ((isset($_POST["name"])) && (isset($_POST["email"]))) {
            $name = htmlspecialchars($_POST["name"]);
            $email = htmlspecialchars($_POST["email"]);
            if ((empty($name)) || (empty($email))) {
            ?>
                <div class="container alert alert-danger" role="alert">
                New user data must not be empty.
                </div>
            <?php
            } else {
                $visitCardUrl = NULL;
                if (isset($_POST["visitCardUrl"])) {
                    $visitCardUrl = htmlspecialchars($_POST["visitCardUrl"]);
                }
                if (updateUserData($uuid, $name, $email, $visitCardUrl)) {
                ?>
                    <div class="container alert alert-success" role="alert">
                      User data has been updated.
                    </div>
                <?php
                } else {
                ?>
                    <div class="container alert alert-danger" role="alert">
                      User data could not be updated.
                    </div>
                <?php
                }
            }
        } else {
        ?>
            <div class="container alert alert-danger" role="alert">
              New user data could not be fetched.
            </div>
        <?php
        }
    }

    // remove message if requested
    if ((isset($_POST["removemessage"])) && (isset($_POST["message_id"]))) {
        $id = htmlspecialchars($_POST["message_id"]);
        if (removeUserMessage($id)) { ?>
          <div class="container alert alert-success" role="alert">
            <p>Removed message</p>
          </div>
        <?php } else { ?>
          <div class="container alert alert-danger" role="alert">
            <p>Could not remove message</p>
          </div>
        <?php }
    }

    // send message if requested
    if ((isset($_POST["sendMessage"])) && (isset($_POST["message"]))) {
        $sendMessage = htmlspecialchars($_POST["sendMessage"]);
        $message = htmlspecialchars($_POST["message"]);
        if (($sendMessage == "true") && (strlen(trim($message)) > 0)) {
            if (storeUserMessage($uuid, $message)) { ?>
              <div class="container alert alert-success" role="alert">
                <p>Stored user message</p>
              </div>
            <?php } else { ?>
              <div class="container alert alert-danger" role="alert">
                <p>Could not store message</p>
              </div>
        <?php }
       } else { ?>
          <div class="container alert alert-danger" role="alert">
            <p>User message not valid</p>
          </div>
       <?php }
    }

    // get current user data
    $userData = getUserData($uuid);
    if ($userData == NULL) {
    ?>
        <div class="container alert alert-danger" role="alert">
          <p>Could not connect fetch user details</p>
        </div>
        <?php
        die();
    }
  ?>
<div class="container">
  <form action="edit_user.php" method="post" style="margin-bottom: 1rem;">
    <div class="mb-3">
      <label for="name" class="form-label">Name</label>
      <input type="text" class="form-control" id="name" name="name" value="<?php echo $userData["name"]; ?>">
    </div>
    <div class="mb-3">
      <label for="email" class="form-label">Email Address</label>
      <input type="email" class="form-control" id="email" name="email" value="<?php echo $userData["email"]; ?>">
    </div>
    <label for="visitCardUrlLabel" class="form-label">Visit card URL</label>
    <div class="input-group mb-3">
      <span class="input-group-text" id="visitCardUrlPrefix">https://</span>
      <input type="text" class="form-control" id="visitCardUrlLabel" aria-describedby="visitCardUrlPrefix" name="visitCardUrl" value="<?php echo getUserVisitCardUrl($uuid) == NULL ? "" : getUserVisitCardUrl($uuid); ?>">
    </div>
    <input type="hidden" name="uuid" value="<?php echo $uuid; ?>">
    <input class="btn btn-primary" type="submit" value="Update" name="update-data">
  </form>
  <div class="mb-3">
    <label for="name" class="form-label">Access Link</label>
    <input type="text" class="form-control" id="name" value="<?php echo "https://".getenv('DOMAIN')."/register/".$userData["uuid"]; ?>" readonly>
  </div>
  <div class="mb-3">
    <?php
     if (hasTags($uuid)) {
      ?>
      <p>Tags (click to remove):</p>
      <?php
        $tags = getTags($uuid);
        foreach ($tags as $currentTag) {
          echo "<form action=\"edit_user.php\" method=\"post\" class=\"sameline-form\"><input class=\"tag btn btn-primary\" type=\"submit\" value=\"".$currentTag."\" name=\"remtag\"><input type=\"hidden\" name=\"uuid\" value=\"".$uuid."\"></form>";
        }
        echo "<br><br>";
      }
    ?>
    <p>Add tag:</p>
    <form action="edit_user.php" method="post">
      <input class="form-control" type="text" name="newtag"><br>
      <input class="btn btn-primary" type="submit" value="Add tag">
      <input type="hidden" name="uuid" value="<?php echo $uuid; ?>">
    </form>
    <?php if (userMessagesExist($uuid)) {
        $messages = getUserMessages($uuid);
        if ($messages == NULL) { ?>
          <div class="container alert alert-danger" role="alert">
            <p>Could not fetch messages</p>
          </div>
        <?php } else { ?>
        <div class="container alert alert-warning" role="alert">
          <p>Only the top message will be shown to the user. If the user also receives a global message, the global message will be shown instead of the private one!</p>
        </div>
        <p class="fs-3">User messages:</p>
        <table class="table">
          <tr>
            <th scope="col">Message</th>
            <th scope="col">Actions</th>
          </tr>
        <?php
          while($row = $messages->fetch(PDO::FETCH_ASSOC)) {
              echo "<tr><td><p class=\"fw-normal\">".$row["message"]."</p></td>";
              echo "<td><form action=\"edit_user.php\" method=\"post\"><input class=\"tag btn btn-danger\" type=\"submit\" value=\"Remove\" name=\"removemessage\"><input type=\"hidden\" name=\"message_id\" value=\"".$row["message_id"]."\"><input type=\"hidden\" name=\"uuid\" value=\"".$uuid."\"></form></td></tr>";
         }
         echo "</table>";
      } } ?>
      <br>
      <p>Send new user message:</p>
      <form action="edit_user.php" method="post">
        <div class="mb-3">
          <label for="messageInput" class="form-label">Message:</label>
          <textarea class="form-control" id="messageInput" name="message" rows="3"></textarea>
        </div>
        <input type="hidden" name="uuid" value="<?php echo $uuid; ?>">
        <input type="hidden" name="sendMessage" value="true">
        <input type="submit" class="btn btn-primary" value="Send">
      </form>
    <?php if (isBanned($uuid)) { ?>
      <br>
      <p>This user has been banned!</p>
      <div class="mb-3">
        <label for="ban_reason" class="form-label">Reason:</label>
        <input type="text" class="form-control" id="ban_reason" value="<?php echo getBanMessage($uuid); ?>" readonly>
      </div>
      <form action="edit_user.php" method="post">
        <input class="btn btn-danger" type="submit" value="Lift ban">
        <input type="hidden" name="uuid" value="<?php echo $uuid; ?>">
        <input type="hidden" name="ban" value="false">
      </form>
    <?php } else { ?>
      <br>
      <p>Ban this user:</p>
      <form action="edit_user.php" method="post">
        <div class="mb-3">
          <label for="ban_reason" class="form-label">Reason:</label>
          <input type="text" class="form-control" id="ban_reason" name="message">
        </div>
        <input class="btn btn-danger" type="submit" value="Ban">
        <input type="hidden" name="uuid" value="<?php echo $uuid; ?>">
        <input type="hidden" name="ban" value="true">
      </form>
    <?php } ?>
  </div>
  <input type="hidden" name="uuid" value="<?php echo $uuid; ?>">
  <a class="btn btn-primary" href="user.php" role="button">Go back</a> 
  <?php $DB = NULL; ?>
</div>
</body>
</html>
