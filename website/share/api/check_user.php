<?php
/*
fetch check user by token -> returns AdminApiData
as of now, only the uuid and the http status code of the result are being evaluated
if this API method is being called
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

if (isset($_GET["token"])) {
    $uuid = htmlspecialchars($_GET["token"]);

    createAccountIfNotExistent($uuid);

    $tags = getTags($uuid);

    $result['organizationSlug'] = "org";
    $result['worldSlug'] = getenv('DOMAIN');
    $result['roomSlug'] = "laboratory";
    $result['mapUrlStart'] = "https://lab.itsec.hs-sm.de/maps/hsm/work/map.json";
    $result['tags'] = $tags;
    $result['policy_type'] = 1;
    $result['userUuid'] = $uuid;
    $result['messages'] = array();
    $result['textures'] = array();

    echo json_encode($result);
} else {
    http_response_code(400);
    die();
}

$DB = NULL;
?>
