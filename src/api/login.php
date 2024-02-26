<?php
header("Content-Type:application/json");

// include necessary helper functions
require_once ('../util/api_authentication.php');
require_once ('../util/database_operations.php');
require_once ('../util/api_helper_functions.php');
require_once ('../util/uuid_adapter.php');

// set up DB handle
$DB = getDatabaseHandleOrDie();

// authorize user
authorizeOrDie();

//token = organizationMemberToken = user UUID
if (isset($_GET["token"])) {
    // get user UUID
    $uuid = htmlspecialchars($_GET["token"]);
    $uuid = getUuid($uuid);

    // check if user exists
    if ((!isValidUuid($uuid)) || (!userExists($uuid))) {
        http_response_code(401);

        echo json_encode(apiErrorMessage("UNAUTHORIZED", "Unauthorized",
            "You are not allowed to use this service."));
        die();
    }

    // fetch user data
    $userData = getUserData($uuid);
    if ($userData === null) {
        http_response_code(403);

        echo json_encode(apiErrorMessage("NO_USERDATA",
            "No user data", "No user data received."));
        die("Could not fetch userdata");
    }
    
    // figure out whether user has start room differing from preset one
    if (array_key_exists("startMap", $userData)) {
        $map = getMap($userData["startMap"]);
    } else {
        $map = getMap(getenv('START_ROOM_URL'));
    }

    // send data
    echo json_encode(array(
        "userUuid" => $uuid,
        "email" => $userData["email"],
        "roomUrl" => $map["wamUrl"],
        "mapUrlStart" => $map["mapUrl"],
        "messages" => array(), // messages are being sent when calling the access function
    ));
} else {
    // return error message if no uuid present
    http_response_code(400);

    echo json_encode(apiErrorMessage("INSUFFICIENT_USER_INFORMATION",
        "Insufficient user information", "You did not specify enough information about the user."));
}
$DB = NULL;
?>
