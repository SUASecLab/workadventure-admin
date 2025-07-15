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
// Check CSRF data
isCSRFDataValidOrDie();
?>
  <main>
    <div class="row">
      <div class="col-sm-6">
        <div class="card card-style">
          <div class="card-body">
            <h5 class="card-title">Room Management</h5>
            <p class="card-text">Room settings can be adjusted here</p>
            <a href="rooms" class="btn btn-primary">Go to Room Management</a>
          </div>
        </div>
      </div>
      <div class="col-sm-6">
        <div class="card card-style">
          <div class="card-body">
            <h5 class="card-title">User Management</h5>
            <p class="card-text">User accounts can be managed here</p>
            <a href="user" class="btn btn-primary">Go to User Management</a>
          </div>
        </div>
      </div>
    </div>
  </main>
</body>

</html>

<?php $DB = NULL; ?>
