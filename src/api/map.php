<?php
header("Content-Type:application/json");
require_once ("../util/api_authentication.php");
require_once ("../util/database_operations.php");
require_once ("../util/api_helper_functions.php");
$DB = getDatabaseHandleOrDie();
authorizeOrDie();
if (isset($_GET["playUri"])) {
    $playUri = htmlspecialchars($_GET["playUri"]);
    $shortUri = substr($playUri, strlen("https://" . getenv("DOMAIN")));
    $resultMap = getMap($shortUri);
    $result = array();
    if ($resultMap === NULL) {
        $mapRedirect = getMapRedirect($shortUri);
        if ($mapRedirect !== NULL) {
            $result["redirectUrl"] = $mapRedirect;
            echo json_encode($result);
        } else {
            http_response_code(404);
            die();
        }
    } else {
        // check if user is allowed to enter the map if a user identification is provided
        if (isset($_GET["userId"])) {
            $userID = htmlspecialchars($_GET["userId"]);
            $uuid = getUuid($userID);

            $canEnter = true;

            if (!userExists($uuid)) {
                $canEnter = false;
            }

            if (!userCanAccessMap($uuid, $shortUri)) {
                http_response_code(403);

                $error["code"] = "MAP_ACCESS_FORBIDDEN";
                $error["title"] = "Access forbidden";
                $error["subtitle"] = "Your are not allowed to enter this room.";
                $error["details"] = "";
                $error["image"] = "";

                echo json_encode($error);

                $DB = NULL;
                die();
            }
        }

        $result["mapUrl"] = "https://".$resultMap["mapFileUrl"];
        $result["policy_type"] = $resultMap["policyNumber"];
        if (array_key_exists("tags", $resultMap)) {
            $result["tags"] = $resultMap["tags"];
        } else {
            $result["tags"] = array();
        }
        $result["roomSlug"] = ""; // deprecated
        $result["contactPage"] = "";
        $result["group"] = "";

        // optional parameters
        $result["authenticationMandatory"] = $resultMap["policyNumber"] !== 1;
        // $result["iframeAuthentication"] = "https://127.0.0.1";
        // $result["expireOn"] = ;
        $result["canReport"] = false;
        // $result["loadingCowebsiteLogo"] = ;
        // $result["miniLogo"] = ;
        // $result["loadingLogo"] = ;
        // $result["loginSceneLogo"] = ;
        // $result["showPoweredBy"] = ;
        // $result["thirdParty"] = ;
        echo json_encode($result);
    }
} else {
    http_response_code(404);

    $error["code"] = "INSUFFICIENT_DATA";
    $error["title"] = "Insufficient data provided";
    $error["subtitle"] = "Not enough data was provided to perform this operation.";
    $error["details"] = "";
    $error["image"] = "";

    echo json_encode($error);
}
$DB = NULL;
?>