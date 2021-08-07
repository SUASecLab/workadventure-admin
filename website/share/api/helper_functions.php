<?php
require_once 'database_operations.php';

function getGlobalMessagesForAdminAPI() {
    $result = array();
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
