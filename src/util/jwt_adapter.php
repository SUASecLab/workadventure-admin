<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function encryptJWT($payload, $key) {
    return JWT::encode($payload, $key, 'HS256');
}

function decryptJWT($jwt, $key) {
    return (array) JWT::decode($jwt, new Key($key, 'HS256'));
}

?>