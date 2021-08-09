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
require 'helper_functions.php';

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

if ((isset($_GET["userIdentifier"])) && (isset($_GET["roomId"])) && (isset($_GET["ipAddress"]))) {
    $userUuid = htmlspecialchars($_GET["userIdentifier"]);
    $roomId = htmlspecialchars($_GET["roomId"]);

    if (allowedToCreateNewUser()) {
        createAccountIfNotExistent($userUuid);
    }

    $result['userUuid'] = $userUuid;
    $result['tags'] = getTags($userUuid);
    $result['visitCardUrl'] = getUserVisitCardUrl($userUuid, true);
    $result['textures'] = array();
    $result['messages'] = getMessages($userUuid);
    $result['anonymous'] = !userExists($userUuid);

    echo json_encode($result);
} else {
    http_response_code(400);
    die();
}

$DB = NULL;
?>
