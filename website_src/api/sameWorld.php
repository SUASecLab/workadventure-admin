<?php
header("Content-Type:application/json");
require_once ('../util/api_authentication.php');
require_once ('../util/database_operations.php');
$DB = getDatabaseHandleOrDie();
authorizeOrDie();
if (isset($_GET["roomUrl"])) {
    $maps = getAllMaps();
    $result = array();
    while ($row = $maps->fetch(PDO::FETCH_ASSOC)) {
        array_push($result, "https://" . getenv('DOMAIN') . $row["map_url"]);
    }
    echo json_encode($result);
} else {
    die();
}
$DB = NULL;
?>