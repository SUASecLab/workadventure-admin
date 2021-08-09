<?php
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

$payload = file_get_contents("php://input");
$decodedPayload = (array) json_decode($payload);

$reportedUserUuid = htmlspecialchars($decodedPayload["reportedUserUuid"]);
$reportedUserComment = htmlspecialchars($decodedPayload["reportedUserComment"]);
$reporterUserUuid = htmlspecialchars($decodedPayload["reporterUserUuid"]);
$reportWorldSlug = htmlspecialchars($decodedPayload["reportWorldSlug"]);

reportUser($reportedUserUuid, $reportedUserComment, $reporterUserUuid, $reportWorldSlug);

$DB = NULL;
?>
