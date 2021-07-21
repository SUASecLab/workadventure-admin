<?php
// Connect to database
try {
    $DB = new PDO("mysql:dbname=".getenv('DB_MYSQL_DATABASE').";host=admin-db;port=3306",
        getenv('DB_MYSQL_USER'), getenv('DB_MYSQL_PASSWORD'));
}
catch (PDOException $exception) {
    return;
}

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
    shell_exec("echo ".$exception." > /var/www/html/le.txt");
    }
}
$DB = NULL;

?>
