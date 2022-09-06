<?php
header("Content-Type:application/json");
require_once ('../util/api_authentication.php');
require_once ('../util/database_operations.php');
require_once ('../util/api_helper_functions.php');
require_once ('../util/uuid_adapter.php');
$DB = getDatabaseHandleOrDie();
authorizeOrDie();
if ((isset($_GET["userIdentifier"])) && (isset($_GET["playUri"])) && (isset($_GET["ipAddress"]))) {
    //  contents of ipAdress are ignored here
    $userIdentifier = htmlspecialchars($_GET["userIdentifier"]);
    $uuid = getUuid($userIdentifier);
    if (userExists($uuid)) {
        $selectedTextures = array();
        if (isset($_GET["characterLayers"])) {
            $selectedTextures = $_GET["characterLayers"];
        } else {
            $selectedTextures = [];
        }

        $userData = getUserData($uuid);
        if ($userData === null) {
            http_response_code(403);

            $error["code"] = "NO_USERDATA";
            $error["title"] = "No user data";
            $error["subtitle"] = "No user data received.";
            $error["details"] = "";
            $error["image"] = "";

            echo json_encode($error);
            die("Could not fetch userdata");
        }

        $tags = array();
        if (array_key_exists("tags", $userData)) {
            $tags = $userData["tags"];
        }

        $result['userUuid'] = $uuid;
        $result['email']= $userData["email"];
        $result['tags'] = $tags;

        $textures =  getTextures();
        $textureArray = array();
        
        if ($textures === null) {
            http_response_code(403);

            $error["code"] = "NO_TEXTURES";
            $error["title"] = "No texture data";
            $error["subtitle"] = "No texture data received.";
            $error["details"] = "";
            $error["image"] = "";

            echo json_encode($error);
            die("Could not fetch texture data");
        }

        foreach ($textures as $texture) {
            if (in_array($texture["waId"], $selectedTextures)) {
                // check if tags are matching (if any)
                if (array_key_exists("tags", $texture)) {
                    $textureTags = $texture["tags"];
                    if ((count($textureTags) !== 0) &&
                        (count(array_intersect($tags, $textureTags)) === 0)) {
                        continue;
                    }
                }
                array_push($textureArray, array(
                    "id" => $texture["waId"],
                    "url" => $texture["url"],
                    "layer" => $texture["layer"]
                ));
            }
        }

        $result["textures"] = $textureArray;

        $visitCardUrl = NULL;
        if (array_key_exists("visitCardURL", $userData)) {
            $visitCardUrl = "https://".$userData["visitCardURL"];
        }

        $result['visitCardUrl'] = $visitCardUrl;

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
        $result['messages'] = $messages;
        removeMessages($uuid);

        // decide whether anonymous login is allowed
        $playUri = htmlspecialchars($_GET["playUri"]);
        $map = getMap(substr($playUri, strlen("https://".getenv("DOMAIN"))));

        if ($map === null) {
            http_response_code(403);

            $error["code"] = "NO_MAP";
            $error["title"] = "No map data";
            $error["subtitle"] = "No map data received.";
            $error["details"] = "";
            $error["image"] = "";

            echo json_encode($error);
            die("Could not fetch map data");
        }

        $result['anonymous'] = $map["policyNumber"] === 1; 

        // Generate user room token
        if (getenv("ENABLE_SUAS_EXTENSIONS") === "true") {
            $sidecarURL = "http://".getenv("SIDECAR_URL")."/issuance?uuid=" . $uuid;
            $sidecarResult = file_get_contents($sidecarURL);
            if ($sidecarResult === false) {
                http_response_code(403);

                $error["code"] = "NO_TOKEN";
                $error["title"] = "No auth token";
                $error["subtitle"] = "No auth token received.";
                $error["details"] = "";
                $error["image"] = "";

                echo json_encode($error);
            } else {
                $result['userRoomToken'] = ((array) json_decode($sidecarResult, true))["token"]; // WA.player.userRoomToken
                echo json_encode($result);
            }
        } else {
            echo json_encode($result);
        }
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
