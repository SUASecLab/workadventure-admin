<?php
include_once ('../vendor/autoload.php');
use Ramsey\Uuid\Uuid;
function generateUuid() {
    return trim(Uuid::uuid4()->toString());
}
?>