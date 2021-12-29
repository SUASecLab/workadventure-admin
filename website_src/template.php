<?php
session_start();
if (!isset($_GET["target"])) {
    die();
}
/*
targets:
- edit_user=edit a user; reuires 'uuid' as post parameter
- login=log in; don't die if no user is logged in
- messages=send global messages, requires quill
- reports = view user reports
- rooms = view and manage maps
- settings = adjust settings
- textures = manage custom textures
- index = show the index
*/
$target = htmlspecialchars($_GET["target"]);
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="/css/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="/css/style.css" rel="stylesheet">
    <script src="/js/bootstrap/bootstrap.min.js"></script>
    <script src="/js/ajax/jquery-3.6.0.min.js"></script>
    <script src="/js/snippets/<?php echo $target; ?>.js"></script>

    <?php
if ($target == "messages") {
?>
        <link href="/css/quill/quill.snow.css" rel="stylesheet">
        <script src="/js/quill/quill.min.js"></script>
    <?php
}
?>

    <title>WorkAdventure Administration</title>
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
            <a class="navbar-brand" href="/">WorkAdventure Administration</a>
            <?php
if (isLoggedIn()) {
?>
                <a class="navbar-brand" href="/logout">Log out</a>
            <?php
} else { ?>
                <a class="navbar-brand" href="/login">Log in</a>
            <?php
} ?>
        </div>
    </nav>

    <?php
if (($target != "login") && (!isLoggedIn())) {
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