<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function encodeJWT($payload, $key) {
    return JWT::encode($payload, $key, 'HS256');
}
?>