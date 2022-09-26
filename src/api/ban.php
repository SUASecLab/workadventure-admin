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
    $userData = getUserData($uuid);
    
    if ($userData === null) {
        http_response_code(403);

        echo json_encode(apiErrorMessage("NO_USERDATA",
            "No user data", "No user data received."));
        die("Could not fetch userdata");
    }

    $isBanned = false;
    if ((array_key_exists("banned", $userData)) && ($userData["banned"] === true)) {
        $isBanned = true;
    }

    $result['is_banned'] = $isBanned;
    echo json_encode($result);
} else {
    http_response_code(404);

    echo json_encode(apiErrorMessage("INSUFFICIENT_USER_INFORMATION",
        "Insufficient user information",
        "You did not specify enough information about the user."));
}
$DB = NULL;
?>