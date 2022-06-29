<?php
//session_start();
use Packback\Lti1p3\Interfaces\ICache;

require_once ('../vendor/autoload.php');

class LtiCache implements ICache{

    public function getLaunchData(string $key): ?array
    {
        if(isset($_SESSION["lti_launch_data:" . $key])) {
            return unserialize($$_SESSION["lti_launch_data"]);
        } else {
            return null;
        }
    }

    public function cacheLaunchData(string $key, array $jwtBody): void
    {
        $_SESSION["lti_launch_data:" . $key] = serialize($jwtBody);
    }

    public function cacheNonce(string $nonce, string $state): void
    {
        $_SESSION["lti_nonce:" . $nonce] = $state;
    }

    public function checkNonceIsValid(string $nonce, string $state): bool
    {
        return (isset($_SESSION["lti_nonce:" . $nonce]) && ($_SESSION["lti_nonce:" . $nonce] == $state));
    }

    public function cacheAccessToken(string $key, string $accessToken): void
    {
        $_SESSION["lti_access_token:" . $key] = $accessToken;
    }

    public function getAccessToken(string $key): ?string
    {
        return $_SESSION["lti_access_token:" . $key] ?? null;
    }

    public function clearAccessToken(string $key): void
    {
        unset($_SESSION["lti_access_token:" . $key]);
    }
}