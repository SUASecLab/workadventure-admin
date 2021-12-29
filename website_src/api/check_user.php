<?php
/*
fetch check user by token -> returns AdminApiData
as of now, only the uuid and the http status code of the result are being evaluated
if this API method is being called
*/
header("Content-Type:application/json");
require_once ('../util/api_authentication.php');
require_once ('../util/database_operations.php');
require_once ('../util/api_helper_functions.php');
$DB = getDatabaseHandleOrDie();
authorizeOrDie();
if (isset($_GET["token"])) {
    $uuid = htmlspecialchars($_GET["token"]);
    $uuid = getUuid($uuid);
    $map = getenv('START_ROOM_URL');
    if (allowedToCreateNewUser()) {
        createAccountIfNotExistent($uuid);
    }
    $result['roomUrl'] = $map;
    $result['email'] = getUserEmail($uuid);
    $result['mapUrlStart'] = getMapFileUrl($map);
    $result['tags'] = getTags($uuid);
    $result['policy_type'] = getMapPolicy($map);
    $result['userUuid'] = $uuid;
    $result['messages'] = array(); // messages are being sent when calling the access function
    $result['textures'] = getTextures($uuid);
    echo json_encode($result);
} else {
    die();
}
$DB = NULL;
?>