<?php
session_start();
?>
<!doctype html>
<html lang="en">

<body>
  <?php
  // Connect to database
  $DB = NULL;
  try {
    $DB = new PDO(
      "mysql:dbname=" . getenv('DB_MYSQL_DATABASE') . ";host=admin-db;port=3306",
      getenv('DB_MYSQL_USER'),
      getenv('DB_MYSQL_PASSWORD')
    );
  } catch (PDOException $exception) { ?>
    <aside class="alert alert-danger" role="alert">
      "Could not connect to database: <?php echo $exception->getMessage(); ?>
    </aside>
  <?php return;
  }

  require_once('../api/database_operations.php');
  require_once('../login_functions.php');

  if (!isLoggedIn()) {
    http_response_code(403);
    die();
  }

  // Get user's uuid
  if (!isset($_POST["uuid"])) {
  ?>
    <aside class="alert alert-danger" role="alert">
      <p>User not specified</p>
    </aside>
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
        <aside class="alert alert-success" role="alert">
          The tag <?php echo "\"" . $newTag . "\""; ?> has been added.
        </aside>
      <?php
      } else {
      ?>
        <aside class="alert alert-danger" role="alert">
          Could not add the tag <?php echo "\"" . $newTag . "\""; ?>
        </aside>
      <?php
      }
    }
  }

  // Get tag to remove
  if (isset($_POST["remtag"])) {
    $remTag = htmlspecialchars($_POST["remtag"]);
    if (!(strlen($remTag) == 0)) {
      $remTagResult = removeTag($uuid, $remTag);
      if ($remTagResult == true) {
      ?>
        <aside class="alert alert-success" role="alert">
          The tag <?php echo "\"" . $remTag . "\""; ?> has been removed.
        </aside>
      <?php
      } else {
      ?>
        <aside class="alert alert-danger" role="alert">
          Could not remove the tag <?php echo "\"" . $remTag . "\""; ?>.
        </aside>
      <?php
      }
    }
  }

  // Ban user if requested
  if (isset($_POST["ban"])) {
    if ((isset($_POST["message"])) && (!empty($_POST["message"])) && (htmlspecialchars($_POST["ban"]) == "true")) {
      if (banUser($uuid, htmlspecialchars($_POST["message"]))) {
      ?>
        <aside class="alert alert-success" role="alert">
          This user has been banned.
        </aside>
      <?php
      } else {
      ?>
        <aside class="alert alert-danger" role="alert">
          Could not ban this user.
        </aside>
      <?php
      }
    } else if (htmlspecialchars($_POST["ban"]) == "false") {
      if (liftBan($uuid)) {
      ?>
        <aside class="alert alert-success" role="alert">
          This user's ban has been lifted.
        </aside>
      <?php
      } else {
      ?>
        <aside class="alert alert-danger" role="alert">
          Could not lift this user's ban.
        </aside>
      <?php
      }
    } else {
      ?>
      <aside class="alert alert-danger" role="alert">
        Ban message required.
      </aside>
    <?php
    }
  }

  // Update userdata if requested
  if ((isset($_POST["name"])) && (isset($_POST["email"]))) {
    $name = htmlspecialchars($_POST["name"]);
    $email = htmlspecialchars($_POST["email"]);
    if ((empty($name)) || (empty($email))) {
    ?>
      <aside class="alert alert-danger" role="alert">
        New user data must not be empty.
      </aside>
      <?php
    } else {
      $visitCardUrl = NULL;
      if (isset($_POST["visitCardUrl"])) {
        $visitCardUrl = htmlspecialchars($_POST["visitCardUrl"]);
      }
      if (updateUserData($uuid, $name, $email, $visitCardUrl)) { ?>
        <aside class="alert alert-success" role="alert">
          User data has been updated.
        </aside>
      <?php
      } else { ?>
        <aside class="alert alert-danger" role="alert">
          User data could not be updated.
        </aside>
      <?php
      }
    }
  }

  // remove message if requested
  if ((isset($_POST["removemessage"])) && (isset($_POST["message_id"]))) {
    $id = htmlspecialchars($_POST["message_id"]);
    if (removeUserMessage($id)) { ?>
      <aside class="alert alert-success" role="alert">
        <p>Removed message</p>
      </aside>
    <?php } else { ?>
      <aside class="alert alert-danger" role="alert">
        <p>Could not remove message</p>
      </aside>
      <?php }
  }

  // send message if requested
  if ((isset($_POST["sendmessage"])) && (isset($_POST["message"]))) {
    $message = htmlspecialchars($_POST["message"]);
    if (strlen(trim($message)) > 0) {
      if (storeUserMessage($uuid, $message)) { ?>
        <aside class="alert alert-success" role="alert">
          <p>Stored user message</p>
        </aside>
      <?php } else { ?>
        <aside class="alert alert-danger" role="alert">
          <p>Could not store message</p>
        </aside>
      <?php }
    } else { ?>
      <aside class="alert alert-danger" role="alert">
        <p>User message not valid</p>
      </aside>
    <?php }
  }

  // get current user data
  $userData = getUserData($uuid);
  if ($userData == NULL) {
    ?>
    <aside class="alert alert-danger" role="alert">
      <p>Could not fetch user details</p>
    </aside>
  <?php
    die();
  }
  ?>
  <main>
    <section>
      <form action="javascript:void(0);" style="margin-bottom: 1rem;">
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
        <button class="btn btn-primary" onclick="updateUserData('<?php echo $uuid; ?>');">Update</button>
      </form>
      <section>
        <section class="mb-3">
          <label for="name" class="form-label">Access Link</label>
          <input type="text" class="form-control" id="name" value="<?php echo "https://" . getenv('DOMAIN') . "/register/" . $userData["uuid"]; ?>" readonly>
        </section>
        <section class="mb-3">
          <?php
          if (hasTags($uuid)) {
          ?>
            <p>Tags (click to remove):</p>
            <?php
            $tags = getTags($uuid);
            foreach ($tags as $currentTag) { ?>
              <button class="tag btn btn-primary" onclick="removeTag('<?php echo $uuid; ?>', '<?php echo $currentTag; ?>');">
                <?php echo $currentTag; ?>
              </button>
          <?
            }
            echo "<br><br>";
          }
          ?>
        </section>
        <section>
          <p>Add tag:</p>
          <form action="javascript:void(0)">
            <input class="form-control" type="text" id="newtag"><br>
            <button class="btn btn-primary" onclick="addTag('<?php echo $uuid; ?>');">Add tag</button>
          </form>
        </section>
        <section>
          <?php if (userMessagesExist($uuid)) {
            $messages = getUserMessages($uuid);
            if ($messages == NULL) { ?>
              <br>
              <div class="alert alert-danger" role="alert">
                <p>Could not fetch messages</p>
              </div>
            <?php } else { ?>
              <br>
              <div class="alert alert-warning" role="alert">
                <p>Only the top message will be shown to the user. If the user also receives a global message, the global message will be shown instead of the private one!</p>
              </div>
              <p class="fs-3">User messages:</p>
              <table class="table">
                <tr>
                  <th scope="col">Message</th>
                  <th scope="col">Actions</th>
                </tr>
                <?php
                while ($row = $messages->fetch(PDO::FETCH_ASSOC)) { ?>
                  <tr>
                    <td>
                      <p class="fw-normal">
                        <?php echo $row["message"]; ?>
                      </p>
                    </td>
                    <td>
                      <button class="tag btn btn-danger" onclick="removeMessage('<?php echo $uuid; ?>', '<?php echo $row['message_id']; ?>');">
                        Remove
                      </button>
                    </td>
                  </tr>
                <?php } ?>
              </table> <?php
                      }
                    } ?>
        </section>
        <br>
        <section>
          <p>Send new user message:</p>
          <form action="javascript:void(0);">
            <div class="mb-3">
              <label for="messageInput" class="form-label">Message:</label>
              <textarea class="form-control" id="messageInput" name="message" rows="3"></textarea>
            </div>
            <button class="btn btn-primary" onclick="sendMessage('<?php echo $uuid; ?>');">
              Send
            </button>
          </form>
        </section>
        <section>
          <?php if (isBanned($uuid)) { ?>
            <br>
            <p>This user has been banned!</p>
            <div class="mb-3">
              <label for="banReason" class="form-label">Reason:</label>
              <input type="text" class="form-control" id="banReason" value="<?php echo getBanMessage($uuid); ?>" readonly>
            </div>
            <button class="btn btn-danger" onclick="unban('<?php echo $uuid; ?>');">Lift ban</button>
          <?php } else { ?>
            <br>
            <p>Ban this user:</p>
            <form action="javascript:void(0);">
              <div class="mb-3">
                <label for="banReason" class="form-label">Reason:</label>
                <input type="text" class="form-control" id="banReason" name="message">
              </div>
              <button class="btn btn-danger" onclick="ban('<?php echo $uuid; ?>');">Ban</button>
            </form>
          <?php } ?>
        </section>
        <br>
        <a class="btn btn-primary" href="user.php" role="button">Go back</a>
  </main>
</body>

</html>
<?php $DB = NULL; ?>