<?php
/*
fetch map details -> returns MapDetailsData
currently, the tags and the policy type are used
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

if ((isset($_GET["organizationSlug"])) && (isset($_GET["worldSlug"])) && (isset($_GET["roomSlug"]))) {
    $organizationSlug = $_GET["organizationSlug"];
    $worldSlug = $_GET["worldSlug"];
    $roomSlug = $_GET["roomSlug"];

    $result['roomSlug'] = $roomSlug;
    $result['mapUrl'] =  "https://lab.itsec.hs-sm.de/maps/hsm/work/map.json";
    $result['policy_type'] = 1;
    $result['tags'] = array();

    echo json_encode($result);
} else {
    http_response_code(400);
    die();
}

$DB = NULL;
?>