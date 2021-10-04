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
    $DB = new PDO(
      "mysql:dbname=" . getenv('DB_MYSQL_DATABASE') . ";host=admin-db;port=3306",
      getenv('DB_MYSQL_USER'),
      getenv('DB_MYSQL_PASSWORD')
    );
  } catch (PDOException $exception) {
    echo "<aside class=\"container alert alert-danger\" role=\"alert\">";
    echo "Could not connect to database: " . $exception->getMessage();
    echo "</aside>";
    return;
  }
  require_once 'api/database_operations.php';
  require_once 'login_functions.php';
  require 'meta/toolbar.php';

  if (!isLoggedIn()) {
    showLogin();
    die();
  }

  if ((isset($_POST["removereport"])) && (isset($_POST["reportId"]))) {
    $reportId = htmlspecialchars($_POST["reportId"]);
    if (removeReport($reportId)) { ?>
      <aside class="container alert alert-success" role="alert">
        Removed report.
      </aside>
    <?php } else { ?>
      <aside class="container alert alert-danger" role="alert">
        Could not remove report.
      </aside>
    <?php }
  }

  if (reportsStored()) {
    $reports = getReports();
    if ($reports == NULL) { ?>
      <aside class="container alert alert-danger" role="alert">
        Could not load reports.
      </aside>
    <?php } else { ?>
      <main class="container">
        <table class="table">
          <tr>
            <th scope="col">Reported user</th>
            <th scope="col">Reporter</th>
            <th scope="col">Comment</th>
            <th scope="col">Actions</th>
          </tr>
        <?php
        while ($row = $reports->fetch(PDO::FETCH_ASSOC)) {
          $reportedUserUuid = $row["reportedUserUuid"];
          $reportedUserData = getUserData($reportedUserUuid);
          $reporterUuid = $row["reporterUserUuid"];
          $reporterUserData = getUserData($reporterUuid);
          echo "<tr><td><p class=\"fw-normal\">" . $reportedUserData["name"] . " (" . $reportedUserData["email"] . ")</p></td>";
          echo "<td><p class=\"fw-normal\">" . $reporterUserData["name"] . " (" . $reporterUserData["email"] . ")</p></td>";
          echo "<td><p class=\"fw-normal\">" . $row["reportedUserComment"] . "</p></td>";
          echo "<td>";
          echo "<form action=\"reports.php\" method=\"post\" class=\"sameline-form\"><input class=\"tag btn btn-danger\" type=\"submit\" value=\"Remove report\" name=\"removereport\"><input type=\"hidden\" name=\"reportId\" value=\"" . $row["report_id"] . "\"></form>";
          echo "<form target=\"_blank\" action=\"edit_user.php\" method=\"post\" class=\"sameline-form\"><input class=\"tag btn btn-danger\" type=\"submit\" value=\"Go to reported user\"><input type=\"hidden\" name=\"uuid\" value=\"" . $row["reportedUserUuid"] . "\"></form>";
          echo "<form target=\"_blank\" action=\"edit_user.php\" method=\"post\" class=\"sameline-form\"><input class=\"tag btn btn-danger\" type=\"submit\" value=\"Go to reporter\"><input type=\"hidden\" name=\"uuid\" value=\"" . $row["reporterUserUuid"] . "\"></form>";
          echo "</td></tr>";
        }
      }
      echo "</table></main>";
    } else { ?>
        <main class="container alert alert-primary" role="alert">
          No reports stored.
        </main>
      <?php }

    $DB = NULL; ?>
</body>

</html>