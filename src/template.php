<?php
session_start();
if (!isset($_GET["target"])) {
    die();
}
/*
targets:
- edit_user=edit a user; reuires 'uuid' as post parameter
- login=log in; don't die if no user is logged in
- rooms = view and manage maps
- index = show the index
*/
$target = htmlspecialchars($_GET["target"]);
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php if ($target !== "edit_user") { ?>
        <link href="css/bootstrap/bootstrap.min.css" rel="stylesheet">
        <link href="css/style.css" rel="stylesheet">
        <script src="js/bootstrap/bootstrap.bundle.min.js"></script>
        <script src="js/ajax/jquery.min.js"></script>
        <script src="js/snippets/<?php echo $target; ?>.js"></script>
    <?php } else { ?>
        <link href="../css/bootstrap/bootstrap.min.css" rel="stylesheet">
        <link href="../css/style.css" rel="stylesheet">
        <script src="../js/bootstrap/bootstrap.bundle.min.js"></script>
        <script src="../js/ajax/jquery.min.js"></script>
        <script src="../js/snippets/<?php echo $target; ?>.js"></script>
    <?php }

$enableSUASExtensions = getenv("ENABLE_SUAS_EXTENSIONS") === "true"; ?>
    <title><?php echo ($enableSUASExtensions ? "SUASecLab" : "WorkAdventure"); ?> Administration</title>
</head>

<body>
    <?php
require_once ('util/database_operations.php');
require_once ('util/web_login_functions.php');
// Connect to database
$DB = getDatabaseHandleOrPrintError();
?>

    <nav class="container navbar navbar-expant-lg navbar-light bg-light">
        <div class="container-fluid">
            <div id="navbarMain">
                <?php if ($target === "edit_user") { ?>
                    <a class="navbar-brand" href="../"><?php echo ($enableSUASExtensions ? "SUASecLab" : "WorkAdventure"); ?> Administration</a>
                <?php } else { ?>
                    <a class="navbar-brand" href="./"><?php echo ($enableSUASExtensions ? "SUASecLab" : "WorkAdventure"); ?> Administration</a>
                <?php } ?>
            </div>
            <?php
if (isLoggedIn()) {
    if ($target === "edit_user") { ?>
        <a class="navbar-brand" href="../logout" id="navLoginLogout">Log out</a>
    <?php } else { ?>
        <a class="navbar-brand" href="logout" id="navLoginLogout">Log out</a>
    <?php }
} else {
    if ($target === "edit_user") { ?>
        <a class="navbar-brand" href="../login" id="navLoginLogout">Log in</a>
    <?php } else { ?>
        <a class="navbar-brand" href="login" id="navLoginLogout">Log in</a>
    <?php } 
} ?>
        </div>
    </nav>

    <?php
if (($target !== "login") && (!isLoggedIn())) {
    showLogin();
    die();
}
$DB = NULL;
?>
    <main class="container">
        <article id="<?php echo $target; ?>">
        </article>
    </main>
</body>

</html>
