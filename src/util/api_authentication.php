<?php
function authorizeOrDie() {
    $headers = getallheaders();
    if (!((isset($headers['Authorization'])) && ((htmlspecialchars($headers['Authorization'])) === getenv('ADMIN_API_TOKEN')))) {
        die();
    }
}
?>