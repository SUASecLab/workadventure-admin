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

if (isset($_GET["playUri"])) {
    $playUri = htmlspecialchars($_GET["playUri"]);

    $result['policy_type'] = 1;
    $result['tags'] = array();
    $result['textures'] = array();

    if ($playUri == "https://lab.itsec.hs-sm.de/@/org/lab.itsec.hs-sm.de/laboratory") {
        $result['mapUrl'] =  "https://lab.itsec.hs-sm.de/maps/hsm/work/map.json";
    } else if ($playUri == "https://lab.itsec.hs-sm.de/@/org/lab.itsec.hs-sm.de/relaxation") {
        $result['mapUrl'] =  "https://lab.itsec.hs-sm.de/maps/hsm/gaming/map.json";
    } else {
        http_response_code(404);
        die();
    }

    echo json_encode($result);
} else {
    http_response_code(400);
    die();
}

$DB = NULL;
?>
