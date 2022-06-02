<?php
header("Content-Type:application/json");
require_once ('../util/api_authentication.php');
require_once ('../util/api_helper_functions.php');
require_once ('../util/database_operations.php');
require_once ('../util/uuid_adapter.php');
$DB = getDatabaseHandleOrDie();
authorizeOrDie();
if ((isset($_GET["roomUrl"]) && (isset($_GET["uuid"])))) {
    $uuid = htmlspecialchars($_GET["uuid"]);
    $uuid = getUuid($uuid);
    isValidUuidOrDie($uuid);
    $result = array("woka" => array("collections" => array(array("name" => "userTextures", "textures" => getTexturesWithoutLayers($uuid)))));
    echo json_encode($result);
} else {
    die();
}
$DB = NULL;
?>
