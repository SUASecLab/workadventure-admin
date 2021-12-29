<?php
header("Content-Type:application/json");
require_once ('../util/api_authentication.php');
require_once ('../util/database_operations.php');
$DB = getDatabaseHandleOrDie();
authorizeOrDie();
$payload = file_get_contents("php://input");
$decodedPayload = (array)json_decode($payload);
$reportedUserUuid = htmlspecialchars($decodedPayload["reportedUserUuid"]);
$reportedUserComment = htmlspecialchars($decodedPayload["reportedUserComment"]);
$reporterUserUuid = htmlspecialchars($decodedPayload["reporterUserUuid"]);
$reportWorldSlug = htmlspecialchars($decodedPayload["reportWorldSlug"]);
reportUser($reportedUserUuid, $reportedUserComment, $reporterUserUuid, $reportWorldSlug);
$DB = NULL;
?>
