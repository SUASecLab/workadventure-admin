<?php
/*
fetch member data by uuid -> returns FetchMemberDataByUuidResponse
currently, the messages, the tags, the textures, the tags, the anonymous variable and http status codes are used
Status codes:
404 -> anonymous login
403 -> world full
tags: 'admin' for sending world messages, furthermore a jitsi moderator tag can be set (set in map, is a custom tag)
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

if ((isset($_GET["uuid"])) && (isset($_GET["roomId"]))) {
    $uuid = htmlspecialchars($_GET["uuid"]);
    $roomId = htmlspecialchars($_GET["roomId"]);

    createAccountIfNotExistent($uuid);

    $tags = getTags($uuid);

    $result['uuid'] = $uuid;
    $result['tags'] = $tags;
    $result['visitCardUrl'] = NULL;
    $result['textures'] = array();
    $result['messages'] = array();
    $result['anonymous'] = false;

    echo json_encode($result);
} else {
    http_response_code(400);
    die();
}

$DB = NULL;
?>
