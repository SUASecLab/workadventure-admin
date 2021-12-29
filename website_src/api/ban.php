<?php
/*
verify ban -> returns AdminBannedData
only works on private maps -> '@' url part
is_banned is used, message not but most likely equal to ban message
*/
header("Content-Type:application/json");
require_once ('../util/api_authentication.php');
require_once ('../util/database_operations.php');
require_once ('../util/api_helper_functions.php');
$DB = getDatabaseHandleOrDie();
authorizeOrDie();
if ((isset($_GET["ipAddress"])) && (isset($_GET["token"])) && (isset($_GET["roomUrl"]))) {
    $uuid = htmlspecialchars($_GET["token"]);
    $uuid = getUuid($uuid);
    // find out whether user is banned
    $isBanned = isBanned($uuid);
    $result['is_banned'] = $isBanned;
    // get ban message if user is banned
    if ($isBanned) {
        $result['message'] = getBanMessage($uuid);
    } else {
        $result['message'] = "";
    }
    echo json_encode($result);
} else {
    die();
}
$DB = NULL;
?>