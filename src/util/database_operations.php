<?php
function getDatabaseHandle()
{
    $handle = NULL;
    try {
        $handle = new PDO("mysql:dbname=" . getenv('DB_MYSQL_DATABASE') . ";host=admin-db;port=3306", getenv('DB_MYSQL_USER'), getenv('DB_MYSQL_PASSWORD'));
    } catch (PDOException $exception) {
        error_log($exception);
    }
    return $handle;
}
function getDatabaseHandleOrDie()
{
    $handle = getDatabaseHandle();
    if ($handle == NULL) {
        die();
    }
    return $handle;
}
function getDatabaseHandleOrPrintError()
{
    $handle = getDatabaseHandle();
    if ($handle == NULL) {
        echo "<aside class=\"alert alert-danger\" role=\"alert\">";
        echo "Could not connect to database";
        echo "</aside>";
        die();
    }
    return $handle;
}
function localUserExists($uuid)
{
    global $DB;
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
        error_log($exception);
        return false;
    }
}
function writeUuidToDatabase($uuid)
{
    global $DB;
    $email = $uuid . "@" . getenv('DOMAIN');
    $Statement = $DB->prepare("INSERT INTO users (uuid, name, email) VALUES (:uuid, 'anonymous', :email)");
    $Statement->bindParam(":uuid", $uuid, PDO::PARAM_STR);
    $Statement->bindParam(":email", $email, PDO::PARAM_STR);
    try {
        $Statement->execute();
        return true;
    } catch (PDOException $exception) {
        error_log($exception);
        return false;
    }
}
function updateUserData($uuid, $name, $email, $visitCardUrl)
{
    global $DB;
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
    } catch (PDOException $exception) {
        error_log($exception);
        return false;
    }
}

function getLocalUuidFromEmail($email)
{
    global $DB;
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
    } catch (PDOException $exception) {
        error_log($exception);
        return NULL;
    }
}

