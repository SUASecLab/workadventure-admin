 <?php

function isAuthorized() {
    $headers = getallheaders();
    if((isset($headers['Authorization'])) && (($headers['Authorization']) == getenv('ADMIN_API_TOKEN'))) {
        return true;
    }
    return false;
}

?>

