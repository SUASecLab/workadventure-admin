<?php
session_start();
?>
<!doctype html>
<html lang="en">

<body>
  <?php
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
    return;
  }
  require_once('../api/database_operations.php');
  require_once('../login_functions.php');

  $triedToLogin = false;

  $validLogin = false;
  if ((isset($_POST["username"])) && (isset($_POST["password"]))) {
    $username = htmlspecialchars($_POST["username"]);
    $password = htmlspecialchars($_POST["password"]);
    $validLogin = login($username, $password);
    $triedToLogin = true;
  } else {
    $validLogin = false;
  }

  if ($validLogin) {
  ?>
    <main>
      <section class="alert alert-success" role="alert">
        Welcome back, <?php echo htmlspecialchars($_POST["username"]); ?>
      </section>
      <br>
      <a class="btn btn-primary" href="index.php" role="button">Go to the administration panel</a>
    </main>
    <?php
  } else {
    if ($triedToLogin) {
    ?>
      <aside class="alert alert-danger" role="alert">
        Invalid credentials.
      </aside>
    <?php
    }
    ?>
    <main>
      <form action="javascript:void(0);">
        <label for="username" class="form-label">Username:</label>
        <input type="text" class="form-control" id="username"><br>
        <label for="password" class="form-label">Password:</label>
        <input class="form-control" type="password" id="password"><br>
        <button class="btn btn-primary" onclick="login();">
          Log in
        </button>
      </form>
    </main>
  <?php
  }
  ?>
</body>

</html>
<?php $DB = NULL; ?>