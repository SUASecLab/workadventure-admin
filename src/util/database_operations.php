<?php
function getDatabaseHandle() {
    $handle = NULL;
    try {
        $handle = new PDO("mysql:dbname=" . getenv('DB_MYSQL_DATABASE') . ";host=admin-db;port=3306", getenv('DB_MYSQL_USER'), getenv('DB_MYSQL_PASSWORD'));
    }
    catch(PDOException $exception) {
        error_log($exception);
    }
    return $handle;
}
function getDatabaseHandleOrDie() {
    $handle = getDatabaseHandle();
    if ($handle == NULL) {
        die();
    }
    return $handle;
}
function getDatabaseHandleOrPrintError() {
    $handle = getDatabaseHandle();
    if ($handle == NULL) {
        echo "<aside class=\"alert alert-danger\" role=\"alert\">";
        echo "Could not connect to database";
        echo "</aside>";
        die();
    }
    return $handle;
}
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
    }
    catch(PDOException $exception) {
        error_log($exception);
        return false;
    }
}
function writeUuidToDatabase($uuid) {
    GLOBAL $DB;
    $email = $uuid . "@" . getenv('DOMAIN');
    $Statement = $DB->prepare("INSERT INTO users (uuid, name, email) VALUES (:uuid, 'anonymous', :email)");
    $Statement->bindParam(":uuid", $uuid, PDO::PARAM_STR);
    $Statement->bindParam(":email", $email, PDO::PARAM_STR);
    try {
        $Statement->execute();
        return true;
    }
    catch(PDOException $exception) {
        error_log($exception);
        return false;
    }
}
function updateUserData($uuid, $name, $email, $visitCardUrl) {
    GLOBAL $DB;
    if ((strlen($visitCardUrl) == 0) || ($visitCardUrl == NULL)) {
        $visitCardUrl == "";
    }
    $Statement = $DB->prepare("UPDATE users SET name = :name, email = :email, visitCardUrl = :visitCardUrl WHERE uuid = :uuid;");
    $Statement->bindParam(":name", $name, PDO::PARAM_STR);
    $Statement->bindParam(":email", $email, PDO::PARAM_STR);
    $Statement->bindParam(":uuid", $uuid, PDO::PARAM_STR);
    $Statement->bindParam(":visitCardUrl", $visitCardUrl, PDO::PARAM_STR);
    try {
        $Statement->execute();
        return true;
    }
    catch(PDOException $exception) {
        error_log($exception);
        return false;
    }
}
function getUserEmail($uuid) {
    GLOBAL $DB;
    $Statement = $DB->prepare("SELECT email FROM users WHERE uuid = :uuid");
    $Statement->bindParam(":uuid", $uuid, PDO::PARAM_STR);
    try {
        $Statement->execute();
        $row = $Statement->fetch(PDO::FETCH_ASSOC);
        $email = $row["email"] ?? $uuid . "@" . getenv('DOMAIN');
        return $email;
    }
    catch(PDOException $exception) {
        error_log($exception);
        return NULL;
    }
}
function getUuidFromEmail($email) {
    GLOBAL $DB;
    $Statement = $DB->prepare("SELECT uuid FROM users WHERE email = :email");
    $Statement->bindParam(":email", $email, PDO::PARAM_STR);
    try {
        $Statement->execute();
        $row = $Statement->fetch(PDO::FETCH_ASSOC);
        if ($Statement->rowCount() > 0) {
            return $uuid = $row["uuid"];
        } else {
            return NULL;
        }
    }
    catch(PDOException $exception) {
        error_log($exception);
        return NULL;
    }
}
function getUserVisitCardUrl($uuid, $withPrefix = false) {
    GLOBAL $DB;
    $Statement = $DB->prepare("SELECT visitCardUrl FROM users WHERE uuid = :uuid");
    $Statement->bindParam(":uuid", $uuid, PDO::PARAM_STR);
    try {
        $Statement->execute();
        if ($Statement->rowCount() > 0) {
            $row = $Statement->fetch(PDO::FETCH_ASSOC);
            $visitCard = $row["visitCardUrl"];
            if (($visitCard == NULL) || (strlen($visitCard) == 0)) {
                return NULL;
            } else {
                if ($withPrefix) {
                    return "https://" . $visitCard;
                } else {
                    return $visitCard;
                }
            }
        } else {
            return NULL;
        }
    }
    catch(PDOException $exception) {
        error_log($exception);
        return NULL;
    }
}
function getUserStartMap($uuid) {
    GLOBAL $DB;
    $Statement = $DB->prepare("SELECT startMap FROM users WHERE uuid = :uuid");
    $Statement->bindParam(":uuid", $uuid, PDO::PARAM_STR);
    try {
        $Statement->execute();
        if ($Statement->rowCount() > 0) {
            $row = $Statement->fetch(PDO::FETCH_ASSOC);
            $map = $row["startMap"];
            if (($map == NULL) || (strlen($map) == 0)) {
                return NULL;
            } else {
                return $map;
            }
        } else {
            return NULL;
        }
    }
    catch(PDOException $exception) {
        error_log($exception);
        return NULL;
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
        if (sizeof($result) == 0) {
            array_push($result, " ");
        }
    }
    catch(PDOException $exception) {
        error_log($exception);
    }
    return $result;
}
function hasTags($uuid) {
    GLOBAL $DB;
    $Statement = $DB->prepare("SELECT * FROM tags WHERE uuid=:uuid");
    $Statement->bindParam(":uuid", $uuid, PDO::PARAM_STR);
    try {
        $Statement->execute();
        if ($Statement->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }
    catch(PDOException $exception) {
        error_log($exception);
        return false;
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
    }
    catch(PDOException $exception) {
        error_log($exception);
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
    }
    catch(PDOException $exception) {
        error_log($exception);
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
    }
    catch(PDOException $exception) {
        error_log($exception);
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
    }
    catch(PDOException $exception) {
        error_log($exception);
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
    }
    catch(PDOException $exception) {
        error_log($exception);
        return NULL;
    }
}
function getAllUsers() {
    GLOBAL $DB;
    $Statement = $DB->prepare("SELECT * FROM users;");
    try {
        $Statement->execute();
        return $Statement;
    }
    catch(PDOException $exception) {
        error_log($exception);
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
    }
    catch(PDOException $exception) {
        error_log($exception);
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
    }
    catch(PDOException $exception) {
        error_log($exception);
        return false;
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
    }
    catch(PDOException $exception) {
        error_log($exception);
        return false;
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
    }
    catch(PDOException $exception) {
        error_log($exception);
        return true; // do not create new users in case of errors
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
    }
    catch(PDOException $exception) {
        error_log($exception);
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
    }
    catch(PDOException $exception) {
        error_log($exception);
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
            return "https://" . $row["map_file_url"];
        } else {
            return NULL;
        }
    }
    catch(PDOException $exception) {
        error_log($exception);
        return NULL;
    }
}
function getMapPolicy($map) {
    GLOBAL $DB;
    $Statement = $DB->prepare("SELECT * FROM maps WHERE map_url = :map_url;");
    $Statement->bindParam(":map_url", $map, PDO::PARAM_STR);
    try {
        $Statement->execute();
        if ($Statement->rowCount() > 0) {
            $row = $Statement->fetch(PDO::FETCH_ASSOC);
            return (int)$row["policy"];
        } else {
            return NULL;
        }
    }
    catch(PDOException $exception) {
        error_log($exception);
        return NULL;
    }
}
function storeMapFileUrl($mapUrl, $mapFileUrl, $policyNumber) {
    GLOBAL $DB;
    $Statement = $DB->prepare("INSERT INTO maps (map_url, map_file_url, policy) VALUES (:map_url, :map_file_url, :policy);");
    $Statement->bindParam(":map_url", $mapUrl, PDO::PARAM_STR);
    $Statement->bindParam(":map_file_url", $mapFileUrl, PDO::PARAM_STR);
    $Statement->bindParam(":policy", $policyNumber, PDO::PARAM_INT);
    try {
        $Statement->execute();
        return true;
    }
    catch(PDOException $exception) {
        error_log($exception);
        return false;
    }
}
function getAllMaps() {
    GLOBAL $DB;
    $Statement = $DB->prepare("SELECT * FROM maps;");
    try {
        $Statement->execute();
        return $Statement;
    }
    catch(PDOException $exception) {
        error_log($exception);
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
    }
    catch(PDOException $exception) {
        error_log($exception);
        return false;
    }
}
function addMapTag($mapUrl, $mapTag) {
    GLOBAL $DB;
    $Statement = $DB->prepare("INSERT INTO maps_tags (map_url, tag) VALUES (:map_url, :tag);");
    $Statement->bindParam(":map_url", $mapUrl, PDO::PARAM_STR);
    $Statement->bindParam(":tag", $mapTag, PDO::PARAM_STR);
    try {
        $Statement->execute();
        return true;
    }
    catch(PDOException $exception) {
        error_log($exception);
        return false;
    }
}
function getMapTags($mapUrl) {
    GLOBAL $DB;
    $result = array();
    $Statement = $DB->prepare("SELECT tag from maps_tags WHERE map_url = :map_url;");
    $Statement->bindParam(":map_url", $mapUrl, PDO::PARAM_STR);
    try {
        $Statement->execute();
        while ($row = $Statement->fetch(PDO::FETCH_ASSOC)) {
            array_push($result, $row["tag"]);
        }
    }
    catch(PDOException $exception) {
        error_log($exception);
    }
    return $result;
}
function removeMapTags($mapUrl) {
    GLOBAL $DB;
    $Statement = $DB->prepare("DELETE FROM maps_tags WHERE map_url = :map_url;");
    $Statement->bindParam(":map_url", $mapUrl, PDO::PARAM_STR);
    try {
        $Statement->execute();
        return true;
    }
    catch(PDOException $exception) {
        error_log($exception);
        return false;
    }
}
function getMapRedirect($mapUrl) {
    GLOBAL $DB;
    $Statement = $DB->prepare("SELECT redirect_url FROM map_redirects WHERE map_url = :map_url;");
    $Statement->bindParam(":map_url", $mapUrl, PDO::PARAM_STR);
    try {
        $Statement->execute();
        if ($Statement->rowCount() > 0) {
            $row = $Statement->fetch(PDO::FETCH_ASSOC);
            return $row["redirect_url"];
        } else {
            return NULL;
        }
    }
    catch(PDOException $exception) {
        error_log($exception);
        return NULL;
    }
}
function addMapRedirect($shortMapOld, $redirectUrl) {
    GLOBAL $DB;
    $Statement = $DB->prepare("INSERT INTO map_redirects(map_url, redirect_url) VALUES(:map_url, :redirect_url);");
    $Statement->bindParam(":map_url", $shortMapOld, PDO::PARAM_STR);
    $Statement->bindParam(":redirect_url", $redirectUrl, PDO::PARAM_STR);
    try {
        $Statement->execute();
        return true;
    } catch (PDOException $exception) {
        error_log($exception);
        return false;
    }
}
function getAllMapRedirects() {
    GLOBAL $DB;
    $Statement = $DB->prepare("SELECT * FROM map_redirects;");
    try {
        $Statement->execute();
        return $Statement;
    }
    catch(PDOException $exception) {
        error_log($exception);
        return NULL;
    }
}
function removeMapRedirect($redirect) {
    GLOBAL $DB;
    $Statement = $DB->prepare("DELETE FROM map_redirects WHERE map_url = :map_url;");
    $Statement->bindParam(":map_url", $redirect, PDO::PARAM_STR);
    try {
        $Statement->execute();
        return true;
    }
    catch(PDOException $exception) {
        error_log($exception);
        return false;
    }
}
function allowedToCreateNewUser() {
    GLOBAL $DB;
    $Statement = $DB->prepare("SELECT preference_key FROM preferences;");
    try {
        $Statement->execute();
        if ($Statement->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }
    catch(PDOException $exception) {
        error_log($exception);
        return false;
    }
}
function setAllowedToCreateNewUser($allow) {
    GLOBAL $DB;
    $result = true;
    if (allowedToCreateNewUser()) {
        $Statement = $DB->prepare("DELETE FROM preferences WHERE preference_key = 'allowedToCreateNewUser';");
        try {
            $Statement->execute();
        }
        catch(PDOException $exception) {
            error_log($exception);
            $result = false;
        }
    }
    if ($allow) {
        $Statement = $DB->prepare("INSERT INTO preferences (preference_key, preference_value) VALUES ('allowedToCreateNewUser', 'true');");
        try {
            $Statement->execute();
        }
        catch(PDOException $exception) {
            error_log($exception);
            $result = false;
        }
    }
    return $result;
}
function globalMessagesExist() {
    GLOBAL $DB;
    $Statement = $DB->prepare("SELECT * FROM global_messages;");
    try {
        $Statement->execute();
        if ($Statement->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }
    catch(PDOException $exception) {
        error_log($exception);
        return false;
    }
}
function createNewGlobalMessage($message) {
    GLOBAL $DB;
    $Statement = $DB->prepare("INSERT INTO global_messages (message) VALUES (:message);");
    $Statement->bindParam(":message", $message, PDO::PARAM_STR);
    try {
        $Statement->execute();
        return true;
    }
    catch(PDOException $exception) {
        error_log($exception);
        return false;
    }
}
function getGlobalMessages() {
    GLOBAL $DB;
    $Statement = $DB->prepare("SELECT * FROM global_messages;");
    try {
        $Statement->execute();
        return $Statement;
    }
    catch(PDOException $exception) {
        error_log($exception);
        return NULL;
    }
}
function deleteGlobalMessage($id) {
    GLOBAL $DB;
    $Statement1 = $DB->prepare("DELETE FROM hidden_global_messages WHERE message_id = :id;");
    $Statement1->bindParam(":id", $id, PDO::PARAM_INT);
    $Statement2 = $DB->prepare("DELETE FROM global_messages WHERE message_id = :id;");
    $Statement2->bindParam(":id", $id, PDO::PARAM_INT);
    try {
        $Statement1->execute();
        $Statement2->execute();
        return true;
    }
    catch(PDOException $exception) {
        error_log($exception);
        return false;
    }
}
function getHiddenMessagesIds($uuid) {
    GLOBAL $DB;
    $result = array();
    $Statement = $DB->prepare("SELECT * FROM hidden_global_messages WHERE uuid = :uuid;");
    $Statement->bindParam(":uuid", $uuid, PDO::PARAM_STR);
    try {
        $Statement->execute();
        while ($row = $Statement->fetch(PDO::FETCH_ASSOC)) {
            array_push($result, $row["message_id"]);
        }
    }
    catch(PDOException $exception) {
        error_log($exception);
    }
    return $result;
}
function hideGlobalMessage($uuid, $messageId) {
    GLOBAL $DB;
    $Statement = $DB->prepare("INSERT INTO hidden_global_messages (uuid, message_id) VALUES (:uuid, :message_id);");
    $Statement->bindParam(":uuid", $uuid, PDO::PARAM_STR);
    $Statement->bindParam(":message_id", $messageId, PDO::PARAM_INT);
    try {
        $Statement->execute();
    }
    catch(PDOException $exception) {
        error_log($exception);
    }
}
function userMessagesExist($uuid) {
    GLOBAL $DB;
    $Statement = $DB->prepare("SELECT * FROM user_messages WHERE user_uuid = :user_uuid;");
    $Statement->bindParam(":user_uuid", $uuid, PDO::PARAM_STR);
    try {
        $Statement->execute();
        if ($Statement->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }
    catch(PDOException $exception) {
        error_log($exception);
        return false;
    }
}
function getUserMessages($uuid) {
    GLOBAL $DB;
    $Statement = $DB->prepare("SELECT * FROM user_messages WHERE user_uuid = :user_uuid;");
    $Statement->bindParam(":user_uuid", $uuid, PDO::PARAM_STR);
    try {
        $Statement->execute();
        return $Statement;
    }
    catch(PDOException $exception) {
        error_log($exception);
        return NULL;
    }
}
function removeUserMessage($messageId) {
    GLOBAL $DB;
    $Statement = $DB->prepare("DELETE FROM user_messages WHERE message_id = :id;");
    $Statement->bindParam(":id", $messageId, PDO::PARAM_INT);
    try {
        $Statement->execute();
        return true;
    }
    catch(PDOException $exception) {
        error_log($exception);
        return false;
    }
}
function storeUserMessage($userUuid, $message) {
    GLOBAL $DB;
    $Statement = $DB->prepare("INSERT INTO user_messages (user_uuid, message) VALUES (:user_uuid, :message);");
    $Statement->bindParam(":user_uuid", $userUuid, PDO::PARAM_STR);
    $Statement->bindParam(":message", $message, PDO::PARAM_STR);
    try {
        $Statement->execute();
        return true;
    }
    catch(PDOException $exception) {
        error_log($exception);
        return false;
    }
}
function customTexturesStored() {
    GLOBAL $DB;
    $Statement = $DB->prepare("SELECT * FROM textures;");
    try {
        $Statement->execute();
        if ($Statement->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }
    catch(PDOException $exception) {
        error_log($exception);
        return false;
    }
}
function getCustomTextures() {
    GLOBAL $DB;
    $Statement = $DB->prepare("SELECT * FROM textures;");
    try {
        $Statement->execute();
        return $Statement;
    }
    catch(PDOException $exception) {
        error_log($exception);
        return NULL;
    }
}
function removeTexture($textureTableId) {
    GLOBAL $DB;
    $Statement1 = $DB->prepare("DELETE FROM textures_restrictions  WHERE texture_table_id = :id;");
    $Statement2 = $DB->prepare("DELETE FROM textures WHERE texture_table_id = :id;");
    $Statement1->bindParam(":id", $textureTableId, PDO::PARAM_INT);
    $Statement2->bindParam(":id", $textureTableId, PDO::PARAM_INT);
    try {
        $Statement1->execute();
        $Statement2->execute();
        return true;
    }
    catch(PDOException $exception) {
        error_log($exception);
        return false;
    }
}
function storeTexture($id, $layer, $url) {
    GLOBAL $DB;
    $Statement = $DB->prepare("INSERT INTO textures (texture_id, texture_name, texture_layer, texture_url) VALUES (:texture_id, :texture_id, :texture_layer, :texture_url);");
    $Statement->bindParam(":texture_id", $id, PDO::PARAM_STR);
    $Statement->bindParam(":texture_name", $id, PDO::PARAM_STR);
    $Statement->bindParam(":texture_layer", $layer, PDO::PARAM_STR);
    $Statement->bindParam(":texture_url", $url, PDO::PARAM_STR);
    try {
        $Statement->execute();
        return true;
    }
    catch(PDOException $exception) {
        error_log($exception);
        return false;
    }
}
function storeTextureTag($textureId, $textureTag) {
    GLOBAL $DB;
    $Statement = $DB->prepare("INSERT INTO textures_restrictions (texture_table_id, tag) VALUES (:texture_table_id, :tag);");
    $Statement->bindParam(":texture_table_id", $textureId, PDO::PARAM_INT);
    $Statement->bindParam(":tag", $textureTag, PDO::PARAM_STR);
    try {
        $Statement->execute();
        return true;
    }
    catch(PDOException $exception) {
        error_log($exception);
        return false;
    }
}
function getTextureTags($textureId) {
    GLOBAL $DB;
    $result = array();
    $Statement = $DB->prepare("SELECT tag FROM textures_restrictions WHERE texture_table_id = :texture_table_id;");
    $Statement->bindParam(":texture_table_id", $textureId, PDO::PARAM_INT);
    try {
        $Statement->execute();
        while ($row = $Statement->fetch(PDO::FETCH_ASSOC)) {
            array_push($result, $row["tag"]);
        }
    }
    catch(PDOException $exception) {
        error_log($exception);
    }
    return $result;
}
function getLastTextureId() {
    GLOBAL $DB;
    $Statement = $DB->prepare("SELECT texture_table_id FROM textures ORDER BY texture_table_id DESC LIMIT 1;");
    try {
        $Statement->execute();
        if ($Statement->rowCount() > 0) {
            $row = $Statement->fetch(PDO::FETCH_ASSOC);
            return $row["texture_table_id"];
        } else {
            return -1;
        }
    }
    catch(PDOException $exception) {
        error_log($exception);
        return -1;
    }
}
function reportUser($reportedUserUuid, $reportedUserComment, $reporterUserUuid, $reportRoomUrl) {
    GLOBAL $DB;
    $Statement = $DB->prepare("INSERT INTO reports (reportedUserUuid, reportedUserComment, reporterUserUuid, reportRoomUrl) VALUES (:reportedUserUuid, :reportedUserComment, :reporterUserUuid, :reportRoomUrl)");
    $Statement->bindParam(":reportedUserUuid", $reportedUserUuid, PDO::PARAM_STR);
    $Statement->bindParam(":reportedUserComment", $reportedUserComment, PDO::PARAM_STR);
    $Statement->bindParam(":reporterUserUuid", $reporterUserUuid, PDO::PARAM_STR);
    $Statement->bindParam(":reportRoomUrl", $reportRoomUrl, PDO::PARAM_STR);
    try {
        $Statement->execute();
    }
    catch(PDOException $exception) {
        error_log($exception);
    }
}
function reportsStored() {
    GLOBAL $DB;
    $Statement = $DB->prepare("SELECT * FROM reports;");
    try {
        $Statement->execute();
        if ($Statement->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }
    catch(PDOException $exception) {
        error_log($exception);
        return false;
    }
}
function getReports() {
    GLOBAL $DB;
    $Statement = $DB->prepare("SELECT * FROM reports;");
    try {
        $Statement->execute();
        return $Statement;
    }
    catch(PDOException $exception) {
        error_log($exception);
        return NULL;
    }
}
function removeReport($reportId) {
    GLOBAL $DB;
    $Statement = $DB->prepare("DELETE FROM reports WHERE report_id = :id;");
    $Statement->bindParam(":id", $reportId, PDO::PARAM_INT);
    try {
        $Statement->execute();
        return true;
    }
    catch(PDOException $exception) {
        error_log($exception);
        return false;
    }
}
