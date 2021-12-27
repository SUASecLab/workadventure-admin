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
        <script src="js/ajax/jquery-3.6.0.min.js"></script>
        <script src="js/ajax/textures.js"></script>

        <title>Workadventure Administration</title>
    </head>
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
            <aside class="container alert alert-danger role=alert">
                Could not connect to database: "<?php echo $exception->getMessage(); ?>
            </aside>
        <?php    die();
        }
        require_once('api/database_operations.php');
        require_once('login_functions.php');
        require('meta/toolbar.php');

        if (!isLoggedIn()) {
            showLogin();
            die();
        }

        $DB = NULL;
    ?>

    <main class="container">
        <!-- ajax-->
        <article id="textures">
        </article>
    </main>
    </body>
</html>