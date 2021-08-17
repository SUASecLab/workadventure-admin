<?php
header("Content-Type:application/json");
require 'database_operations.php';
require 'authentication.php';

try {
    $DB = new PDO("mysql:dbname=".getenv('DB_MYSQL_DATABASE').";host=admin-db;port=3306",
        getenv('DB_MYSQL_USER'), getenv('DB_MYSQL_PASSWORD'));
}
catch (PDOException $exception) {
    return;
}

if (!isAuthorized()) {
    http_response_code(403);
    die();
}

if (isset($_GET["uuid"])) {
    $uuid = htmlspecialchars($_GET["uuid"]);
    createAccountIfNotExistent($uuid, true);
}
$DB = NULL;
?>
