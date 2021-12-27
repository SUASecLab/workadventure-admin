<?php
session_start();
?>
<!doctype html>
<html lang="en">

<body>
  <?php
  // Connect to database
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

  if (isset($_POST["reportId"])) {
    $reportId = htmlspecialchars($_POST["reportId"]);
    if (removeReport($reportId)) { ?>
      <aside class="alert alert-success" role="alert">
        Removed report.
      </aside>
    <?php } else { ?>
      <aside class="alert alert-danger" role="alert">
        Could not remove report.
      </aside>
    <?php }
  }

  if (reportsStored()) {
    $reports = getReports();
    if ($reports == NULL) { ?>
      <aside class="alert alert-danger" role="alert">
        Could not load reports.
      </aside>
    <?php } else { ?>
      <main>
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
            $reporterUserData = getUserData($reporterUuid); ?>
            <tr>
              <td>
                <p class="fw-normal">
                  <?php echo $reportedUserData["name"] . "(" . $reportedUserData["email"] . ")"; ?>
                </p>
              </td>
              <td>
                <p class="fw-normal">
                  <?php echo $reporterUserData["name"] . "(" . $reporterUserData["email"] . ")"; ?>
                </p>
              </td>
              <td>
                <p class="fw-normal">
                  <?php echo $row["reportedUserComment"]; ?>
                </p>
              </td>
              <td>
                <button class="tag btn btn-danger" onclick="removeReport('<?php echo $row['report_id']; ?>');">
                  Remove report
                </button>
                <form target="_blank" action="edit_user.php" method="post" class="sameline-form">
                  <input class="tag btn btn-danger" type="submit" value="Go to reported user">
                  <input type="hidden" name="uuid" value="<?php echo $row['reportedUserUuid']; ?>">
                </form>
                <form target="_blank" action="edit_user.php" method="post" class="sameline-form">
                  <input class="tag btn btn-danger" type="submit" value="Go to reporter">
                  <input type="hidden" name="uuid" value="<?php echo $row['reporterUserUuid']; ?>">
                </form>
              </td>
            </tr>
        <?php }
        } ?>
        </table>
      </main> <?php
            } else { ?>
      <main class="alert alert-primary" role="alert">
        No reports stored.
      </main>
    <?php }

            $DB = NULL; ?>
</body>

</html>