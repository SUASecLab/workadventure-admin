<?php
header("Content-Type:application/json");
require_once ('../util/api_authentication.php');
require_once ('../util/api_helper_functions.php');
require_once ('../util/database_operations.php');
require_once ('../util/uuid_adapter.php');
$DB = getDatabaseHandleOrDie();
authorizeOrDie();
if ((isset($_GET["roomUrl"]) && (isset($_GET["uuid"])))) {
    // roomUrl is currently ignored
    $uuid = htmlspecialchars($_GET["uuid"]);
    $uuid = getUuid($uuid);
    if (isValidUuid($uuid)) {
        //TODO: check if name is correct
        $result = array("woka" => array("collections" => array(array("name" => "userTextures", "textures" => getTexturesWithoutLayers($uuid)))));
        echo json_encode($result);
    } else {
        http_response_code(404);
        $error["code"] = "INVALID_UUID";
        $error["title"] = "Invalid UUID";
        $error["subtitle"] = "The provided user identifier is invalid.";
        $error["details"] = "";
        $error["image"] = "";
        echo json_encode($error);
    }
} else {
    http_response_code(404);
    $error["code"] = "INSUFFICIENT_PARAMETERS";
    $error["title"] = "Insuficcient parameters";
    $error["subtitle"] = "You did not specify all required parameters.";
    $error["details"] = "";
    $error["image"] = "";
    echo json_encode($error);
}
$DB = NULL;
?>
