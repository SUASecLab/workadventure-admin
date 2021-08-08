<?php
require_once 'database_operations.php';

function getMessages($uuid) {
    $result = array();
    $privateMessagesIds = array();
    $globalMessagesIds = array();

    // get private messages
    $userMessages = getUserMessages($uuid);
    while($row = $userMessages->fetch(PDO::FETCH_ASSOC)) {
        $messageArray = array(
            "type" => "ban",
            "message" => $row["message"]
        );
        array_push($result, $messageArray);
        array_push($privateMessagesIds, $row["message_id"]);
    }

    // get global messages
    $messages = getGlobalMessages();
    $hiddenMessagesIds = getHiddenMessagesIds($uuid);
    while($row = $messages->fetch(PDO::FETCH_ASSOC)) {
        if (!(in_array($row["message_id"], $hiddenMessagesIds))) {
            $messageArray = array(
                "type" => "message",
                "message" => json_encode(array("ops" => array(array("insert" => $row["message"]))))
            );
            array_push($result, $messageArray);
            array_push($globalMessagesIds, $row["message_id"]);
        }
    }

    if (sizeof($globalMessagesIds) > 0) {
        hideGlobalMessage($uuid, $globalMessagesIds[0]);
    } else {
        removeUserMessage($privateMessagesIds[0]);
    }
    return $result;
}
?>
