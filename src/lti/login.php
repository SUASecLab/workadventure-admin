<?php
//session_start();
require_once('../util/lti_helper_functions.php');
require_once('../vendor/autoload.php');
require_once('./db.php');
require_once('./cookie.php');
require_once('./cache.php');

$DB = getDatabaseHandleOrPrintError();

use \Packback\Lti1p3;

if (!isset($_POST["iss"])) {
    showErrorAndDie("LTI-Login failed: Issuer is missing.");
}

if (!isset($_POST["login_hint"])) {
    showErrorAndDie("LTI-Login failed: Login-Hint is missing.");
}

if (!isset($_POST["target_link_uri"])) {
    showErrorAndDie("LTI-Login failed: Target-Link-Uri is missing.");
    die();
}
$iss = $_POST["iss"];

$reg = getLTIRegistrationByIss($iss);

if (!$reg || !registrationExistsAndComplete($reg["registration_id"])) {
    // Redirect to Error Page
    showErrorAndDie("LTI-Login failed: Platform-Registration not working.");
}


try {
    Lti1p3\LtiOidcLogin::new(new issuer_database(), new LtiCache(), new LtiCookie())
        ->doOidcLoginRedirect($_POST["target_link_uri"])
        ->doRedirect();
} catch (Lti1p3\OidcException $e) {
    showErrorAndDie("LTI-Login failed with error message: " . $e->getMessage());
}

$DB = NULL;
