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

// remove map if requested
if (isset($_GET["removeMap"])) {
    $mapToRemove = htmlspecialchars($_GET["removeMap"]);
    if ((removeMapTags($mapToRemove)) &&  (removeMap($mapToRemove))) {
        $result["deleteSuccess"] = true;
    } else {
        $result["deleteSuccess"] = false;
    }
    $result["mapToRemove"] = $mapToRemove;
} else if ((isset($_GET["addMap"])) &&
    (isset($_GET["fileUrl"])) &&
    (isset($_GET["access"]))
) {
    $validInput = true;
    $mapUrl = htmlspecialchars($_GET["addMap"]);
    $mapFileUrl = htmlspecialchars($_GET["fileUrl"]);
    $accessRestriction = intval(htmlspecialchars($_GET["access"]));
    $tagsArray = array();

    if ((($accessRestriction) < 1) || ($accessRestriction > 3)) {
        $validInput = false;
        error_log($accessRestriction);
    }
    if (strlen($mapUrl) == 0) {
        $validInput = false;
    }
    if (strlen($mapFileUrl) == 0) {
        $validInput = false;
    }
    if (($validInput) && ($accessRestriction == 3)) {
        if (isset($_GET["tags"])) {
            $tags = json_decode($_GET["tags"]);
            if (sizeof($tags) == 0) {
                $validInput = false;
            } else {
                foreach ($tags as $tag) {
                    array_push($tagsArray, trim($tag));
                }
                error_log(json_encode($tagsArray));
            }
        } else {
            $validInput = false;
        }
    }

    if ($validInput) {
        $mapUrlPrefix = "/@/org/" . getenv('DOMAIN') . "/";
        $mapUrl = $mapUrlPrefix . $mapUrl;

        $storedMap = storeMapFileUrl($mapUrl, $mapFileUrl, $accessRestriction);
        $tagsSucess = true;
        foreach ($tagsArray as $tag) {
            $tagsSucess &= addMapTag($mapUrl, $tag);
        }

        if ($storedMap && $tagsSucess) {
            $result["storeSuccess"] = true;
        } else {
            $result["storeSuccess"] = false;
            if (!$storedMap) {
                $result["errorMessage"] = "Could not store map";
            }
            if (!$tagsSucess) {
                $result["errorMessage"] = "Could not store tags";
            }
            if ((!$storedMap) && (!$tagsSucess)) {
                $result["errorMessage"] = "Could not store map and tags";
            }
        }
    } else {
        $result["storeSuccess"] = false;
        $result["errorMessage"] = "Invalid input";
    }
} else {
    if (getMapFileUrl(getenv('START_ROOM_URL'))) {
        $result["startRoomSet"] = true;
    } else {
        $result["startRoomSet"] = false;
        $result["startRoom"] = getenv('START_ROOM_URL');
    }

    $maps = getAllMaps();
    $mapsArray = array();
    while ($row = $maps->fetch(PDO::FETCH_ASSOC)) {
        $map = array(
            "url" => $row["map_url"],
            "file" => $row["map_file_url"],
            "policy" => $row["policy"],
            "tags" => getMapTags($row["map_url"]),
        );
        array_push($mapsArray, $map);
    }
    $result["maps"] = $mapsArray;
}
echo json_encode($result);
$DB = NULL;
?>