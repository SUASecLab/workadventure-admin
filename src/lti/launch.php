<?php
require_once('../util/lti_helper_functions.php');
require_once('../vendor/autoload.php');
require_once('./db.php');
require_once('./cookie.php');
require_once('./cache.php');

$DB = getDatabaseHandleOrPrintError();

use \Packback\Lti1p3\LtiMessageLaunch;
use GuzzleHttp\Client;
use Firebase\JWT\JWT;
use Packback\Lti1p3\LtiServiceConnector;

if (!isset($_POST["id_token"])) {
    showErrorAndDie("LTI-Launch failed: Id-Token is missing!");
}
$cacheInstance = new LtiCache();
try {
    $messageLaunch = LtiMessageLaunch::new(new issuer_database(), $cacheInstance, new LtiCookie(), new LtiServiceConnector($cacheInstance, new Client(['timeout' => 30, 'defaults' => ['verify' => false]])))->validate();
} catch (Exception $e) {
    showErrorAndDie("LTI-Launch failed with error: " . $e->getMessage());
}

$jwtBody = $messageLaunch->getLaunchData();
try {
    $existentUuid = getLtiUserUuidIfExistentFromIdToken($jwtBody);
    if ($existentUuid) {
        $removed = removeLtiUserWithDependencies($existentUuid);
    }
    $createdUuid = createNewLtiUser($jwtBody);

    if ($createdUuid) {
        $data = getLtiUserData($createdUuid);
        $expiryInSeconds = (int) getenv("LTI_TOKEN_EXPIRY");
        $message_jwt = [
            'exp' => time() + $expiryInSeconds,
            'iat' => time(),
            'identifier' => $createdUuid,
            'isLtiUser' => 'true'
        ];
        if (isset($data["name"]) && $data["name"]) {
            $message_jwt['username'] = $data["name"];
        }
        $jwt = JWT::encode($message_jwt, getenv('SECRET_KEY'), "HS256", null, null);
        $location = 'https://' . getenv('DOMAIN') . '?token=' . $jwt;
        echo "<script>location.href=\"$location\";</script>";
        die();
    } else {
        echo "Creation of new Lti User failed!";
        die();
    }
} catch (Exception $e) {
    showErrorAndDie("Creation of Lti-User failed with error: " . $e->getMessage());
}
