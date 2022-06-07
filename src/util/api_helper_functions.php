<?php
require_once ('database_operations.php');
function getMessages($uuid) {
    $result = array();
    $privateMessagesIds = array();
    $globalMessagesIds = array();
    // get private messages
    $userMessages = getUserMessages($uuid);
    if ($userMessages != NULL) {
        while ($row = $userMessages->fetch(PDO::FETCH_ASSOC)) {
            $messageArray = array("type" => "ban", "message" => $row["message"]);
            array_push($result, $messageArray);
            array_push($privateMessagesIds, $row["message_id"]);
        }
    }
    // get global messages
    $messages = getGlobalMessages();
    if ($messages != NULL) {
        $hiddenMessagesIds = getHiddenMessagesIds($uuid);
        while ($row = $messages->fetch(PDO::FETCH_ASSOC)) {
            if (!(in_array($row["message_id"], $hiddenMessagesIds))) {
                $messageArray = array("type" => "message", "message" => htmlspecialchars_decode($row["message"]));
                array_push($result, $messageArray);
                array_push($globalMessagesIds, $row["message_id"]);
            }
        }
    }
    if (sizeof($globalMessagesIds) > 0) {
        hideGlobalMessage($uuid, $globalMessagesIds[0]);
    } else if (sizeof($privateMessagesIds) > 0) {
        removeUserMessage($privateMessagesIds[0]);
    }
    return $result;
}
function getUuid($userIdentifier) {
    if (str_contains($userIdentifier, "@")) {
        // email as unique identifier -> get uuid
        return getUuidFromEmail($userIdentifier);
    } else {
        // uuid is already our identifier
        return $userIdentifier;
    }
}
function getTextures($uuid = NULL) {
    $result = array();
    $userTags = array();
    $textures = getCustomTextures();
    if ($textures != NULL) {
        if (($uuid != NULL) && (userExists($uuid))) {
            $userTags = getTags($uuid);
        }
        while ($row = $textures->fetch(PDO::FETCH_ASSOC)) {
            $textureId = $row["texture_table_id"];
            $textureTags = getTextureTags($textureId);
            if ((count($textureTags) == 0) || (count(array_intersect($textureTags, $userTags)) > 0)) {
                $thisTexture = array("id" => $row["texture_id"], "name" => $row["texture_name"], "layer" => $row["texture_layer"], "url" => $row["texture_url"]);
                array_push($result, $thisTexture);
            }
        }
    }
    return $result;
}
// checks if the textureIDs in characterLayer are valid and returns their corresponding textures
function getTexturesIfValid($uuid, $characterLayer) {
    if (!is_array($characterLayer)){
        return [];
    }
    $result = array();
    $userTextures = getTextures($uuid);
    if (!is_array($userTextures)){
        return [];
    }
    foreach  ($characterLayer as &$textureId) {
        $found = FALSE;
        foreach ($userTextures as &$userTexture) {
            if ($userTexture["id"] == $textureId) {
                $found = TRUE;
                array_push($result, $userTexture);
                break;
            }
        }
            if (!$found) {
                return [];
            }
    }

    return $result;
}

function getTexturesWithoutLayers($uuid = NULL) {
    $result = array();
    $userTags = array();
    $textures = getCustomTextures();
    if ($textures != NULL) {
        if (($uuid != NULL) && (userExists($uuid))) {
            $userTags = getTags($uuid);
        }
        while ($row = $textures->fetch(PDO::FETCH_ASSOC)) {
            $textureId = $row["texture_table_id"];
            $textureTags = getTextureTags($textureId);
            if ((count($textureTags) == 0) || (count(array_intersect($textureTags, $userTags)) > 0)) {
                $thisTexture = array("id" => $row["texture_id"], "name" => $row["texture_name"], "url" => $row["texture_url"]);
                array_push($result, $thisTexture);
            }
        }
    }
    return $result;
}

function getAuthenticationMandatory($map) {
    $mapPolicy = getMapPolicy($map);
    return $mapPolicy != 1;
}

// checks if user is not allowed to access map
function userCanAccessMap($userUuid, $shortMapUri) {
    $policy = getMapPolicy($shortMapUri);
    if ($policy != 1) {
        // null is acceptable here, because this is not meant for security, but for UX
        // WA uses call to /map Endpoint also without userId -> need to support that
        if ($userUuid === null) {
            return true;
        }
        if (!userExists($userUuid)) {
            return false;
        }
        if ($policy == 3) {
            $sharedTagsCount = count(array_intersect(getMapTags($shortMapUri), getTags($userUuid)));
            if ($sharedTagsCount < 1) {
                return false;
            }
        }
    }
    return true;
}
