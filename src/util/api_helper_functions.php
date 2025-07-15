<?php
require_once ('database_operations.php');
function getUuid(string $userIdentifier): string {
    if (str_contains($userIdentifier, "@")) {
        // email as unique identifier -> get uuid
        return getUuidFromEmail($userIdentifier);
    } else {
        // uuid is already our identifier
        return $userIdentifier;
    }
}

// checks if user is allowed to access map
function userCanAccessMap(string|null $userUuid, string|bool $shortMapUri): bool {
    if (gettype($shortMapUri) === "boolean") {
        return false;
    }

    $map = getMap($shortMapUri);

    if ($map === null) {
        error_log("Could not fetch map information");
        return false;
    }

    $policy = $map["policyNumber"];
    if ($policy !== 1) {
        // null is acceptable here, because this is not meant for security, but for UX
        // WA uses call to /map Endpoint also without userId -> need to support that
        if ($userUuid === null) {
            return true;
        }
        if ($policy === 2) {
            if (!userExists($userUuid)) {
                return false;
            } else {
                return true;
            }
        }
        if (($policy === 3) && (array_key_exists("tags", $map))) {
            $user = getUserData($userUuid);

            if ($user === null) {
                error_log("Could not fetch user data.");
                return false;
            }

            if (!array_key_exists("tags", $user)) {
                return false;
            }

            $sharedTagsCount = count(array_intersect($map["tags"], $user["tags"]));
            if ($sharedTagsCount < 1) {
                return false;
            }
        }
    }
    return true;
}

/**
 * Generate an API error message
 * @return array{"code": string, "title": string, "subtitle": string, "details": string, "image": string}
 */
function apiErrorMessage(string $code, string $title,
    string $subtitle, string $details = "", string $image = ""): array {
    return array(
        "code" => $code,
        "title" => $title,
        "subtitle" => $subtitle,
        "details" => $details,
        "image" => $image
    );
}