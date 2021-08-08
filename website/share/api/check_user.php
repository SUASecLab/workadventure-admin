<?php
/*
fetch check user by token -> returns AdminApiData
as of now, only the uuid and the http status code of the result are being evaluated
if this API method is being called
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

if (isset($_GET["token"])) {
    $uuid = htmlspecialchars($_GET["token"]);
    $map = getenv('START_ROOM_URL');

    if (allowedToCreateNewUser()) {
        createAccountIfNotExistent($uuid);
    }

    $result['roomUrl'] = $map;
    $result['email'] = NULL;
    $result['mapUrlStart'] = getMapFileUrl($map);
    $result['tags'] = getTags($uuid);
    $result['policy_type'] = getMapPolicy($map);
    $result['userUuid'] = $uuid;
    $result['messages'] = array(); // messages are being sent when caling the access function
    $result['textures'] = array();

    echo json_encode($result);
} else {
    http_response_code(400);
    die();
}

$DB = NULL;
?>
