<?php

/*
report player -> returns ?
no result object -> not used by the client
does not seem to work properly as of now
*/
header("Content-Type:application/json");
require 'authentication.php';
require 'database_operations.php';

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

if ((isset($_POST["reportedUserUuid"])) && (isset($_POST["reportedUserComment"])) && (isset($_POST["reporterUserUuid"])) && (isset($_POST["reportWorldSlug"]))) {
    $reportedUserUuid = htmlentities($_POST["reportedUserUuid"]);
    $reportedUserComment = htmlentities($_POST["reportedUserComment"]);
    $reporterUserUuid = htmlentities($_POST["reporterUserUuid"]);
    $reportWorldSlug = htmlentities($_POST["reportWorldSlug"]);
    reportUser($reportedUserUuid, $reportedUserComment, $reporterUserUuid, $reportWorldSlug);
} else {
    http_response_code(400);
    die();
}

$DB = NULL;
?>
