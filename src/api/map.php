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


    if (strcmp($playUri,"https://" . getenv("DOMAIN") . "/login") === 0) {
       $shortUri = getenv('START_ROOM_URL');
       error_log("LOGIN URL CALLED!");
    }


    $resultMap = getMap($shortUri);
    $result = array();
    if ($resultMap === NULL) {
         http_response_code(404);
        die();
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

                echo json_encode(apiErrorMessage("MAP_ACCESS_FORBIDDEN", "Access forbidden",
                    "Your are not allowed to enter this room."));

                $DB = NULL;
                die();
            }
        }

        // Provide MapDetailsData
        //wamUrl
        // substring is to remove the leading "/~" characters, this is necessary to prevent
        // a 404 error in the map storage
        $result["wamUrl"] = "https://" . getenv("DOMAIN") . substr($resultMap["wamUrl"], 2);

        // mapUrl
        $result["mapUrl"] = "https://".$resultMap["mapUrl"];

        // authenticationMandatory
        $result["authenticationMandatory"] = $resultMap["policyNumber"] !== 1;

        // group
        $result["group"] = null; // TODO: set to org/world

        // contactPage
        //$result["contactPage"] = null; // not used by us

        // opidLogoutRedirectUrl
        //$result["opidLogoutRedirectUrl"] = null; // not used by us

        // opidWokaNamePolicy
        //$result["opidWokaNamePolicy"] = null; // not used by us

        // expireOn
        //$result["expireOn"] = "2099-01-01T00:00:00-00:00"; // not used by us

        // canReport
        $result["canReport"] = false; // we disable the reporting feature

        // editable
        $result["editable"] = false; // we disable the map editor

        // loadingCowebsiteLogo
        //$result["loadingCowebsiteLogo"] = null; // not used by us

        // miniLogo
        //$result["miniLogo"] = null; // not used by us

        // loadingLogo
        //$result["loadingLogo"] = null; // not used by us

        // loginSceneLogo
        //$result["loginSceneLogo"] = null; // not used by us

        // showPoweredBy
        //$result["showPoweredBy"] = null; // not used by us

        // thirdParty
        //$result["thirdParty"] = null; // not used by us

        // metadata
        //$result["metadata"] = null; // not used by us

        // roomName
        //$result["roomName"] = null; // not used by us

        // pricingUrl
        //$result["pricingUrl"] = null; // not used by us

        // enableChat
        $result["enableChat"] = true; // not used by us

        // enableChatUpload
        $result["enableChatUpload"] = false; // not used by us

        // enableChatOnlineList
        $result["enableChatOnlineList"] = true; // not used by us

        // enableChatDisconnectedList
        $result["enableChatDisconnectedList"] = false; // not used by us

        // metatags
        //$result["metatags"] = null; // not used by us

        // legals
        //$result["legals"] = null; // not used by us

        // customizeWokaScene
        //$result["customizeWokaScene"] = null; // not used by us

        // backgroundColor
        //$result["backgroundColor"] = null; // not used by us

        // reportIssuesUrl
        //$result["reportIssuesUrl"] = null; // not used by us

        // entityCollectionsUrls
        //$result["entityCollectionsUrls"] = null; // not used by us

        // errorSceneLogo
        //$result["errorSceneLogo"] = null; // not used by us

        // SUASecLab customizations
        if (getenv("ENABLE_SUAS_EXTENSIONS") === "true") {
            // contact page
            $result["contactPage"] = "https://itsec.hs-sm.de/";
            //loadingCowebsiteLogo -> cowebsite loading page logo
            $result["loadingCowebsiteLogo"] = "/maps/logos/logo_mini.png";
            //loadingLogo -> logo used on loading page
            $result["loadingLogo"] = "/maps/logos/logo_lab.png";
            //miniLogo -> miniLogo (?)
            $result["loginSceneLogo"] = "/maps/logos/logo_mini.png";
            //loginSceneLogo -> logo shown on login scene
            $result["loginSceneLogo"] = "/maps/logos/logo_lab.png";
            // show powered by workadventure
            $result["showPoweredBy"] = true;
            // backgroundColor -> set new background color (html code)
            $result["backgroundColor"] = "#012d5c";
            //errorSceneLogo -> error Logo
            $result["errorSceneLogo"] = "/maps/logos/logo_error.png";
        }

        echo json_encode($result);
    }
} else {
    http_response_code(404);

    echo json_encode(apiErrorMessage("INSUFFICIENT_DATA",
        "Insufficient data provided",
        "Not enough data was provided to perform this operation."));
}
$DB = NULL;
?>
