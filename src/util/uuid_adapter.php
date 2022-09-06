<?php
include_once ('../../vendor/autoload.php');
use Ramsey\Uuid\Uuid;
function generateUuid(): string {
    return trim(Uuid::uuid4()->toString());
}
function isValidUuid(string|null $uuid): bool {
    if (is_null($uuid)) {
        return FALSE;
    }
    return Uuid::isValid($uuid);
}
function isValidUuidOrDie(string $uuid): void {
    if (!isValidUuid($uuid)) {
        die();
    }
}
?>