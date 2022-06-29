<?php
session_start();

use Packback\Lti1p3\Interfaces\IDatabase;
use Packback\Lti1p3\LtiDeployment;
use Packback\Lti1p3\LtiRegistration;

require_once('../util/database_operations.php');
require_once('../util/lti_helper_functions.php');



class issuer_database implements IDatabase
{

    public function findRegistrationByIssuer($iss, $clientId = null): ?LtiRegistration
    {
        $reg = getLTIRegistrationByIss($iss);

        if (!registrationExistsAndComplete($reg["registration_id"])) {
            return null;
        }

        $registration = getLTIRegistrationByIss($iss);
        return LtiRegistration::new()
            ->setClientId($registration['client_id'])
            ->setIssuer($registration["platform_id"])
            ->setAuthLoginUrl($registration["authentication_request_url"])
            ->setAuthTokenUrl($registration["access_token_url"])
            ->setKeySetUrl($registration["jwks_url"])
            ->setKid(getenv("LTI_KID"))
            ->setToolPrivateKey(getenv("LTI_SECRET_KEY"));
    }

    public function findDeployment($iss, $deploymentId, $clientId = null): ?LtiDeployment
    {
        return getLTIDeploymentByIss($deploymentId, $iss);
    }
}
