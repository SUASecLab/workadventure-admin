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
if ((isset($_GET["userIdentifier"])) && (isset($_GET["playUri"])) && (isset($_GET["ipAddress"]))) {
    //  contents of playUri, ipAdress are ignored here
    $userIdentifier = htmlspecialchars($_GET["userIdentifier"]);
    $uuid = getUuid($userIdentifier);
    if (userExists($uuid)) {
        if (allowedToCreateNewUser()) {
            createAccountIfNotExistent($uuid);
        }
        if((isset($_GET["characterLayers"]))) {
            $result['textures'] = getTexturesIfValid($uuid, $_GET["characterLayers"]);
        } else {
            $result['textures'] = [];
        }
        $result['userUuid'] = $uuid;
        $result['email']= getUserEmail($uuid);
        $result['tags'] = getTags($uuid);
        $result['visitCardUrl'] = getUserVisitCardUrl($uuid, true);
        $result['messages'] = getMessages($uuid);
        // optional parameters
        $result['anonymous'] = !getAuthenticationMandatory($uuid);
        //$result['userRoomToken'] = '';
        echo json_encode($result);
    } else {
        http_response_code(403);

        $error["code"] = "INVALID_USER";
        $error["title"] = "Invalid user";
        $error["subtitle"] = "The specified user does not exist.";
        $error["details"] = "";
        $error["image"] = "";

        echo json_encode($error);
    }
} else {
    http_response_code(403);

    $error["code"] = "INSUFFICIENT_USER_INFORMATION";
    $error["title"] = "Insufficient user information";
    $error["subtitle"] = "You did not specify enough information about the user.";
    $error["details"] = "";
    $error["image"] = "";

    echo json_encode($error);
}
$DB = NULL;
?>