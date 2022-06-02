<?php
/*
fetch map details -> returns MapDetailsData
currently, the tags and the policy type are used
*/
header("Content-Type:application/json");
require_once ('../util/api_authentication.php');
require_once ('../util/database_operations.php');
require_once ('../util/api_helper_functions.php');
$DB = getDatabaseHandleOrDie();
authorizeOrDie();
if (isset($_GET["playUri"])) {
    $playUri = htmlspecialchars($_GET["playUri"]);
    $shortUri = substr($playUri, strlen("https://" . getenv('DOMAIN')));
    $resultMap = getMapFileUrl($shortUri);
    $result = array();
    if ($resultMap == NULL) {
        $mapRedirect = getMapRedirect($shortUri);
        if ($mapRedirect != NULL) {
            $result['redirectUrl'] = $mapRedirect;
        } else {
            die();
        }
    } else {
        $result['mapUrl'] = $resultMap;
        $result['policy_type'] = getMapPolicy($shortUri);
        $result['tags'] = getMapTags($shortUri);
        $result['authenticationMandatory'] = getAuthenticationMandatory($shortUri);
        $result['roomSlug'] = ''; // deprecated
        $result['contactPage'] = '';
        $result['group'] = '';
        
        // optional parameters

        $result['iframeAuthentication'] = "https://127.0.0.1";
        // unused currently
        // $result['expireOn'] = ;
        // $result['canReport'] = ;
        // $result['loadingLogo'] = ;
        // $result['loginSceneLogo'] = ;
    }
    echo json_encode($result);
} else {
    die();
}
$DB = NULL;
?>