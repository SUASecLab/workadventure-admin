<?php
header("Content-Type:application/json");
require_once ('../util/api_authentication.php');
require_once ('../util/api_helper_functions.php');
require_once ('../util/database_operations.php');
require_once ('../util/uuid_adapter.php');
$DB = getDatabaseHandleOrDie();
/**
 * @param string $layer The layer for which the tags should be fetched
 * @param string[] $userTags The tags the user has
 * @return array<int, array{"id": string, "name": string, "url": string, "position": int}>
 */
function generateTextureInformationPerLayer(string $layer, array $userTags): array {
    $layerList = getTexturesByLayer($layer);
    $layerTextures = array();
    $layerIndex = 0;

    if ($layerList === null) {
        http_response_code(403);

        echo json_encode(apiErrorMessage("NO_TEXTURES",
            "No texture data",
            "No texture data received."));
        die("Could not fetch texture data");
    }

    foreach($layerList as $texture) {
        $textureTags = array();
        if (array_key_exists("tags", $texture)) {
            $textureTags = $texture["tags"];
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
        $userData = getUserData($uuid);
        $userTags = array();

        if ($userData === null) {
            http_response_code(403);

            echo json_encode(apiErrorMessage("NO_USERDATA",
                "No user data", "No user data received."));
            die("Could not fetch userdata");
        }

        if (array_key_exists("tags", $userData)) {
            $userTags = $userData["tags"];
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

        echo json_encode(apiErrorMessage("INVALID_UUID",
            "Invalid UUID",
            "The provided user identifier is invalid."));
    }
} else {
    http_response_code(404);

    echo json_encode(apiErrorMessage("INSUFFICIENT_PARAMETERS",
        "Insuficcient parameters",
        "You did not specify all required parameters."));
}
$DB = NULL;
?>
