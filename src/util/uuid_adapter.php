<?php
include_once ('../vendor/autoload.php');
use Ramsey\Uuid\Uuid;
function generateUuid() {
    return trim(Uuid::uuid4()->toString());
}
function isValidUuid($uuid) {
    if (is_null($uuid)) {
        return FALSE;
    }
    return Uuid::isValid($uuid);
}
function isValidUuidOrDie($uuid) {
    if (!isValidUuid($uuid)) {
        die();
    }
}
?>