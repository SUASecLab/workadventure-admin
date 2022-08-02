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
include_once ('../util/uuid_adapter.php');
// Get number of users
$nrOfUsers = getNumberOfUsers();
if ($nrOfUsers === NULL) {
?>
    <aside class="container alert alert-danger" role="alert">
      <p>Could not fetch user count</p>
    </aside>
  <?php
    $DB = NULL;
    die();
} ?>

  <main>
    <p class="fs-3">
      Listing <?php echo $nrOfUsers; ?> accounts
    </p>
    <?php
// Get all users
$users = iterator_to_array(getAllUsers());
if ($users === NULL) {
?>
      <aside class="alert alert-danger" role="alert">
        <p>Could not fetch users</p>
      </aside>
    <?php
    $DB = NULL;
    die();
}
// Display all accounts

?>
    <article>
      <table class="table">
        <tr>
          <th scope="col">Name</th>
          <th scope="col">Tags</th>
          <th scope="col">Actions</th>
        </tr>

        <?php
foreach ($users as $user) { ?>
          <tr>
            <td>
              <p class="fw-normal">
                <?php echo $user["name"]; ?>
              </p>
            </td>
            <td>
<?php 
  if (array_key_exists("tags", iterator_to_array($user))) {
  $tags = $user["tags"];
    foreach ($tags as $currentTag) {
        if (strlen(trim($currentTag)) > 0) {?>
            <div class="badge rounded-pill bg-primary tag">
    <?php echo $currentTag; ?>
            </div> <?php
        }
    }
  }
?>
            </td>
            <td>
              <a class="btn btn-dark" role="button" href="./edit/<?php echo $user["uuid"]; ?>">Edit</a>
            </td>

          <?php
}
?>
      </table>
      <a class="btn btn-primary" role="button" href="./edit/<?php echo generateUuid(); ?>">Create new user</a>
    </article>
  </main>
</body>

</html>
<?php $DB = NULL; ?>
