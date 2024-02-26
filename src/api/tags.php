<?php
header("Content-Type:application/json");
require_once ('../util/api_authentication.php');
require_once ('../util/api_helper_functions.php');
require_once ('../util/database_operations.php');
$DB = getDatabaseHandleOrDie();
authorizeOrDie();
if (isset($_GET["roomUrl"])) {
    error_log("gettings tags for room URL called" . $_GET["roomUrl"]);
    echo json_encode(array());
} else {
    http_response_code(404);

    echo json_encode(apiErrorMessage("INSUFFICIENT_DATA",
        "Insufficient data provided",
        "Not enough data was provided to perform this operation."));
}
$DB = NULL;
?>