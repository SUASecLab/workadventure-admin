<?php
header("Content-Type:application/json");
require_once ('../util/api_authentication.php');
require_once ('../util/database_operations.php');
$DB = getDatabaseHandleOrDie();
authorizeOrDie();
if (isset($_GET["roomUrl"])) {
    $maps = getAllMaps();
    $result = array();
    if ($maps != NULL) {
        foreach ($maps as $map) {
            array_push($result, "https://" . getenv('DOMAIN') . $map["mapUrl"]);
        }
    }
    echo json_encode($result);
} else {
    http_response_code(404);

    $error["code"] = "INSUFFICIENT_DATA";
    $error["title"] = "Insufficient data provided";
    $error["subtitle"] = "Not enough data was provided to perform this operation.";
    $error["details"] = "";
    $error["image"] = "";

    echo json_encode($error);
}
$DB = NULL;
?>