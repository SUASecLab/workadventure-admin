<?php
/*
fetch member data by token -> returns AdminApiData
maybe useful for generating the admin join links
currently, the uuid, the organization slug, the world slug, the room slug,
the mapUrlStart and the textures are being considered
*/
header("Content-Type:application/json");
require_once ('../util/api_authentication.php');
require_once ('../util/database_operations.php');
require_once ('../util/api_helper_functions.php');
require_once ('../util/uuid_adapter.php');
$DB = getDatabaseHandleOrDie();
authorizeOrDie();
if (isset($_GET["token"])) {
    $uuid = htmlspecialchars($_GET["token"]);
    $uuid = getUuid($uuid);
    isValidUuidOrDie($uuid);
    if (allowedToCreateNewUser()) {
        createAccountIfNotExistent($uuid);
    }
    $map = getUserStartMap($uuid);
    if ($map == NULL) {
        $map = getenv('START_ROOM_URL');
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
