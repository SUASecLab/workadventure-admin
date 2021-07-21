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
require 'database_operations.php';

if ((isset($_GET["uuid"])) && (isset($_GET["roomId"]))) {
    $uuid = $_GET["uuid"];
    $roomId = $_GET["roomId"];

    createAccountIfNotExistent($uuid);

    $tags = getTags($uuid);

    $result['uuid'] = $uuid;
    $result['tags'] = $tags;
    $result['textures'] = array();
    $result['messages'] = array();
    $result['anonymous'] = false;

    echo json_encode($result);
} else {
    http_response_code(400);
    die();
}
?>
