<?php
session_start();
?>
<!doctype html>
<html lang="en">

<body>
  <?php
require_once ('../util/database_operations.php');
require_once ('../util/web_login_functions.php');
// Connect to database
$DB = getDatabaseHandleOrPrintError();
if (!isLoggedIn()) {
    $DB = NULL;
    die();
}
if (isset($_POST["reportId"])) {
    $reportId = htmlspecialchars($_POST["reportId"]);
    if (removeReport($reportId)) { ?>
      <aside class="alert alert-success" role="alert">
        Removed report.
      </aside>
    <?php
    } else { ?>
      <aside class="alert alert-danger" role="alert">
        Could not remove report.
      </aside>
    <?php
    }
}
if (reportsStored()) {
    $reports = getReports();
    if ($reports == NULL) { ?>
      <aside class="alert alert-danger" role="alert">
        Could not load reports.
      </aside>
    <?php
    } else { ?>
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
                <a class="tag btn btn-danger" role="button" target="_blank" href="/edit/<?php echo $row['reportedUserUuid']; ?>">Go to reported user</a>
                <a class="tag btn btn-danger" role="button" target="_blank" href="/edit/<?php echo $row['reporterUserUuid']; ?>">Go to reporter</a>
              </td>
            </tr>
        <?php
        }
    } ?>
        </table>
      </main> <?php
} else { ?>
      <main class="alert alert-primary" role="alert">
        No reports stored.
      </main>
    <?php
}
$DB = NULL; ?>
</body>

</html>