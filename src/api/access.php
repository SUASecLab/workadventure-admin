<?php
/*
fetch member data by uuid -> returns FetchMemberDataByUuidResponse
currently, the messages, the tags, the textures, the tags, the anonymous variable and http status codes are used
Status codes:
404 -> anonymous login
403 -> world full
tags: 'admin' for sending world messages, furthermore a jitsi moderator tag can be set (set in map, is a custom tag)
*/
header("Content-Type:application/json");
require_once ('../util/api_authentication.php');
require_once ('../util/database_operations.php');
require_once ('../util/api_helper_functions.php');
require_once ('../util/uuid_adapter.php');
$DB = getDatabaseHandleOrDie();
authorizeOrDie();
if ((isset($_GET["userIdentifier"])) && (isset($_GET["roomId"])) && (isset($_GET["ipAddress"]))) {
    $userIdentifier = htmlspecialchars($_GET["userIdentifier"]);
    $uuid = getUuid($userIdentifier);
    isValidUuidOrDie($uuid);
    if (allowedToCreateNewUser()) {
        createAccountIfNotExistent($uuid);
    }
    if((isset($_GET["characterLayers"]))) {
        $result['textures'] = getTexturesIfValid($uuid, $_GET["characterLayers"]);
    } else {
        $result['textures'] = [];
    }
    $userData = getUserData($uuid);
    $visitCardUrl = $userData["visitCardUrl"] ?? null;
    if ($visitCardUrl) {
        $visitCardUrl = 'https://' . $visitCardUrl;
    }
    $result['userUuid'] = $uuid;
    $result['email']= $userData["email"];
    $result['tags'] = getTags($uuid);
    $result['visitCardUrl'] = $visitCardUrl;
    $result['messages'] = getMessages($uuid);
    // optional parameters
    $result['anonymous'] = !userExists($uuid);
    $result['userRoomToken'] = '';
    echo json_encode($result);
} else {
    http_response_code(400);
    die();
}
$DB = NULL;