function createAccountIfNotExistent($uuid)
{
    if (!localUserExists($uuid)) {
        return writeUuidToDatabase($uuid);
    }
}
function getTags($uuid)
{
    global $DB;
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
    } catch (PDOException $exception) {
        error_log($exception);
    }
    return $result;
}
function hasTags($uuid)
{
    global $DB;
    $Statement = $DB->prepare("SELECT * FROM tags WHERE uuid=:uuid");
    $Statement->bindParam(":uuid", $uuid, PDO::PARAM_STR);
    try {
        $Statement->execute();
        if ($Statement->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    } catch (PDOException $exception) {
        error_log($exception);
        return false;
    }
}
function isBanned($uuid)
{
    global $DB;
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
        error_log($exception);
        return false;
    }
}
function getBanMessage($uuid)
{
    global $DB;
    $Statement = $DB->prepare("SELECT * FROM banned_users WHERE uuid=:uuid;");
    $Statement->bindParam(":uuid", $uuid, PDO::PARAM_STR);
    try {
        $Statement->execute();
        $row = $Statement->fetch(PDO::FETCH_ASSOC);
        return $row["ban_message"];
    } catch (PDOException $exception) {
        error_log($exception);
        return "";
    }
}
function banUser($uuid, $banMessage)
{
    global $DB;
    $Statement = $DB->prepare("INSERT INTO banned_users (uuid, ban_message) VALUES (:uuid, :ban_message)");
    $Statement->bindParam(":uuid", $uuid, PDO::PARAM_STR);
    $Statement->bindParam(":ban_message", $banMessage, PDO::PARAM_STR);
    try {
        $Statement->execute();
        return true;
    } catch (PDOException $exception) {
        error_log($exception);
        return false;
    }
}
function liftBan($uuid)
{
    global $DB;
    $Statement = $DB->prepare("DELETE FROM banned_users WHERE uuid=:uuid;");
    $Statement->bindParam(":uuid", $uuid, PDO::PARAM_STR);
    try {
        $Statement->execute();
        return true;
    } catch (PDOException $exception) {
        error_log($exception);
        return false;
    }
}
function getNumberOfUsers()
{
    global $DB;
    $Statement = $DB->prepare("SELECT count('uuid') as number FROM users;");
    try {
        $Statement->execute();
        $row = $Statement->fetch(PDO::FETCH_ASSOC);
        return $row["number"];
    } catch (PDOException $exception) {
        error_log($exception);
        return NULL;
    }
}
function getAllUsers()
{
    global $DB;
    $Statement = $DB->prepare("SELECT * FROM users;");
    try {
        $Statement->execute();
        return $Statement;
    } catch (PDOException $exception) {
        error_log($exception);
        return NULL;
    }
}
function getLocalUserData($uuid)
{
    global $DB;
    $Statement = $DB->prepare("SELECT * FROM users WHERE uuid=:uuid;");
    $Statement->bindParam(":uuid", $uuid, PDO::PARAM_STR);
    try {
        $Statement->execute();
        return $Statement->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $exception) {
        error_log($exception);
        return NULL;
    }
}
function addTag($uuid, $newTag)
{
    global $DB;
    $Statement = $DB->prepare("INSERT INTO tags (uuid, tag) VALUES (:uuid, :tag);");
    $Statement->bindParam(":uuid", $uuid, PDO::PARAM_STR);
    $Statement->bindParam(":tag", $newTag, PDO::PARAM_STR);
    try {
        $Statement->execute();
        return true;
    } catch (PDOException $exception) {
        error_log($exception);
        return false;
    }
}
function removeTag($uuid, $remTag)
{
    global $DB;
    $Statement = $DB->prepare("DELETE FROM tags WHERE uuid=:uuid AND tag=:tag;");
    $Statement->bindParam(":uuid", $uuid, PDO::PARAM_STR);
    $Statement->bindParam(":tag", $remTag, PDO::PARAM_STR);
    try {
        $Statement->execute();
        return true;
    } catch (PDOException $exception) {
        error_log($exception);
        return false;
    }
}
function removeAllTags($uuid)
{
    global $DB;
    $Statement = $DB->prepare("DELETE FROM tags WHERE uuid=:uuid;");
    $Statement->bindParam(":uuid", $uuid, PDO::PARAM_STR);
    try {
        $Statement->execute();
        return true;
    } catch (PDOException $exception) {
        error_log($exception);
        return false;
    }
}
function websiteUserExists()
{
    global $DB;
    $Statement = $DB->prepare("SELECT * FROM website");
    try {
        $Statement->execute();
        if ($Statement->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    } catch (PDOException $exception) {
        error_log($exception);
        return true; // do not create new users in case of errors
    }
}
function createWebsiteUser($username, $hashedPassword)
{
    global $DB;
    $Statement = $DB->prepare("INSERT INTO website (username, hashed_password) VALUES (:username, :hashed_password);");
    $Statement->bindParam(":username", $username, PDO::PARAM_STR);
    $Statement->bindParam(":hashed_password", $hashedPassword, PDO::PARAM_STR);
    try {
        $Statement->execute();
        return true;
    } catch (PDOException $exception) {
        error_log($exception);
        return false;
    }
}
function websiteUserValid($username, $hashedPassword)
{
    global $DB;
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
        error_log($exception);
        return false;
    }
}
function getMapFileUrl($map)
{
    global $DB;
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
    } catch (PDOException $exception) {
        error_log($exception);
        return NULL;
    }
}
function getMapPolicy($map)
{
    global $DB;
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
    } catch (PDOException $exception) {
        error_log($exception);
        return NULL;
    }
}
function storeMapFileUrl($mapUrl, $mapFileUrl, $policyNumber)
{
    global $DB;
    $Statement = $DB->prepare("INSERT INTO maps (map_url, map_file_url, policy) VALUES (:map_url, :map_file_url, :policy);");
    $Statement->bindParam(":map_url", $mapUrl, PDO::PARAM_STR);
    $Statement->bindParam(":map_file_url", $mapFileUrl, PDO::PARAM_STR);
    $Statement->bindParam(":policy", $policyNumber, PDO::PARAM_INT);
    try {
        $Statement->execute();
        return true;
    } catch (PDOException $exception) {
        error_log($exception);
        return false;
    }
}
function getAllMaps()
{
    global $DB;
    $Statement = $DB->prepare("SELECT * FROM maps;");
    try {
        $Statement->execute();
        return $Statement;
    } catch (PDOException $exception) {
        error_log($exception);
        return NULL;
    }
}
function removeMap($mapUrl)
{
    global $DB;
    $Statement = $DB->prepare("DELETE FROM maps WHERE map_url = :map_url;");
    $Statement->bindParam(":map_url", $mapUrl, PDO::PARAM_STR);
    try {
        $Statement->execute();
        return true;
    } catch (PDOException $exception) {
        error_log($exception);
        return false;
    }
}
function addMapTag($mapUrl, $mapTag)
{
    global $DB;
    $Statement = $DB->prepare("INSERT INTO maps_tags (map_url, tag) VALUES (:map_url, :tag);");
    $Statement->bindParam(":map_url", $mapUrl, PDO::PARAM_STR);
    $Statement->bindParam(":tag", $mapTag, PDO::PARAM_STR);
    try {
        $Statement->execute();
        return true;
    } catch (PDOException $exception) {
        error_log($exception);
        return false;
    }
}
function getMapTags($mapUrl)
{
    global $DB;
    $result = array();
    $Statement = $DB->prepare("SELECT tag from maps_tags WHERE map_url = :map_url;");
    $Statement->bindParam(":map_url", $mapUrl, PDO::PARAM_STR);
    try {
        $Statement->execute();
        while ($row = $Statement->fetch(PDO::FETCH_ASSOC)) {
            array_push($result, $row["tag"]);
        }
    } catch (PDOException $exception) {
        error_log($exception);
    }
    return $result;
}
function removeMapTags($mapUrl)
{
    global $DB;
    $Statement = $DB->prepare("DELETE FROM maps_tags WHERE map_url = :map_url;");
    $Statement->bindParam(":map_url", $mapUrl, PDO::PARAM_STR);
    try {
        $Statement->execute();
        return true;
    } catch (PDOException $exception) {
        error_log($exception);
        return false;
    }
}
function getMapRedirect($mapUrl)
{
    global $DB;
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
    } catch (PDOException $exception) {
        error_log($exception);
        return NULL;
    }
}
function addMapRedirect($shortMapOld, $redirectUrl)
{
    global $DB;
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
function getAllMapRedirects()
{
    global $DB;
    $Statement = $DB->prepare("SELECT * FROM map_redirects;");
    try {
        $Statement->execute();
        return $Statement;
    } catch (PDOException $exception) {
        error_log($exception);
        return NULL;
    }
}
function removeMapRedirect($redirect)
{
    global $DB;
    $Statement = $DB->prepare("DELETE FROM map_redirects WHERE map_url = :map_url;");
    $Statement->bindParam(":map_url", $redirect, PDO::PARAM_STR);
    try {
        $Statement->execute();
        return true;
    } catch (PDOException $exception) {
        error_log($exception);
        return false;
    }
}
function allowedToCreateNewUser()
{
    global $DB;
    $Statement = $DB->prepare("SELECT preference_key FROM preferences;");
    try {
        $Statement->execute();
        if ($Statement->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    } catch (PDOException $exception) {
        error_log($exception);
        return false;
    }
}
function setAllowedToCreateNewUser($allow)
{
    global $DB;
    $result = true;
    if (allowedToCreateNewUser()) {
        $Statement = $DB->prepare("DELETE FROM preferences WHERE preference_key = 'allowedToCreateNewUser';");
        try {
            $Statement->execute();
        } catch (PDOException $exception) {
            error_log($exception);
            $result = false;
        }
    }
    if ($allow) {
        $Statement = $DB->prepare("INSERT INTO preferences (preference_key, preference_value) VALUES ('allowedToCreateNewUser', 'true');");
        try {
            $Statement->execute();
        } catch (PDOException $exception) {
            error_log($exception);
            $result = false;
        }
    }
    return $result;
}
function globalMessagesExist()
{
    global $DB;
    $Statement = $DB->prepare("SELECT * FROM global_messages;");
    try {
        $Statement->execute();
        if ($Statement->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    } catch (PDOException $exception) {
        error_log($exception);
        return false;
    }
}
function createNewGlobalMessage($message)
{
    global $DB;
    $Statement = $DB->prepare("INSERT INTO global_messages (message) VALUES (:message);");
    $Statement->bindParam(":message", $message, PDO::PARAM_STR);
    try {
        $Statement->execute();
        return true;
    } catch (PDOException $exception) {
        error_log($exception);
        return false;
    }
}
function getGlobalMessages()
{
    global $DB;
    $Statement = $DB->prepare("SELECT * FROM global_messages;");
    try {
        $Statement->execute();
        return $Statement;
    } catch (PDOException $exception) {
        error_log($exception);
        return NULL;
    }
}
function deleteGlobalMessage($id)
{
    global $DB;
    $Statement1 = $DB->prepare("DELETE FROM hidden_global_messages WHERE message_id = :id;");
    $Statement1->bindParam(":id", $id, PDO::PARAM_INT);
    $Statement2 = $DB->prepare("DELETE FROM global_messages WHERE message_id = :id;");
    $Statement2->bindParam(":id", $id, PDO::PARAM_INT);
    try {
        $Statement1->execute();
        $Statement2->execute();
        return true;
    } catch (PDOException $exception) {
        error_log($exception);
        return false;
    }
}
function getHiddenMessagesIds($uuid)
{
    global $DB;
    $result = array();
    $Statement = $DB->prepare("SELECT * FROM hidden_global_messages WHERE uuid = :uuid;");
    $Statement->bindParam(":uuid", $uuid, PDO::PARAM_STR);
    try {
        $Statement->execute();
        while ($row = $Statement->fetch(PDO::FETCH_ASSOC)) {
            array_push($result, $row["message_id"]);
        }
    } catch (PDOException $exception) {
        error_log($exception);
    }
    return $result;
}
function hideGlobalMessage($uuid, $messageId)
{
    global $DB;
    $Statement = $DB->prepare("INSERT INTO hidden_global_messages (uuid, message_id) VALUES (:uuid, :message_id);");
    $Statement->bindParam(":uuid", $uuid, PDO::PARAM_STR);
    $Statement->bindParam(":message_id", $messageId, PDO::PARAM_INT);
    try {
        $Statement->execute();
    } catch (PDOException $exception) {
        error_log($exception);
    }
}
function userMessagesExist($uuid)
{
    global $DB;
    $Statement = $DB->prepare("SELECT * FROM user_messages WHERE user_uuid = :user_uuid;");
    $Statement->bindParam(":user_uuid", $uuid, PDO::PARAM_STR);
    try {
        $Statement->execute();
        if ($Statement->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    } catch (PDOException $exception) {
        error_log($exception);
        return false;
    }
}
function getUserMessages($uuid)
{
    global $DB;
    $Statement = $DB->prepare("SELECT * FROM user_messages WHERE user_uuid = :user_uuid;");
    $Statement->bindParam(":user_uuid", $uuid, PDO::PARAM_STR);
    try {
        $Statement->execute();
        return $Statement;
    } catch (PDOException $exception) {
        error_log($exception);
        return NULL;
    }
}
function removeUserMessage($messageId)
{
    global $DB;
    $Statement = $DB->prepare("DELETE FROM user_messages WHERE message_id = :id;");
    $Statement->bindParam(":id", $messageId, PDO::PARAM_INT);
    try {
        $Statement->execute();
        return true;
    } catch (PDOException $exception) {
        error_log($exception);
        return false;
    }
}
function storeUserMessage($userUuid, $message)
{
    global $DB;
    $Statement = $DB->prepare("INSERT INTO user_messages (user_uuid, message) VALUES (:user_uuid, :message);");
    $Statement->bindParam(":user_uuid", $userUuid, PDO::PARAM_STR);
    $Statement->bindParam(":message", $message, PDO::PARAM_STR);
    try {
        $Statement->execute();
        return true;
    } catch (PDOException $exception) {
        error_log($exception);
        return false;
    }
}
function customTexturesStored()
{
    global $DB;
    $Statement = $DB->prepare("SELECT * FROM textures;");
    try {
        $Statement->execute();
        if ($Statement->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    } catch (PDOException $exception) {
        error_log($exception);
        return false;
    }
}
function getCustomTextures()
{
    global $DB;
    $Statement = $DB->prepare("SELECT * FROM textures;");
    try {
        $Statement->execute();
        return $Statement;
    } catch (PDOException $exception) {
        error_log($exception);
        return NULL;
    }
}
function removeTexture($textureTableId)
{
    global $DB;
    $Statement1 = $DB->prepare("DELETE FROM textures_restrictions  WHERE texture_table_id = :id;");
    $Statement2 = $DB->prepare("DELETE FROM textures WHERE texture_table_id = :id;");
    $Statement1->bindParam(":id", $textureTableId, PDO::PARAM_INT);
    $Statement2->bindParam(":id", $textureTableId, PDO::PARAM_INT);
    try {
        $Statement1->execute();
        $Statement2->execute();
        return true;
    } catch (PDOException $exception) {
        error_log($exception);
        return false;
    }
}
function storeTexture($id, $layer, $url)
{
    global $DB;
    $Statement = $DB->prepare("INSERT INTO textures (texture_id, texture_name, texture_layer, texture_url) VALUES (:texture_id, :texture_id, :texture_layer, :texture_url);");
    $Statement->bindParam(":texture_id", $id, PDO::PARAM_STR);
    $Statement->bindParam(":texture_name", $id, PDO::PARAM_STR);
    $Statement->bindParam(":texture_layer", $layer, PDO::PARAM_STR);
    $Statement->bindParam(":texture_url", $url, PDO::PARAM_STR);
    try {
        $Statement->execute();
        return true;
    } catch (PDOException $exception) {
        error_log($exception);
        return false;
    }
}
function storeTextureTag($textureId, $textureTag)
{
    global $DB;
    $Statement = $DB->prepare("INSERT INTO textures_restrictions (texture_table_id, tag) VALUES (:texture_table_id, :tag);");
    $Statement->bindParam(":texture_table_id", $textureId, PDO::PARAM_INT);
    $Statement->bindParam(":tag", $textureTag, PDO::PARAM_STR);
    try {
        $Statement->execute();
        return true;
    } catch (PDOException $exception) {
        error_log($exception);
        return false;
    }
}
function getTextureTags($textureId)
{
    global $DB;
    $result = array();
    $Statement = $DB->prepare("SELECT tag FROM textures_restrictions WHERE texture_table_id = :texture_table_id;");
    $Statement->bindParam(":texture_table_id", $textureId, PDO::PARAM_INT);
    try {
        $Statement->execute();
        while ($row = $Statement->fetch(PDO::FETCH_ASSOC)) {
            array_push($result, $row["tag"]);
        }
    } catch (PDOException $exception) {
        error_log($exception);
    }
    return $result;
}
function getLastTextureId()
{
    global $DB;
    $Statement = $DB->prepare("SELECT texture_table_id FROM textures ORDER BY texture_table_id DESC LIMIT 1;");
    try {
        $Statement->execute();
        if ($Statement->rowCount() > 0) {
            $row = $Statement->fetch(PDO::FETCH_ASSOC);
            return $row["texture_table_id"];
        } else {
            return -1;
        }
    } catch (PDOException $exception) {
        error_log($exception);
        return -1;
    }
}
function reportUser($reportedUserUuid, $reportedUserComment, $reporterUserUuid, $reportWorldSlug)
{
    global $DB;
    $Statement = $DB->prepare("INSERT INTO reports (reportedUserUuid, reportedUserComment, reporterUserUuid, reportWorldSlug) VALUES (:reportedUserUuid, :reportedUserComment, :reporterUserUuid, :reportWorldSlug)");
    $Statement->bindParam(":reportedUserUuid", $reportedUserUuid, PDO::PARAM_STR);
    $Statement->bindParam(":reportedUserComment", $reportedUserComment, PDO::PARAM_STR);
    $Statement->bindParam(":reporterUserUuid", $reporterUserUuid, PDO::PARAM_STR);
    $Statement->bindParam(":reportWorldSlug", $reportWorldSlug, PDO::PARAM_STR);
    try {
        $Statement->execute();
    } catch (PDOException $exception) {
        error_log($exception);
    }
}
function reportsStored()
{
    global $DB;
    $Statement = $DB->prepare("SELECT * FROM reports;");
    try {
        $Statement->execute();
        if ($Statement->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    } catch (PDOException $exception) {
        error_log($exception);
        return false;
    }
}
function getReports()
{
    global $DB;
    $Statement = $DB->prepare("SELECT * FROM reports;");
    try {
        $Statement->execute();
        return $Statement;
    } catch (PDOException $exception) {
        error_log($exception);
        return NULL;
    }
}
function removeReport($reportId)
{
    global $DB;
    $Statement = $DB->prepare("DELETE FROM reports WHERE report_id = :id;");
    $Statement->bindParam(":id", $reportId, PDO::PARAM_INT);
    try {
        $Statement->execute();
        return true;
    } catch (PDOException $exception) {
        error_log($exception);
        return false;
    }
}


# LTI
function addEmptyLtiRegistration($name, $autoRegistration)
{
    global $DB;
    $Statement = $DB->prepare("INSERT INTO lti_registration (name, auto_registration) VALUES (:name, :auto_registration)");
    $Statement->bindParam(":name", $name, PDO::PARAM_STR);
    $Statement->bindParam(":auto_registration", $autoRegistration, PDO::PARAM_BOOL);
    try {
        $Statement->execute();
        return true;
    } catch (PDOException $exception) {
        error_log($exception);
        return false;
    }
}

function addLtiDeployment($registrationId, $deploymentName, $deploymentId)
{
    global $DB;
    $Statement = $DB->prepare("INSERT INTO lti_deployment (registration_id, name, deployment_id) VALUES (:registration_id, :name, :deployment_id)");
    $Statement->bindParam(":registration_id", $registrationId, PDO::PARAM_STR);
    $Statement->bindParam(":name", $deploymentName, PDO::PARAM_STR);
    $Statement->bindParam(":deployment_id", $deploymentId, PDO::PARAM_STR);
    try {
        $Statement->execute();
        return true;
    } catch (PDOException $exception) {
        error_log($exception);
        return false;
    }
}

function getAllLTIDeployments($registrationId)
{
    global $DB;
    $Statement = $DB->prepare("SELECT * FROM lti_deployment where registration_id = :registrationId");
    $Statement->bindParam(":registrationId", $registrationId, PDO::PARAM_STR);
    try {
        $Statement->execute();
        return $Statement;
    } catch (PDOException $exception) {
        error_log("Error retrieving Registrations:" . $exception);
        return null;
    }
}

function getLTIDeployment($deploymentId, $registrationId)
{
    global $DB;
    $Statement = $DB->prepare("SELECT * FROM lti_deployment where deployment_id = :deploymentId and registration_id = :registrationId");
    $Statement->bindParam(":deploymentId", $deploymentId, PDO::PARAM_STR);
    $Statement->bindParam(":registrationId", $registrationId, PDO::PARAM_STR);
    try {
        $Statement->execute();
        $row = $Statement->fetch(PDO::FETCH_ASSOC);
        if ($Statement->rowCount() > 0) {
            return \Packback\Lti1p3\LtiDeployment::new()
                ->setDeploymentId($row["deployment_id"]);
        } else {
            return null;
        }
    } catch (PDOException $exception) {
        error_log($exception);
        return null;
    }
}

function removeLTIDeployment($deploymentId, $registrationId)
{
    global $DB;
    $Statement = $DB->prepare("DELETE FROM lti_deployment where deployment_id = :deploymentId and registration_id = :registrationId");
    $Statement->bindParam(":deploymentId", $deploymentId, PDO::PARAM_STR);
    $Statement->bindParam(":registrationId", $registrationId, PDO::PARAM_STR);
    try {
        $Statement->execute();
        return true;
    } catch (PDOException $exception) {
        error_log("Error removing deployment with id:" . $deploymentId . ": " . $exception);
        return false;
    }
}

# Removes all deployments for specified registrationId
function removeAllLTIDeployment($registrationId)
{
    global $DB;
    $Statement = $DB->prepare("DELETE FROM lti_deployment where registration_id = :registrationId");
    $Statement->bindParam(":registrationId", $registrationId, PDO::PARAM_STR);
    try {
        $Statement->execute();
        return true;
    } catch (PDOException $exception) {
        error_log("Error removing all Deployments:" . $exception);
        return false;
    }
}

function getLTIRegistrationData($registrationId)
{
    global $DB;
    $Statement = $DB->prepare("SELECT * FROM lti_registration where registration_id = :registrationId");
    $Statement->bindParam(":registrationId", $registrationId, PDO::PARAM_STR);
    try {
        $Statement->execute();
        if ($Statement->rowCount() > 0) {
            return $Statement->fetch(PDO::FETCH_ASSOC);
        } else {
            return null;
        }
    } catch (PDOException $exception) {
        error_log("Error retrieving Registration:" . $exception);
        return null;
    }
}

function getLTIRegistrationId($platformId, $clientId)
{
    global $DB;
    $Statement = $DB->prepare("SELECT registration_id FROM lti_registration where platform_id = :platformId AND client_id = :clientId");
    $Statement->bindParam(":platformId", $platformId, PDO::PARAM_STR);
    $Statement->bindParam(":clientId", $clientId, PDO::PARAM_STR);
    try {
        $Statement->execute();
        if ($Statement->rowCount() > 0) {
            return $Statement->fetch(PDO::FETCH_ASSOC)["registration_id"];
        } else {
            return null;
        }
    } catch (PDOException $exception) {
        error_log("Error retrieving Registration:" . $exception);
        return null;
    }
}
function getLTIRegistrationByIss($platformId)
{
    global $DB;
    $Statement = $DB->prepare("SELECT * FROM lti_registration where platform_id = :platformId");
    $Statement->bindParam(":platformId", $platformId, PDO::PARAM_STR);
    try {
        $Statement->execute();
        if ($Statement->rowCount() > 0) {
            return $Statement->fetch(PDO::FETCH_ASSOC);
        } else {
            return null;
        }
    } catch (PDOException $exception) {
        error_log("Error retrieving Registration:" . $exception);
        return null;
    }
}

function getAllLTIRegistrations()
{
    global $DB;
    $Statement = $DB->prepare("SELECT * FROM lti_registration");
    try {
        $Statement->execute();
        return $Statement;
    } catch (PDOException $exception) {
        error_log("Error retrieving Registrations:" . $exception);
        return null;
    }
}


function removeLTIRegistration($registrationId)
{
    global $DB;
    $Statement = $DB->prepare("DELETE FROM lti_registration where registration_id = :registrationId");
    $Statement->bindParam(":registrationId", $registrationId, PDO::PARAM_STR);
    try {
        $Statement->execute();
        return true;
    } catch (PDOException $exception) {
        error_log("Error removing Registration: " . $exception);
        return false;
    }
}

function getLTIDeploymentCount($registrationId)
{
    global $DB;
    $Statement = $DB->prepare("SELECT COUNT(*) as count FROM lti_deployment where registration_id = :registrationId");
    $Statement->bindParam(":registrationId", $registrationId, PDO::PARAM_STR);
    try {
        $Statement->execute();
        $data = $Statement->fetch(PDO::FETCH_ASSOC);
        if ($Statement->rowCount() > 0) {
            return $data['count'];
        } else {
            return null;
        }
    } catch (PDOException $exception) {
        error_log($exception);
        return null;
    }
}

function getLTIRegistrationCount()
{
    global $DB;
    $Statement = $DB->prepare("SELECT COUNT(*) as count FROM lti_registration");
    try {
        $Statement->execute();
        $data = $Statement->fetch(PDO::FETCH_ASSOC);
        if ($Statement->rowCount() > 0) {
            return $data['count'];
        } else {
            return null;
        }
    } catch (PDOException $exception) {
        error_log($exception);
        return null;
    }
}

function editLTIRegistration($registrationId, $name, $platformId, $clientId, $authenticationRequestUrl, $accessTokenUrl, $jwksUrl)
{
    global $DB;

    $Statement = $DB->prepare("UPDATE lti_registration SET name = NULLIF(:name, ''), platform_id = NULLIF(:platform_id, ''), client_id = NULLIF(:client_id, ''), authentication_request_url = NULLIF(:authenticationRequestUrl, ''), access_token_url = NULLIF(:accessTokenUrl, ''), jwks_url = NULLIF(:jwksUrl, '') WHERE registration_id = :registrationId;");
    $Statement->bindParam(":registrationId", $registrationId, PDO::PARAM_INT);
    $Statement->bindParam(":name", $name, PDO::PARAM_STR);
    $Statement->bindParam(":platform_id", $platformId, PDO::PARAM_STR);
    $Statement->bindParam(":client_id", $clientId, PDO::PARAM_STR);
    $Statement->bindParam(":authenticationRequestUrl", $authenticationRequestUrl, PDO::PARAM_STR);
    $Statement->bindParam(":accessTokenUrl", $accessTokenUrl, PDO::PARAM_STR);
    $Statement->bindParam(":jwksUrl", $jwksUrl, PDO::PARAM_STR);

    try {
        $Statement->execute();
        return true;
    } catch (PDOException $exception) {
        error_log($exception);
        return false;
    }
}

function createLtiUser($uuid, $sub, $deploymentId, $registrationId, $name, $givenName, $familyName, $email)
{
    global $DB;
    $Statement = $DB->prepare("INSERT INTO lti_users
                (uuid, sub, deployment_id, registration_id, name, given_name, family_name, email) 
        VALUES (:uuid, :sub, :deployment_id, :registration_id, :name, :given_name, :family_name, :email)");
    $Statement->bindParam(":uuid", $uuid, PDO::PARAM_STR);
    $Statement->bindParam(":sub", $sub, PDO::PARAM_STR);
    $Statement->bindParam(":deployment_id", $deploymentId, PDO::PARAM_STR);
    $Statement->bindParam(":registration_id", $registrationId, PDO::PARAM_STR);
    $Statement->bindParam(":name", $name, PDO::PARAM_STR);
    $Statement->bindParam(":given_name", $givenName, PDO::PARAM_STR);
    $Statement->bindParam(":family_name", $familyName, PDO::PARAM_STR);
    $Statement->bindParam(":email", $email, PDO::PARAM_STR);
    try {
        $Statement->execute();
        return true;
    } catch (PDOException $exception) {
        error_log($exception);
        return false;
    }
}
function removeLtiUser($uuid)
{
    global $DB;
    $Statement = $DB->prepare("DELETE FROM lti_users WHERE uuid=:uuid");
    $Statement->bindParam(":uuid", $uuid, PDO::PARAM_STR);
    try {
        $Statement->execute();
        return true;
    } catch (PDOException $exception) {
        error_log($exception);
        return false;
    }
}

function getLtiUserData($uuid)
{
    global $DB;
    $Statement = $DB->prepare("SELECT * FROM lti_users WHERE uuid=:uuid;");
    $Statement->bindParam(":uuid", $uuid, PDO::PARAM_STR);
    try {
        $Statement->execute();
        return $Statement->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $exception) {
        error_log($exception);
        return NULL;
    }
}

function getLtiUserUuidIfExistent($sub, $registrationId)
{
    global $DB;
    $Statement = $DB->prepare("SELECT uuid FROM lti_users WHERE sub = :sub AND registration_id = :registration_id;");
    $Statement->bindParam(":sub", $sub, PDO::PARAM_STR);
    $Statement->bindParam(":registration_id", $registrationId, PDO::PARAM_STR);
    try {
        $Statement->execute();
        if ($Statement->rowCount() > 0) {
            return $Statement->fetch(PDO::FETCH_ASSOC)["uuid"];
        } else {
            return null;
        }
    } catch (PDOException $exception) {
        error_log($exception);
        return null;
    }
}

function addLtiRole($uuid, $newRole)
{
    global $DB;
    $Statement = $DB->prepare("INSERT INTO lti_roles (uuid, role) VALUES (:uuid, :role);");
    $Statement->bindParam(":uuid", $uuid, PDO::PARAM_STR);
    $Statement->bindParam(":role", $newRole, PDO::PARAM_STR);
    try {
        $Statement->execute();
        return true;
    } catch (PDOException $exception) {
        error_log($exception);
        return false;
    }
}

function removeAllLtiRoles($uuid)
{
    global $DB;
    $Statement = $DB->prepare("DELETE FROM lti_roles WHERE uuid=:uuid");
    $Statement->bindParam(":uuid", $uuid, PDO::PARAM_STR);
    try {
        $Statement->execute();
        return true;
    } catch (PDOException $exception) {
        error_log($exception);
        return false;
    }
}

function getLtiRoles($uuid)
{
    global $DB;
    $result = [];
    $Statement = $DB->prepare("SELECT * FROM lti_roles WHERE uuid=:uuid");
    $Statement->bindParam(":uuid", $uuid, PDO::PARAM_STR);
    try {
        $Statement->execute();
        while ($row = $Statement->fetch(PDO::FETCH_ASSOC)) {
            array_push($result, $row["role"]);
        }
        if (sizeof($result) == 0) {
            return null;
        }
    } catch (PDOException $exception) {
        error_log($exception);
    }
    return $result;
}

function getTagsForLtiRole($role)
{
    global $DB;
    $result = [];
    $Statement = $DB->prepare("SELECT * FROM lti_role_tag_mappings WHERE role=:role");
    $Statement->bindParam(":role", $role, PDO::PARAM_STR);
    try {
        $Statement->execute();
        while ($row = $Statement->fetch(PDO::FETCH_ASSOC)) {
            array_push($result, $row["tag"]);
        }
        if (sizeof($result) == 0) {
            return null;
        }
    } catch (PDOException $exception) {
        error_log($exception);
    }
    return $result;
}

function getAllMappedLtiRoles()
{
    global $DB;
    $result = [];
    $Statement = $DB->prepare("SELECT DISTINCT role FROM lti_role_tag_mappings");
    try {
        $Statement->execute();
        while ($row = $Statement->fetch(PDO::FETCH_ASSOC)) {
            array_push($result, $row["role"]);
        }
        if (sizeof($result) == 0) {
            return null;
        }
    } catch (PDOException $exception) {
        error_log($exception);
    }
    return $result;
}

function addTagForLtiRole($role, $newTag)
{
    global $DB;
    $Statement = $DB->prepare("INSERT INTO lti_role_tag_mappings (role, tag) VALUES (:role, :tag);");
    $Statement->bindParam(":role", $role, PDO::PARAM_STR);
    $Statement->bindParam(":tag", $newTag, PDO::PARAM_STR);
    try {
        $Statement->execute();
        return true;
    } catch (PDOException $exception) {
        error_log($exception);
        return false;
    }
}

function removeTagForRole($role, $oldTag)
{
    global $DB;
    $Statement = $DB->prepare("DELETE FROM lti_role_tag_mappings WHERE role=:role AND tag=:tag");
    $Statement->bindParam(":role", $role, PDO::PARAM_STR);
    $Statement->bindParam(":tag", $oldTag, PDO::PARAM_STR);
    try {
        $Statement->execute();
        return true;
    } catch (PDOException $exception) {
        error_log($exception);
        return false;
    }
}

function removeAllTagsForRole($role)
{
    global $DB;
    $Statement = $DB->prepare("DELETE FROM lti_role_tag_mappings WHERE role=:role");
    $Statement->bindParam(":role", $role, PDO::PARAM_STR);
    try {
        $Statement->execute();
        return true;
    } catch (PDOException $exception) {
        error_log($exception);
        return false;
    }
}

function getLtiUuidFromEmail($email)
{
    global $DB;
    $Statement = $DB->prepare("SELECT uuid FROM lti_users WHERE email = :email");
    $Statement->bindParam(":email", $email, PDO::PARAM_STR);
    try {
        $Statement->execute();
        $row = $Statement->fetch(PDO::FETCH_ASSOC);
        if ($Statement->rowCount() > 0) {
            return $uuid = $row["uuid"];
        } else {
            return NULL;
        }
    } catch (PDOException $exception) {
        error_log($exception);
        return NULL;
    }
}

function ltiUserExists($uuid)
{
    global $DB;
    $Statement = $DB->prepare("SELECT * FROM lti_users WHERE uuid=:uuid;");
    $Statement->bindParam(":uuid", $uuid, PDO::PARAM_STR);
    try {
        $Statement->execute();
        if ($Statement->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    } catch (PDOException $exception) {
        error_log($exception);
        return false;
    }
}

function getALlLtiUserUuidsForDeployment($deploymentId, $registrationId)
{
    global $DB;
    $Statement = $DB->prepare("SELECT uuid FROM lti_users WHERE registration_id=:registration_id and deployment_id=:deployment_id;");
    $Statement->bindParam(":registration_id", $registrationId, PDO::PARAM_STR);
    $Statement->bindParam(":deployment_id", $deploymentId, PDO::PARAM_STR);
    try {
        $Statement->execute();
        return $Statement;
    } catch (PDOException $exception) {
        error_log($exception);
        return NULL;
    }
}
