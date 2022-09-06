<?php
header("Content-Type:application/json");
require_once ('../util/api_authentication.php');
require_once ('../util/api_helper_functions.php');
require_once ('../util/database_operations.php');
require_once ('../util/uuid_adapter.php');
$DB = getDatabaseHandleOrDie();
function generateTextureInformationPerLayer($layer, $userTags) {
    $layerList = iterator_to_array(getTexturesByLayer($layer));
    $layerTextures = array();
    $layerIndex = 0;

    foreach($layerList as $texture) {
        $textureTags = array();
        $texture = iterator_to_array($texture);
        if (array_key_exists("tags", $texture)) {
            $textureTags = iterator_to_array($texture["tags"]);
            // check, if there is a match between user and map tags
            // if not, continue, so that the texture is not added
            if  ((count($textureTags) !== 0) &&
                (count(array_intersect($textureTags, $userTags)) === 0)) {
                continue;
            }
        }

        $finalTexture = array(
            "id" => $texture["waId"],
            "name" => $texture["waId"],
            "url" => $texture["url"],
            "position" => $layerIndex
        );
        array_push($layerTextures, $finalTexture);
        $layerIndex++;
    }

    return $layerTextures;
}

authorizeOrDie();
if ((isset($_GET["roomUrl"]) && (isset($_GET["uuid"])))) {
    // roomUrl is currently ignored
    $uuid = htmlspecialchars($_GET["uuid"]);
    $uuid = getUuid($uuid);
    if (isValidUuid($uuid)) {
        $userData = iterator_to_array(getUserData($uuid));
        $userTags = array();

        if (array_key_exists("tags", $userData)) {
            $userTags = iterator_to_array($userData["tags"]);
        }

        $result = array(
            "woka" => array(
                "collections" => array(array(
                    "name" => "default",
                    "position" => 0,
                    "textures" => generateTextureInformationPerLayer("woka", $userTags)
                ))),
            "body" => array(
                "required" => true,
                "collections" => array(array(
                    "name" => "default",
                    "position" => 0,
                    "textures" => generateTextureInformationPerLayer("body", $userTags)
                ))),
            "eyes" => array(
                "required" => true,
                "collections" => array(array(
                    "name" => "default",
                    "position" => 0,
                    "textures" => generateTextureInformationPerLayer("eyes", $userTags)
                ))),
            "hair" => array(
                "collections" => array(array(
                    "name" => "default",
                    "position" => 0,
                    "textures" => generateTextureInformationPerLayer("hair", $userTags)
                ))),
            "clothes" => array(
                "collections" => array(array(
                    "name" => "default",
                    "position" => 0,
                    "textures" => generateTextureInformationPerLayer("clothes", $userTags)
                ))),
            "hat" => array(
                "collections" => array(array(
                    "name" => "default",
                    "position" => 0,
                    "textures" => generateTextureInformationPerLayer("hat", $userTags)
                ))),
            "accessory" => array(
                "required" => true,
                    "collections" => array(array(
                        "name" => "default",
                        "position" => 0,
                        "textures" => generateTextureInformationPerLayer("accessory", $userTags)
                    )))
        );
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
