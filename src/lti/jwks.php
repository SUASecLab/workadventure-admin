<?php
require_once('../vendor/autoload.php');

use \Packback\Lti1p3;

require_once('./db.php');
$DB = getDatabaseHandleOrPrintError();

header("Content-Type:application/json");

$privatekey = getenv("LTI_SECRET_KEY");
$kid = getenv("LTI_KID");

$test = new issuer_database();

$endpoint = Lti1p3\JwksEndpoint::new([$kid => $privatekey]);

$endpoint->outputJwks();

$DB = NULL;
