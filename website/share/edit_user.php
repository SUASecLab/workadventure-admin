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
  <script src="js/ajax/jquery-3.6.0.min.js"></script>
  <script src="js/ajax/user.js"></script>
  <title>Workadventure Administration</title>
</head>

<body>
  <?php
  // Connect to database
  try {
    $DB = new PDO(
      "mysql:dbname=" . getenv('DB_MYSQL_DATABASE') . ";host=admin-db;port=3306",
      getenv('DB_MYSQL_USER'),
      getenv('DB_MYSQL_PASSWORD')
    );
  } catch (PDOException $exception) {
    echo "<aside class=\"container alert alert-danger\" role=\"alert\">";
    echo "Could not connect to database: " . $exception->getMessage();
    echo "</aside>";
    die();
  }
  require_once 'api/database_operations.php';
  require_once 'login_functions.php';
  require 'meta/toolbar.php';

  if (!isLoggedIn()) {
    showLogin();
    die();
  }

  // Get user's uuid
  if (!isset($_POST["uuid"])) {
  ?>
    <aside class="container alert alert-danger" role="alert" id="alert">
      <p>User not specified</p>
    </aside>
  <?php
    die();
  } else { ?>
    <aside class="container alert" role="alert" id="alert">
    </aside>
  <?php
  }

  $uuid = htmlspecialchars($_POST["uuid"]);

  //this is safe here, as an admin account is needed to access this
  createAccountIfNotExistent($uuid);

  // get current user data
  $userData = getUserData($uuid);
  if ($userData == NULL) {
  ?>
    <aside class="container alert alert-danger" role="alert">
      <p>Could not fetch user details</p>
    </aside>
  <?php
    die();
  }
  ?>
  <main class="container">
    <section>
      <form action="javascript:void(0)" method="post" style="margin-bottom: 1rem;">
        <div class="mb-3">
          <label for="uuid" class="form-label">UUID</label>
          <input type="text" class="form-control" id="uuid" value="<?php echo $uuid; ?>" disabled>
        </div>
        <div class="mb-3">
          <label for="username" class="form-label">Name</label>
          <input type="text" class="form-control" id="username" name="name" value="<?php echo $userData["name"]; ?>">
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
        <input class="btn btn-primary" type="submit" value="Update" name="update-data" id="buttonUpdateMainInformation">
      </form>
      <section>
        <section class="mb-3">
          <label for="name" class="form-label">Access Link</label>
          <input type="text" class="form-control" id="name" value="<?php echo "https://" . getenv('DOMAIN') . "/register/" . $userData["uuid"]; ?>" readonly>
        </section>
        <section class="mb-3" id="tagsSection">
          <?php
          if (hasTags($uuid)) {
          ?>
            <p>Tags (click to remove):</p>
            <?php
            $tags = getTags($uuid);
            foreach ($tags as $currentTag) {
            ?>
              <form action="javascript:void(0)" method="post" class="sameline-form">
                <input class="tag btn btn-primary" type="submit" value="<?php echo $currentTag; ?>" name="removeTag">
                <input type="hidden" name="uuid" value="<?php echo $uuid; ?>">
              </form>
            <?php
            }
            echo "<br><br>";
          } else { ?>
            <p>Tags:</p>
          <?php }
          ?>
        </section>
        <section>
          <form class="input-group mb-3" style="margin-top: 1rem;" action="javascript:void(0)" method="post">
            <input type="text" class="form-control" placeholder="Tag" aria-label="Tag" aria-describedby="buttonTag" id="tagInput" name="newTag">
            <button class="btn btn-primary" type="button" type="submit" id="buttonTag">Add</button>
          </form>
        </section>
        <section id="messagesSection">
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
                <thead>
                  <tr>
                    <th>Message</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="tableBody">
                </tbody>
              </table>
          <?php
            }
          } ?>
        </section>
        <br>
        <section>
          <p>Send new user message:</p>
          <form action="javascript:void(0)" method="post">
            <div class="mb-3">
              <label for="messageInput" class="form-label">Message:</label>
              <textarea class="form-control" id="messageInput" name="message" rows="3"></textarea>
            </div>
            <input type="hidden" name="uuid" value="<?php echo $uuid; ?>">
            <input type="hidden" name="sendMessage" value="true">
            <input type="submit" class="btn btn-primary" id="sendMessage" value="Send">
          </form>
        </section>
        <section>
          <?php if (isBanned($uuid)) { ?>
            <br>
            <p id="banHeader">This user has been banned!</p>
            <div class="mb-3">
              <label for="banReason" class="form-label">Reason:</label>
              <input type="text" class="form-control" id="banReason" value="<?php echo getBanMessage($uuid); ?>" disabled>
            </div>
            <form action="javascript:void(0)" method="post">
              <input class="btn btn-danger" type="submit" id="banButton" value="Lift ban">
              <input type="hidden" name="uuid" value="<?php echo $uuid; ?>">
              <input type="hidden" name="ban" value="false">
            </form>
          <?php } else { ?>
            <br>
            <p id="banHeader">Ban this user:</p>
            <form action="javascript:void(0)" method="post">
              <div class="mb-3">
                <label for="banReason" class="form-label">Reason:</label>
                <input type="text" class="form-control" id="banReason" name="message">
              </div>
              <input class="btn btn-danger" type="submit" id="banButton" value="Ban">
              <input type="hidden" name="uuid" value="<?php echo $uuid; ?>">
              <input type="hidden" name="ban" value="true">

            </form>
          <?php } ?>
        </section>
        <br>
        <input type="hidden" name="uuid" value="<?php echo $uuid; ?>">
        <a class="btn btn-primary" href="user.php" role="button">Go back</a>
        <?php $DB = NULL; ?>
  </main>
</body>

</html>