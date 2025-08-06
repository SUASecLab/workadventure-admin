<?php
session_start();
if (!isset($_GET["target"])) {
    die();
}
/*
targets:
- index = show the index
- edit_user=edit a user; requires 'uuid' as post parameter
- login=log in; don't die if no user is logged in
- logout=log out
- rooms = view and manage maps
- user=show users
*/
$target = $_GET["target"];
if (gettype($target) !== "string") {
    die("Invalid data provided");
}
$target = htmlspecialchars($target);
?>
<!doctype html>
<html lang="en">
<html data-bs-theme="dark">
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
require_once ('util/uuid_adapter.php');
// Connect to database
$DB = getDatabaseHandleOrPrintError();

// Create Get token
if (isset($_SESSION["get_token"])) {
    $_SESSION["old_get_token"] = $_SESSION["get_token"];
}
$_SESSION["get_token"] = hash("sha256", generateUuid());
?>

    <nav class="container navbar navbar-expant-lg bg-body-tertiary nav">
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
        <a class="navbar-brand" href="../logout?token=<?php echo $_SESSION['get_token']; ?>" id="navLoginLogout">Log out</a>
    <?php } else { ?>
        <a class="navbar-brand" href="logout?token=<?php echo $_SESSION['get_token']; ?>" id="navLoginLogout">Log out</a>
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
    showLogin($target !== "edit_user");
    die();
}
$DB = NULL;
?>
    <main class="container">
        <article id="<?php echo $target; ?>">
        </article>

        <?php
            $darkModeSites = ["index", "edit_user", "rooms", "user"];
            if (in_array($target, $darkModeSites)) {
                ?>
                    <div id="theme-button-parent">
                        <button class="btn btn-lg btn-primary" id="theme-switch"></button>
                    </div>
                <?php
            }
        ?>
    </main>

    <script>
        // Get currently stored theme
        let theme = localStorage.getItem("theme");

        // No or invalid theme stored
        if ((theme == null) || ((theme != "light") && (theme != "dark"))) {
            theme = "dark"
            window.localStorage.setItem("theme", theme)
        }

        // Get theme switch button
        const themeButton = document.getElementById("theme-switch");

        // Enable dark mode function
        function enableDarkMode() {
            document.documentElement.setAttribute("data-bs-theme", "dark");
            window.localStorage.setItem("theme", "dark");
            themeButton.innerHTML =
                `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-sun-fill" viewBox="0 0 16 16">
                    <path d="M8 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8M8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0m0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13m8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5M3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8m10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0m-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0m9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707M4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708"/>
                </svg>`;
        }

        // Enable light mode function
        function enableLightMode() {
            document.documentElement.setAttribute("data-bs-theme", "light");
            window.localStorage.setItem("theme", "light");
            themeButton.innerHTML =
                `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-moon-stars-fill" viewBox="0 0 16 16">
                    <path d="M6 .278a.77.77 0 0 1 .08.858 7.2 7.2 0 0 0-.878 3.46c0 4.021 3.278 7.277 7.318 7.277q.792-.001 1.533-.16a.79.79 0 0 1 .81.316.73.73 0 0 1-.031.893A8.35 8.35 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71 0 4.266 2.114 1.312 5.124.06A.75.75 0 0 1 6 .278"/>
                    <path d="M10.794 3.148a.217.217 0 0 1 .412 0l.387 1.162c.173.518.579.924 1.097 1.097l1.162.387a.217.217 0 0 1 0 .412l-1.162.387a1.73 1.73 0 0 0-1.097 1.097l-.387 1.162a.217.217 0 0 1-.412 0l-.387-1.162A1.73 1.73 0 0 0 9.31 6.593l-1.162-.387a.217.217 0 0 1 0-.412l1.162-.387a1.73 1.73 0 0 0 1.097-1.097zM13.863.099a.145.145 0 0 1 .274 0l.258.774c.115.346.386.617.732.732l.774.258a.145.145 0 0 1 0 .274l-.774.258a1.16 1.16 0 0 0-.732.732l-.258.774a.145.145 0 0 1-.274 0l-.258-.774a1.16 1.16 0 0 0-.732-.732l-.774-.258a.145.145 0 0 1 0-.274l.774-.258c.346-.115.617-.386.732-.732z"/>
                </svg>`;
        }

        // Set theme
        if (theme == "dark") {
            enableDarkMode();
        } else {
            enableLightMode();
        }

        // Change theme button
        themeButton.addEventListener("click", () => {
            if (document.documentElement.getAttribute("data-bs-theme") == "light") {
                enableDarkMode();
            } else {
                enableLightMode();
            }
        });
    </script>
</body>

</html>
