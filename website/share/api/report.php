<?php

/*
report player -> returns ?
no result object -> not used by the client
does not seem to work properly as of now
*/
header("Content-Type:application/json");
require 'database_operations.php';

if ((isset($_POST["reportedUserUuid"])) && (isset($_POST["reportedUserComment"])) && (isset($_POST["reporterUserUuid"])) && (isset($_POST["reportWorldSlug"]))) {
    $reportedUserUuid = $_POST["reportedUserUuid"];
    $reportedUserComment = $_POST["reportedUserComment"];
    $reporterUserUuid = $_POST["reporterUserUuid"];
    $reportWorldSlug = $_POST["reportWorldSlug"];
    reportUser($reportedUserUuid, $reportedUserComment, $reporterUserUuid, $reportWorldSlug);
} else {
    http_response_code(400);
    die();
}
?>
