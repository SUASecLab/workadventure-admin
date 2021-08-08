<?php
require_once 'database_operations.php';

function getMessages($uuid) {
    $result = array();

    // get private messages
    $userMessages = getUserMessages($uuid);
    while($row = $userMessages->fetch(PDO::FETCH_ASSOC)) {
        $messageArray = array(
            "type" => "ban",
            "message" => $row["message"]
        );
        array_push($result, $messageArray);
    }

    // get global messages
    $messages = getGlobalMessages();
    while($row = $messages->fetch(PDO::FETCH_ASSOC)) {
        $messageArray = array(
            "type" => "message",
            "message" => json_encode(array("ops" => array(array("insert" => $row["message"]))))
        );
        array_push($result, $messageArray);
    }
    return $result;
}
?>
