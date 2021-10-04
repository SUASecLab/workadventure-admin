<?php
header("Content-Type:application/json");
session_start();
require 'login_functions.php';
require 'api/database_operations.php';

try {
    $DB = new PDO(
        "mysql:dbname=" . getenv('DB_MYSQL_DATABASE') . ";host=admin-db;port=3306",
        getenv('DB_MYSQL_USER'),
        getenv('DB_MYSQL_PASSWORD')
    );
} catch (PDOException $exception) {
    error_log($exception);
    die();
}

if (!isLoggedIn()) {
    http_response_code(403);
    die();
}

$result = array();

if (isset($_GET["userId"])) {
    $userId = htmlspecialchars($_GET["userId"]);

    if ((isset($_GET["name"])) &&
        (isset($_GET["email"])) &&
        (isset($_GET["visitCardUrl"]))
    ) {
        $name = trim(htmlspecialchars($_GET["name"]));
        $email = trim(htmlspecialchars($_GET["email"]));
        $visitCardUrl = trim(htmlspecialchars($_GET["visitCardUrl"]));

        if ((strlen($name) > 0) &&
            (strlen($email) > 0)) {
            $success = updateUserData($userId, $name, $email, $visitCardUrl);
            if ($success) {
                $result["message"] = "User data has been updated";
            } else {
                $result["message"] = "User data could not be updated";
            }
            $result["success"] = $success;
        } else {
            $result["success"] = false;
            $result["message"] = "Invalid user data";
        }
    }

    if (isset($_GET["addTag"])) {
        $tag = trim(htmlspecialchars($_GET["addTag"]));
        if (strlen($tag > 0)) {
            $success = addTag($userId, $tag);
            if ($success) {
                $result["message"] = "The tag " . $tag . " has been added";
            } else {
                $result["message"] = "The tag " . $tag . " could not be added";
            }
            $result["success"] = $success;
        } else {
            $result["success"] = false;
            $result["message"] = "Invalid tag entered";
        }
    }


    if (isset($_GET["removeTag"])) {
        $tag = trim(htmlspecialchars($_GET["removeTag"]));
        if (strlen($tag > 0)) {
            $success = removeTag($userId, $tag);
            if ($success) {
                $result["message"] = "The tag " . $tag . " has been removed";
            } else {
                $result["message"] = "The tag " . $tag . " could not be removed";
            }
            $result["success"] = $success;
        } else {
            $result["success"] = false;
            $result["message"] = "Invalid tag";
        }
    }

    if (isset($_GET["banReason"])) {
        if (isBanned($userId)) {
            $success = liftBan($userId);
            if ($success) {
                $result["message"] = "The ban has been lifted";
            } else {
                $result["message"] = "The ban could not be lifted";
            }
            $result["success"] = $success;
        } else {
            $reason = htmlspecialchars($_GET["banReason"]);
            if (strlen($reason) > 0) {
                $success = banUser($userId, $reason);
                if ($success) {
                    $result["message"] = "The user has been banned";
                } else {
                    $result["message"] = "The user could not be banned";
                }
                $result["success"] = $success;
            } else {
                $result["success"] = false;
                $result["message"] = "Invalid ban reason";
            }
        }
    }

    if (isset($_GET["storeMessage"])) {
        $message = htmlspecialchars($_GET["storeMessage"]);
        if (strlen($message) > 10) {
            $success = storeUserMessage($userId, $message);
            $result["success"] = $success;
            if ($success) {
                $result["message"] = "The message has been stored";
            } else {
                $result["message"] = "The message could not be stored";
            }
        } else {
            $result["success"] = false;
            $result["message"] = "The message must have at least ten characters";
        }
    }

    if (isset($_GET["removeMessage"])) {
        $messageId = htmlspecialchars($_GET["removeMessage"]);
        $success = removeUserMessage($messageId);
        $result["success"] = $success;
        if ($success) {
            $result["message"] = "The message has been removed";
        } else {
            $result["message"] = "The message could not be removed";
        }
    }

    $userData = getUserData($userId);
    $uuid = $userData['uuid'];

    $result["userData"] = $userData;
    $result["accessLink"] = "https://" . getenv('DOMAIN') . "/register/" . $uuid;
    $result["tags"] = getTags($uuid);
    $messageIterator = getUserMessages($uuid);
    $messages = array();
    while ($row = $messageIterator->fetch(PDO::FETCH_ASSOC)) {
        $message = array(
            "text" => $row["message"],
            "id" => $row["message_id"]
        );
        array_push($messages, $message);
    }
    $result["messages"] = $messages;

    $result["banned"] = isBanned($uuid);
    if (isBanned($uuid)) {
        $result["banReason"] = getBanMessage($uuid);
    }
}
echo json_encode($result);
$DB = NULL;
