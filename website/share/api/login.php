<?php
/*
fetch member data by token -> returns AdminApiData
maybe useful for generating the admin join links
currently, the uuid, the organization slug, the world slug, the room slug,
the mapUrlStart and the textures are being considered
*/
header("Content-Type:application/json");
require 'database_operations.php';

if (isset($_GET["token"])) {
    $uuid = $_GET["token"];

    createAccountIfNotExistent($uuid);

    $tags = getTags($uuid);

    $result['organizationSlug'] = "hsm";
    $result['worldSlug'] = "computerscience";
    $result['roomSlug'] = "laboratory";
    $result['mapUrlStart'] = "maps/gaming/map.json";
    $result['tags'] = $tags;
    $result['policy_type'] = 1;
    $result['userUuid'] = $uuid;
    $result['messages'] = array();
    $result['textures'] = array();

    echo json_encode($result);
} else {
    http_response_code(400);
    die();
}
?>
