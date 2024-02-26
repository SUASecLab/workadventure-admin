<?php
header("Content-Type:application/json");

// include helper functions
require_once ('../util/api_authentication.php');
require_once ('../util/api_helper_functions.php');
require_once ('../util/database_operations.php');

// set up database handle
$DB = getDatabaseHandleOrDie();

// authorice user
authorizeOrDie();

// figure out if URL of current room is set
if (isset($_GET["roomUrl"])) {
    // get all maps and store them in an array
    $allMaps = getAllMaps();
    $result = array();

    if ($allMaps !== NULL) {
        foreach ($allMaps as $nextMap) {
            $map = (array) $nextMap;
            $currentMap = array(
                "name" => "https://" . getenv("DOMAIN") . $map["wamUrl"],
                "url" => "https://" . getenv("DOMAIN") . $map["wamUrl"],
                "roomUrl" => "https://" . getenv("DOMAIN") . $map["wamUrl"],
            );
            array_push($result, $currentMap);
        }
    }
    error_log(json_encode($result));
    echo json_encode($result);
} else {
    // return error if no room was set
    http_response_code(400);

    echo json_encode(apiErrorMessage("INSUFFICIENT_DATA",
        "Insufficient data provided",
        "Not enough data was provided to perform this operation."));
}
$DB = NULL;
?>