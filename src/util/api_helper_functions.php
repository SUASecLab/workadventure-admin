<?php
require_once ('database_operations.php');
function getUuid($userIdentifier) {
    if (str_contains($userIdentifier, "@")) {
        // email as unique identifier -> get uuid
        return getUuidFromEmail($userIdentifier);
    } else {
        // uuid is already our identifier
        return $userIdentifier;
    }
}

// checks if user is not allowed to access map
function userCanAccessMap($userUuid, $shortMapUri) {
    $map = iterator_to_array(getMap($shortMapUri));
    $policy = $map["policyNumber"];
    if ($policy !== 1) {
        // null is acceptable here, because this is not meant for security, but for UX
        // WA uses call to /map Endpoint also without userId -> need to support that
        if ($userUuid === null) {
            return true;
        }
        if (!userExists($userUuid)) {
            return false;
        }
        if ($policy === 3) {
            $user = iterator_to_array(getUserData($userUuid));
            $sharedTagsCount = count(array_intersect(iterator_to_array($map["tags"]),
                                                    iterator_to_array($user["tags"])));
            if ($sharedTagsCount < 1) {
                return false;
            }
        }
    }
    return true;
}
