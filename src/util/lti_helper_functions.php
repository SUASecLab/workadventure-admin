<?php
require_once('database_operations.php');
require_once('uuid_adapter.php');

use \Packback\Lti1p3\LtiConstants;

function registrationComplete($registrationId): bool
{
    $registration = getLTIRegistrationData($registrationId);
    return $registration['platform_id'] && $registration['client_id'] && $registration['authentication_request_url'] && $registration['access_token_url'] && $registration['jwks_url'];
}

function getRegistrationStatusMessage($registrationId): string
{
    $registration = getLTIRegistrationData($registrationId);
    if (registrationComplete($registrationId)) {
        return "Valid Config.";
    } else if ($registration['auto_registration']) {
        return "Automatic Config. pending.";
    } else {
        return "Manual Config. incomplete.";
    }
}

function registrationExists($registrationId): bool
{
    $t = getLTIRegistrationData($registrationId) != null;
    return $t;
}

function registrationExistsAndComplete($registrationId): bool
{
    return registrationExists($registrationId) && registrationComplete($registrationId);
}

function getRegistrationIdByIss($iss)
{
    $reg = getLTIRegistrationByIss($iss);
    if (!$reg) {
        return null;
    } else {
        return $reg["registration_id"];
    }
}

function getLTIDeploymentByIss($deploymentId, $registrationId)
{
    return getLTIDeployment($deploymentId, getRegistrationIdByIss($registrationId));
}

function showErrorAndDie($message, $response_code = 400)
{
    http_response_code($response_code);
    die($message);
}

// Delete Registration and all deployments at once
function removeLtiRegistrationWithDependencies($registrationId)
{
    $deployments = getAllLTIDeployments($registrationId);
    if (!$deployments) return true;
    while ($row = $deployments->fetch(PDO::FETCH_ASSOC)) {
        if (!removeLtiDeploymentWithDependencies($row["deployment_id"], $registrationId)) return false;
    }
    return removeLTIRegistration($registrationId);
}

// Delete Deployment and everything that depends on it
function removeLtiDeploymentWithDependencies($deploymentId, $registrationId)
{
    // before deleting deployment, users need to be deleted before deleting deployment
    if (!removeAllLtiUsersForDeploymentWithDependencies($deploymentId, $registrationId)) return false;
    return removeLTIDeployment($deploymentId, $registrationId);
}

function removeAllLtiUsersForDeploymentWithDependencies($deploymentId, $registrationId)
{
    $userUuids = getALlLtiUserUuidsForDeployment($deploymentId, $registrationId);
    if (!$userUuids) return true;
    while ($row = $userUuids->fetch(PDO::FETCH_ASSOC)) {
        if (!removeLtiUserWithDependencies($row["uuid"])) return false;
    }
    return true;
}

function removeLtiUserWithDependencies($uuid)
{
    return removeAllLtiRoles($uuid) && removeAllTags($uuid) && removeLtiUser($uuid);
}

# write new lti user to db
function createNewLtiUser($idTokenPayload)
{
    $uuid = generateUuid();
    $platformId = $idTokenPayload["iss"];
    $clientId = $idTokenPayload["aud"];
    $registrationId = getLTIRegistrationId($platformId, $clientId);
    $sub = $idTokenPayload["sub"] ?? null;
    $email = $uuid . "@" . getenv('DOMAIN');
    $deploymentId = $idTokenPayload[LtiConstants::DEPLOYMENT_ID];
    $name = $idTokenPayload["name"] ?? null;
    $givenName = $idTokenPayload["given_name"] ?? null;
    $familyName = $idTokenPayload["family_name"] ?? null;
    if (!createLtiUser($uuid, $sub, $deploymentId, $registrationId, $name, $givenName, $familyName, $email)) {
        return false;
    };
    $roles = $idTokenPayload[LtiConstants::ROLES] ?? null;
    addRolesToLtiUser($uuid, $roles);
    updateTagsForLtiUser($uuid);
    return $uuid;
}

function addRolesToLtiUser($uuid, $roles)
{
    foreach ($roles as $role) {
        addLtiRole($uuid, $role);
    }
}

function updateTagsForLtiUser($uuid)
{
    removeAllTags($uuid);
    $roles = getLtiRoles($uuid) ?? [];
    foreach ($roles as $role) {
        $tags = getTagsForLtiRole($role) ?? [];
        foreach ($tags as $tag) {
            addTag($uuid, $tag);
        }
    }
}

function getLtiUserUuidIfExistentFromIdToken($idTokenPayload)
{
    $platformId = $idTokenPayload["iss"];
    $clientId = $idTokenPayload["aud"];
    $sub = $idTokenPayload["sub"];
    $registrationId = getLTIRegistrationId($platformId, $clientId);
    return getLtiUserUuidIfExistent($sub, $registrationId);
}
