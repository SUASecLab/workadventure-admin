<?php
/*
verify ban -> returns AdminBannedData
only works on private maps -> '@' url part
is_banned is used, message not but most likely equal to ban message
*/
header("Content-Type:application/json");
require 'database_operations.php';

if ((isset($_GET["organization"])) && (isset($_GET["world"])) && (isset($_GET["ipAddress"])) && (isset($_GET["token"]))) {
    $organization = $_GET["organization"];
    $world = $_GET["world"];
    $ipAddress = $_GET["ipAddress"];
    $uuid = $_GET["token"];

    $result['is_banned'] = false;
    $result['message'] = array();

    echo json_encode($result);
} else {
    http_response_code(400);
    die();
}
?>
