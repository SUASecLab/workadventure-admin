<?php
/*
fetch map details -> returns MapDetailsData
currently, the tags and the policy type are used
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
    error_log($exception);
    return;
}

if (!isAuthorized()) {
    http_response_code(403);
    die();
}

if (isset($_GET["playUri"])) {
    $playUri = htmlspecialchars($_GET["playUri"]);

    $shortUri = substr($playUri, strlen("https://".getenv('DOMAIN')));
    $resultMap = getMapFileUrl($shortUri);

    if ($resultMap == NULL) {
        http_response_code(404);
        die();
    }

    $result['policy_type'] = getMapPolicy($shortUri);
    $result['mapUrl'] =  $resultMap;
    $result['tags'] = getMapTags($shortUri);
    $result['textures'] = getTextures();

    echo json_encode($result);
} else {
    http_response_code(400);
    die();
}

$DB = NULL;
?>
