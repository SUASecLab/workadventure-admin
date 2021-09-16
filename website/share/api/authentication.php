 <?php

    function isAuthorized()
    {
        $headers = getallheaders();
        if ((isset($headers['Authorization'])) && ((htmlspecialchars($headers['Authorization'])) == getenv('ADMIN_API_TOKEN'))) {
            return true;
        }
        return false;
    }

?>