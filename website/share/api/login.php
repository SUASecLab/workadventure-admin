<?php
/*
fetch member data by token -> returns AdminApiData
maybe useful for generating the admin join links
currently, the uuid, the organization slug, the world slug, the room slug,
the mapUrlStart and the textures are being considered
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
    $uuid = $_GET["token"];

    createAccountIfNotExistent($uuid);

    $tags = getTags($uuid);

    $result['organizationSlug'] = "hsm";
    $result['worldSlug'] = getenv('DOMAIN');
    $result['roomSlug'] = "laboratory";
    $result['mapUrlStart'] = "maps/gaming/map.json";
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
