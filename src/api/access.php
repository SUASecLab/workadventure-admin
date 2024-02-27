<?php
header("Content-Type:application/json");
require_once ("../util/api_authentication.php");
require_once ("../util/database_operations.php");
require_once ("../util/api_helper_functions.php");
require_once ("../util/uuid_adapter.php");
$DB = getDatabaseHandleOrDie();
authorizeOrDie();
if ((isset($_GET["userIdentifier"])) && (isset($_GET["playUri"])) && (isset($_GET["ipAddress"]))) {
    //  contents of ipAdress are ignored here
    $userIdentifier = htmlspecialchars($_GET["userIdentifier"]);
    $uuid = getUuid($userIdentifier);

    /* validation fields */
    $isCharacterTexturesValid = true;
    $isCompanionTextureValid = true;

    if (userExists($uuid)) {
        $selectedTextures = array();
        if (isset($_GET["characterTextureIds"])) {
            $selectedTextures = $_GET["characterTextureIds"];
        } else {
            $selectedTextures = [];
            $isCharacterTexturesValid = false;
        }

        $userData = getUserData($uuid);
        if ($userData === null) {
            error_log("Could not fetch userdata for user ". $uuid);
            die();
        }

        $tags = array();
        if (array_key_exists("tags", $userData)) {
            $tags = $userData["tags"];
        }

        // basic user data
        $result["userUuid"] = $uuid;
        $result["email"]= $userData["email"];
        $result["tags"] = $tags;
        //$result["username"] = null; // not used by us

        $textures =  getTextures();
        $textureArray = array();
        
        if ($textures === null) {
            error_log("No texture data stored");
            die();
        }

        foreach ($textures as $texture) {
            if (in_array($texture["waId"], $selectedTextures)) {
                // check if tags are matching (if any)
                if (array_key_exists("tags", $texture)) {
                    $textureTags = $texture["tags"];
                    if ((count($textureTags) !== 0) &&
                        (count(array_intersect($tags, $textureTags)) === 0)) {
                            $isCharacterTexturesValid = false;
                            continue;
                    }
                }
                array_push($textureArray, array(
                    "id" => $texture["waId"],
                    "url" => $texture["url"],
                    //"layer" => $texture["layer"]
                ));
            }
        }

        $result["characterTextures"] = $textureArray;


        if (isset($_GET["companionTextureId"])) {
            $selectedCompanion = $_GET["companionTextureId"]; // = id in companions
            $companion = getCompanion($selectedCompanion);
            if ($companion === null) {
                $isCompanionTextureValid = false;
            } else {
                $result["companionTexture"] = array(
                    "id" => $companion["id"],
                    "url" => $companion["url"]
                );
            }
        } else {
            $result["companionTexture"] = null;
        }
        $visitCardUrl = NULL;
        if (array_key_exists("visitCardURL", $userData)) {
            $visitCardUrl = "https://".$userData["visitCardURL"];
        }

        $result["visitCardUrl"] = $visitCardUrl;

        // fetch messages
        $messages = array();

        if (array_key_exists("messages", $userData)) {
            $userMessages = $userData["messages"];
            foreach($userMessages as $message) {
                $currentMessage = array(
                    "type" => "ban",
                    "message" => $message
                );
                array_push($messages, $currentMessage);
            }
        }
        $result["messages"] = $messages;
        removeMessages($uuid);

        // decide whether anonymous login is allowed
        $playUri = htmlspecialchars($_GET["playUri"]);
        $map = getMap(substr($playUri, strlen("https://".getenv("DOMAIN"))));

        if ($map === null) {
            http_response_code(403);

            echo json_encode(apiErrorMessage("NO_MAP",
                "No map data", "No map data received."));
            die("Could not fetch map data");
        }

        $result["anonymous"] = $map["policyNumber"] === 1;
        $result["canEdit"] = false;
        $result["activatedInviteUser"] = false;
        $result["applications"] = null; // not used by us

        // XMPP
        $result["jabberId"] = null; // not used by us
        $result["jabberPassword"] = null; // not used by us
        //$result["mucRooms"] = array(array(
        //    "name" => $map["mapUrl"],
        //    "url" =>  "https://".$map["mapUrl"],
        //    "type" => "live"
        //));
        // send empty arrays -> no rooms, this variable is not nullable even if stated otherwise in documentation
        $result["mucRooms"] = array();

        // textures and companion
        $result["isCharacterTexturesValid"] = $isCharacterTexturesValid;
        $result["isCompanionTextureValid"] = $isCompanionTextureValid;
        $result["status"] = "ok";

        // Generate user room token
        if (getenv("ENABLE_SUAS_EXTENSIONS") === "true") {
            $sidecarURL = "http://".getenv("SIDECAR_URL")."/issuance?uuid=" . $uuid;
            $sidecarResult = file_get_contents($sidecarURL);
            if ($sidecarResult === false) {
                $result["status"] = "error";
                error_log("No auth token received.");
            } else {
                $result["userRoomToken"] = ((array) json_decode($sidecarResult, true))["token"]; // WA.player.userRoomToken
                echo json_encode($result);
            }
        } else {
            echo json_encode($result);
        }
    } else {
        http_response_code(403);
        error_log("Invalid user specified");
    }
} else {
    http_response_code(403);
    error_log("Invalid user specified");
}
$DB = NULL;
?>
