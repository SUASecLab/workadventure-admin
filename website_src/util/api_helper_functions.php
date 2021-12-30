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
                $thisTexture = array("id" => (int)$row["texture_id"], "level" => (int)$row["texture_level"], "url" => "https://" . $row["url"], "rights" => $row["rights"]);
                array_push($result, $thisTexture);
            }
        }
    }
    return $result;
}
