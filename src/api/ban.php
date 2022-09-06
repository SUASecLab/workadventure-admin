<?php
header("Content-Type:application/json");
require_once ('../util/api_authentication.php');
require_once ('../util/database_operations.php');
require_once ('../util/api_helper_functions.php');
require_once ('../util/uuid_adapter.php');
$DB = getDatabaseHandleOrDie();
authorizeOrDie();
if ((isset($_GET["ipAddress"])) && (isset($_GET["token"])) && (isset($_GET["roomUrl"]))) {
    $uuid = htmlspecialchars($_GET["token"]);
    $uuid = getUuid($uuid);
    isValidUuidOrDie($uuid);
    $userData = iterator_to_array(getUserData($uuid));

    $isBanned = false;
    if ((array_key_exists("banned", $userData)) && ($userData["banned"] === true)) {
        $isBanned = true;
    }

    $result['is_banned'] = $isBanned;
    echo json_encode($result);
} else {
    http_response_code(404);

    $error["code"] = "INSUFFICIENT_USER_INFORMATION";
    $error["title"] = "Insufficient user information";
    $error["subtitle"] = "You did not specify enough information about the user.";
    $error["details"] = "";
    $error["image"] = "";

    echo json_encode($error);
}
$DB = NULL;
?>