<?php
// Get environment information
function get_domain() {
    return getenv("DOMAIN");
}

function get_oidc_domain() {
    return getenv("OPENID_DOMAIN");
}

function get_oidc_realm() {
    return getenv("OPENID_REALM");
}

function get_admin_cli_secret() {
    return getenv("OPENID_CLI_SECRET");
}

function get_realm_username() {
    return getenv("OPENID_REALM_ADMIN_USER");
}

function get_realm_password() {
    return getenv("OPENID_REALM_ADMIN_PASSWORD");
}

// Generate a new access token
function get_access_token() {
    $oidc_domain = get_oidc_domain();
    $realm = get_oidc_realm();
    $admin_cli_secret = get_admin_cli_secret();
    $post_data = array(
        "username" => get_realm_username(),
        "password" => get_realm_password(),
        "grant_type" => "password"
    );

    $url = "https://{$oidc_domain}/realms/{$realm}/protocol/openid-connect/token";
    $curl_client = curl_init($url);
    curl_setopt_array($curl_client, array(
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($post_data),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERPWD => "admin-cli:".$admin_cli_secret,
        CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
        CURLOPT_HTTPHEADER => array(
            "Content-Type: application/x-www-form-urlencoded"
        ),
        // testing only!
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0,
    ));

    $response = curl_exec($curl_client);
    if ($response == false) {
         die("curl did not succeed: ". curl_error($curl_client));
    }

    $status_code = curl_getinfo($curl_client, CURLINFO_HTTP_CODE);
    curl_close($curl_client);

    $result = json_decode($response, true);
    if ($status_code !== 200 || empty($result["access_token"])) {
        die("Token request failed (HTTP $status_code)".$response);
    }
    return $result["access_token"];
}

// Test if a user with that uuid already exists
function test_if_user_exists($uuid) {
    $access_token = get_access_token();
    $domain = get_domain();
    $oidc_domain = get_oidc_domain();
    $realm = get_oidc_realm();

    $url = "https://{$oidc_domain}/admin/realms/{$realm}/users?".http_build_query(array(
        "email" => "{$uuid}@{$domain}"
    ));

    $curl_client = curl_init($url);
    curl_setopt_array($curl_client, array(
        CURLOPT_HTTPGET => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array(
            "Accept: application/json",
            "Authorization: Bearer ".$access_token
        ),
        // testing only!
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0,
    ));

    $response = curl_exec($curl_client);
    if ($response == false) {
         die("curl did not succeed: ". curl_error($curl_client));
    }
    $status_code = curl_getinfo($curl_client, CURLINFO_HTTP_CODE);
    curl_close($curl_client);

    $users = json_decode($response, true);
    return is_array($users) && count($users) > 0;
}

// Register a new user
function register_new_user($uuid) {
    if (!test_if_user_exists($uuid)) {
        $access_token = get_access_token();
        $oidc_domain = get_oidc_domain();
        $realm = get_oidc_realm();
        $domain = get_domain();
        $password = bin2hex(random_bytes(16));

        $post_data = array(
            "username" => $uuid,
            "firstName" => $uuid,
            "lastName" => $uuid,
            "enabled" => true,
            "email" => "{$uuid}@{$domain}",
            "emailVerified" => true,
            "credentials" => array(array(
                "type" => "password",
                "value" => $password,
                "temporary" => false
            ))
        );

        $url = "https://{$oidc_domain}/admin/realms/{$realm}/users";
        $curl_client = curl_init($url);
        curl_setopt_array($curl_client, array(
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($post_data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: Bearer ".$access_token
            ),
            // testing only!
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
        ));

        curl_exec($curl_client);
        $status_code = curl_getinfo($curl_client, CURLINFO_HTTP_CODE);
        curl_close($curl_client);

        if ($status_code === 201) {
            return "Sent user data to OpenID Connect. Password: ".$password;
        } else {
            return "Could not send user information to OpenID Connect: ".$status_code;
        }
    } else {
        return "User already stored in OpenID Connect.";
    }
}
?>