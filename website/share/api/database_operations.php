<?php

function userExists($uuid) {
    GLOBAL $DB;
    $Statement = $DB->prepare("SELECT * FROM users WHERE uuid=:uuid;");
    $Statement->bindParam(":uuid", $uuid, PDO::PARAM_STR);
    try {
        $Statement->execute();
        if ($Statement->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    } catch (PDOException $exception) {
        return false;
    }
}

function writeUuidToDatabase($uuid) {
    GLOBAL $DB;
    $Statement = $DB->prepare("INSERT INTO users (uuid, name, email) VALUES (:uuid, 'anonymous', 'anonymous@example.com')");
    $Statement->bindParam(":uuid", $uuid, PDO::PARAM_STR);
    try {
        $Statement->execute();
        return true;
    } catch (PDOException $exception) {
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
    try {
        $Statement->execute();
        while ($row = $Statement->fetch(PDO::FETCH_ASSOC)) {
            array_push($result, $row["tag"]);
        }
    } catch (PDOException $exception) {
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
    try {
        $Statement->execute();
        if ($Statement->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    } catch (PDOException $exception) {
        return false;
    }
}

function getBanMessage($uuid) {
    GLOBAL $DB;
    $Statement = $DB->prepare("SELECT * FROM banned_users WHERE uuid=:uuid;");
    $Statement->bindParam(":uuid", $uuid, PDO::PARAM_STR);
    try {
        $Statement->execute();
        $row = $Statement->fetch(PDO::FETCH_ASSOC);
        return $row["ban_message"];
    } catch (PDOException $exception) {
        return "";
    }
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
    try {
        $Statement->execute();
        return true;
    } catch (PDOException $exception) {
        return false;
    }
}

function getNumberOfUsers() {
    GLOBAL $DB;
    $Statement = $DB->prepare("SELECT count('uuid') as number FROM users;");
    try {
        $Statement->execute();
        $row = $Statement->fetch(PDO::FETCH_ASSOC);
        return $row["number"];
    } catch (PDOException $exception) {
        return NULL;
    }
}

function getAllUsers() {
    GLOBAL $DB;
    $Statement = $DB->prepare("SELECT * FROM users;");
    try {
        $Statement->execute();
        return $Statement;
    } catch (PDOException $exception) {
        return NULL;
    }
}

function getUserData($uuid) {
    GLOBAL $DB;
    $Statement = $DB->prepare("SELECT * FROM users WHERE uuid=:uuid;");
    $Statement->bindParam(":uuid", $uuid, PDO::PARAM_STR);
    try {
        $Statement->execute();
        return $Statement->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $exception) {
        return NULL;
    }
}

function addTag($uuid, $newTag) {
    GLOBAL $DB;
    $Statement = $DB->prepare("INSERT INTO tags (uuid, tag) VALUES (:uuid, :tag);");
    $Statement->bindParam(":uuid", $uuid, PDO::PARAM_STR);
    $Statement->bindParam(":tag", $newTag, PDO::PARAM_STR);
    try {
        $Statement->execute();
        return true;
    } catch (PDOException $exception) {
        return NULL;
    }
}

function removeTag($uuid, $remTag) {
    GLOBAL $DB;
    $Statement = $DB->prepare("DELETE FROM tags WHERE uuid=:uuid AND tag=:tag;");
    $Statement->bindParam(":uuid", $uuid, PDO::PARAM_STR);
    $Statement->bindParam(":tag", $remTag, PDO::PARAM_STR);
    try {
        $Statement->execute();
        return true;
    } catch (PDOException $exception) {
        return NULL;
    }
}

function websiteUserExists() {
    GLOBAL $DB;
    $Statement = $DB->prepare("SELECT * FROM website");
    try {
        $Statement->execute();
        if ($Statement->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    } catch (PDOException $exception) {
        return false;
    }
}

function createWebsiteUser($username, $hashedPassword) {
    GLOBAL $DB;
    $Statement = $DB->prepare("INSERT INTO website (username, hashed_password) VALUES (:username, :hashed_password);");
    $Statement->bindParam(":username", $username, PDO::PARAM_STR);
    $Statement->bindParam(":hashed_password", $hashedPassword, PDO::PARAM_STR);
    try {
        $Statement->execute();
        return true;
    } catch (PDOException $exception) {
        return false;
    }
}

function websiteUserValid($username, $hashedPassword) {
    GLOBAL $DB;
    $Statement = $DB->prepare("SELECT * FROM website WHERE username = :username AND hashed_password = :hashed_password;");
    $Statement->bindParam(":username", $username, PDO::PARAM_STR);
    $Statement->bindParam(":hashed_password", $hashedPassword, PDO::PARAM_STR);
    try {
        $Statement->execute();
        if ($Statement->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    } catch (PDOException $exception) {
        return false;
    }
}

function getMapFileUrl($map) {
    GLOBAL $DB;
    $Statement = $DB->prepare("SELECT * FROM maps WHERE map_url = :map_url;");
    $Statement->bindParam(":map_url", $map, PDO::PARAM_STR);
    try {
        $Statement->execute();
        if ($Statement->rowCount() > 0) {
            $row = $Statement->fetch(PDO::FETCH_ASSOC);
            return "https://".$row["map_file_url"];
        } else {
            return NULL;
        }
    } catch (PDOException $exception) {
        return NULL;
    }
}

function storeMapFileUrl($mapUrl, $mapFileUrl) {
    GLOBAL $DB;
    $Statement = $DB->prepare("INSERT INTO maps (map_url, map_file_url) VALUES (:map_url, :map_file_url);");
    $Statement->bindParam(":map_url", $mapUrl, PDO::PARAM_STR);
    $Statement->bindParam(":map_file_url", $mapFileUrl, PDO::PARAM_STR);
    try {
        $Statement->execute();
        return true;
    } catch (PDOException $exception) {
        return false;
    }
}

function getAllMaps() {
    GLOBAL $DB;
    $Statement = $DB->prepare("SELECT * FROM maps;");
    try {
        $Statement->execute();
        return $Statement;
    } catch (PDOException $exception) {
        return NULL;
    }
}

function removeMap($mapUrl) {
    GLOBAL $DB;
    $Statement = $DB->prepare("DELETE FROM maps WHERE map_url = :map_url;");
    $Statement->bindParam(":map_url", $mapUrl, PDO::PARAM_STR);
    try {
        $Statement->execute();
        return true;
    } catch (PDOException $exception) {
        return false;
    }
}
?>
