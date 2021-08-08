<?php
/*
verify ban -> returns AdminBannedData
only works on private maps -> '@' url part
is_banned is used, message not but most likely equal to ban message
*/
header("Content-Type:application/json");
require 'authentication.php';
require 'database_operations.php';

try {
    $DB = new PDO("mysql:dbname=".getenv('DB_MYSQL_DATABASE').";host=admin-db;port=3306",
        getenv('DB_MYSQL_USER'), getenv('DB_MYSQL_PASSWORD'));
}
catch (PDOException $exception) {
    return;
}

if (!isAuthorized()) {
    http_response_code(403);
    die();
}

if ((isset($_GET["ipAddress"])) && (isset($_GET["token"])) && (isset($_GET["roomUrl"]))) {
    $uuid = htmlspecialchars($_GET["token"]);

    // find out whether user is banned
    $isBanned = isBanned($uuid);
    $result['is_banned'] = $isBanned;

    // get ban message if user is banned
    if ($isBanned) {
        $result['message'] = getBanMessage($uuid);
    } else {
        $result['message'] = "";
    }

    echo json_encode($result);
} else {
    http_response_code(400);
    die();
}

$DB = NULL;
?>
