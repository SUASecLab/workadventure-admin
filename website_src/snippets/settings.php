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
      Could not connect to database: <?php echo $exception->getMessage(); ?>
    </aside>
  <?php return;
  }
  require_once('../api/database_operations.php');
  require_once('../login_functions.php');

  if (!isLoggedIn()) {
    http_response_code(403);
    die();
  }

  if (isset($_POST["anonymousAccountCreation"])) {
    $allowAnonymousAccountCreation = htmlspecialchars($_POST["anonymousAccountCreation"]) == "true";
    setAllowedToCreateNewUser($allowAnonymousAccountCreation);
  }

  ?>
  <main>
    <form action="javascript:void(0);">
      <p class="fs-3">Settings</p>
      <div class="form-check form-switch">
        <?php if (allowedToCreateNewUser()) { ?>
          <input class="form-check-input" type="checkbox" id="createAccountCheckBox" checked>
          <label class="form-check-label" for="createAccountCheckBox">Create accounts for anonymous users automatically</label>
          <div class="alert alert-warning" role="alert">
            This setting is insecure: creating accounts for anonymous users automatically works around the 'member' access restriction, as these users are members then!
          </div>
        <?php } else { ?>
          <input class="form-check-input" type="checkbox" id="createAccountCheckBox">
          <label class="form-check-label" for="createAccountCheckBox">Create accounts for anonymous users automatically</label>
        <?php } ?>
      </div>
      <br>
      <button class="btn btn-primary" type="submit" onclick="updateSettings();">
        Update settings
      </button>
    </form>
  </main>
</body>

</html>

<?php
$DB = NULL;
?>