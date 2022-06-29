<?php
//session_start();
use Packback\Lti1p3\Interfaces\ICookie;

require_once('../vendor/autoload.php');

class LtiCookie implements ICookie
{
    public function getCookie(string $name): ?string
    {
        return $_COOKIE[$name] ?? null;
    }

    public function setCookie(string $name, string $value, $exp = 3600, $options = []): void
    {
        setcookie($name, $value, time() + $exp);
    }
}
