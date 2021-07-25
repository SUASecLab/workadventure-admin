<?php

function userExists($uuid) {
    GLOBAL $DB;
    $Statement = $DB->prepare("SELECT * FROM users WHERE uuid=:uuid;");
    $Statement->bindParam(":uuid", $uuid, PDO::PARAM_STR);
    if ($Statement->execute()) {
        $row = $Statement->fetch(PDO::FETCH_ASSOC);
        if ($row["uuid"]) {
            return true;
        }
    }
    return false;
}

function writeUuidToDatabase($uuid) {
    GLOBAL $DB;
    $Statement = $DB->prepare("INSERT INTO users (uuid, name, email) VALUES (:uuid, 'anonymous', 'anonymous@example.com')");
    $Statement->bindParam(":uuid", $uuid, PDO::PARAM_STR);
    if ($Statement->execute()) {
        return true;
    } else {
        return false;
    }
}

function createAccountIfNotExistent($uuid) {
    if (!userExists($uuid)) {
        return writeUuidToDatabase($uuid);
    }
}

function getTags($uuid) {
    GLOBAL $DB;
    $result = [];
    $Statement = $DB->prepare("SELECT * FROM tags WHERE uuid=:uuid");
    $Statement->bindParam(":uuid", $uuid, PDO::PARAM_STR);
    if ($Statement->execute()) {
        while($row = $Statement->fetch(PDO::FETCH_ASSOC)) {
            array_push($result, $row["tag"]);
        }
    }
    return $result;
}

function reportUser($reportedUserUuid, $reportedUserComment, $reporterUserUuid, $reportWorldSlug) {
    GLOBAL $DB;
    $Statement = $DB->prepare("INSERT INTO reports (reportedUserUuid, reportedUserComment, reporterUserUuid, reportWorldSlug) VALUES (:reportedUserUuid, :reportedUserComment, :reporterUserUuid, :reportWorldSlug)");
    $Statement->bindParam(":reportedUserUuid", $reportedUserUuid, PDO::PARAM_STR);
    $Statement->bindParam(":reportedUserComment", $reportedUserComment, PDO::PARAM_STR);
    $Statement->bindParam(":reporterUserUuid", $reporterUserUuid, PDO::PARAM_STR);
    $Statement->bindParam(":reportWorldSlug", $reportWorldSlug, PDO::PARAM_STR);
    try {
        $Statement->execute();
    } catch (PDOException $exception) {
    }
}

function isBanned($uuid) {
    GLOBAL $DB;
    $Statement = $DB->prepare("SELECT * FROM banned_users WHERE uuid=:uuid;");
    $Statement->bindParam(":uuid", $uuid, PDO::PARAM_STR);
    if ($Statement->execute()) {
        if ($Statement->rowCount() > 0) {
            return true;
        }
    }
    return false;
}

function getBanMessage($uuid) {
    GLOBAL $DB;
    $Statement = $DB->prepare("SELECT * FROM banned_users WHERE uuid=:uuid;");
    $Statement->bindParam(":uuid", $uuid, PDO::PARAM_STR);
    if ($Statement->execute()) {
        $row = $Statement->fetch(PDO::FETCH_ASSOC);
        return $row["ban_message"];
    }
    return "";
}

function banUser($uuid, $banMessage) {
    GLOBAL $DB;
    $Statement = $DB->prepare("INSERT INTO banned_users (uuid, ban_message) VALUES (:uuid, :ban_message)");
    $Statement->bindParam(":uuid", $uuid, PDO::PARAM_STR);
    $Statement->bindParam(":ban_message", $banMessage, PDO::PARAM_STR);
    try {
        $Statement->execute();
        return true;
    } catch (PDOException $exception) {
        return false;
    }
}
function liftBan($uuid) {
    GLOBAL $DB;
    $Statement = $DB->prepare("DELETE FROM banned_users WHERE uuid=:uuid;");
    $Statement->bindParam(":uuid", $uuid, PDO::PARAM_STR);
    if ($Statement->execute()) {
        return true;
    } else {
        return false;
    }
}

?>
