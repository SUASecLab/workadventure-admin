<?php 
require_once __DIR__ . '/../../vendor/autoload.php';
use MongoDB\Client;
use MongoDB\BSON\ObjectId;

function getDatabaseHandle() {
    $user = getenv("DB_USER");
    $pwd = getenv("DB_PASSWORD");
    $dbName = getenv("DB_NAME");
    
    $mongo = new Client("mongodb://${user}:${pwd}@admin-db:27017/".$dbName);
    return $mongo->$dbName;
}

function getDatabaseHandleOrDie() {
    $handle = getDatabaseHandle();
    if ($handle === NULL) {
        die();
    }
    return $handle;
}
function getDatabaseHandleOrPrintError() {
    $handle = getDatabaseHandle();
    if ($handle === NULL) {
        echo "<aside class=\"alert alert-danger\" role=\"alert\">";
        echo "Could not connect to database";
        echo "</aside>";
        die();
    }
    return $handle;
}
function userExists($uuid) {
    GLOBAL $DB;
    $result = $DB->users->findOne([
        "uuid" => $uuid
    ]);
    return $result !== NULL;
}
function writeUuidToDatabase($uuid) {
    GLOBAL $DB;
    $result = $DB->users->insertOne([
        "uuid" => $uuid,
        "name" => "anonymous",
        "email" => $uuid . "@" . getenv('DOMAIN')
    ]);
    return $result->getInsertedCount() === 1;
}
function updateUserData($uuid, $name, $email, $visitCardURL) {
    GLOBAL $DB;
    $result = $DB->users->updateOne(
        [ 'uuid' => $uuid ],
        [ '$set' => [
            'name' => $name,
            'email' => $email,
            'visitCardURL' => $visitCardURL
        ]]
    );
    
    return $result->getModifiedCount() === 1;
}
function getUuidFromEmail($email) {
    GLOBAL $DB;

    $result = $DB->users->findOne(["email" => $email]);
    return $result["uuid"];
}
function createAccountIfNotExistent($uuid) {
    if (!userExists($uuid)) {
        return writeUuidToDatabase($uuid);
    }
}
function banUser($uuid, $banReason) {
    GLOBAL $DB;
    $result = $DB->users->updateOne(
        [ 'uuid' => $uuid ],
        [ '$set' => [
            'banned' => true,
            'banReason' => $banReason
        ]]
    );
    
    return $result->getModifiedCount() === 1;
}
function liftBan($uuid) {
    GLOBAL $DB;
    $result = $DB->users->updateOne(
        ['uuid' => $uuid],
        ['$set' => ['banned' => false]]
    );
    
    return $result->getModifiedCount() === 1;
}
function getNumberOfUsers() {
    GLOBAL $DB;
    $users = $DB->users->find();
    $numberOfUsers = 0;
    foreach($users as $user) {
        $numberOfUsers++;
    }
    return $numberOfUsers;
}
function getAllUsers() {
    GLOBAL $DB;
    return $DB->users->find();
}
function getUserData($uuid) {
    GLOBAL $DB;
    $user = $DB->users->findOne([
        "uuid" => $uuid
    ]);
    return $user;
}
function addTag($uuid, $newTag) {
    GLOBAL $DB;
    $user = getUserData($uuid);

    if ($user === NULL) {
        return false;
    }

    $user = iterator_to_array($user); 
    $tags = array();

    if (array_key_exists("tags", $user)) {
        $tags = iterator_to_array($user["tags"]);
    }

    array_push($tags, $newTag);

    $result = $DB->users->updateOne(
        [ 'uuid' => $uuid ],
        [  '$set' => [
            'tags' => $tags ]
        ]
    );

    return $result->getModifiedCount() === 1;
}
function removeTag($uuid, $remTag) {
    GLOBAL $DB;
    $user = getUserData($uuid);

    if ($user === NULL) {
        return false;
    }

    $user = iterator_to_array($user); 
    
    $tags = iterator_to_array($user["tags"]);
    $removeTag = array($remTag);
    $tags = array_diff($tags, $removeTag);

    $result = $DB->users->updateOne(
        [ 'uuid' => $uuid ],
        [ '$set' => [
            'tags' => $tags ]
        ]
    );

    return $result->getModifiedCount() === 1;
}
function removeMessages($uuid) {
    GLOBAL $DB;

    $DB->users->updateOne(
        [ 'uuid' => $uuid ],
        [ '$set' => [ 'messages'  => array() ]]
    );
}
function websiteUserExists($user, $password) {
    GLOBAL $DB;
    $document = $DB->websiteUsers->findOne(
        [
            "username" => $user,
            "password" => $password
        ]
    );

    if ($document === NULL) {
        return false;
    }
    return true;
}

function getNumberOfWebsiteUsers() {
    GLOBAL $DB;
    $users = $DB->websiteUsers->find();
    $numberOfUsers = 0;
    foreach($users as $user) {
        $numberOfUsers++;
    }
    return $numberOfUsers;
}

function createWebsiteUser($username, $hashedPassword) {
    GLOBAL $DB;

    $result = $DB->websiteUsers->insertOne([
        "username" => $username,
        "password" => $hashedPassword
    ]);

    // The insertion was successful if one document was inserted.
    // Return true then.

    return $result->getInsertedCount() === 1;
}

function getMapFileUrl($mapUrl) {
    GLOBAL $DB;
    $document = $DB->maps->findOne(
        [
            "mapUrl" => $mapUrl
        ]
    );

    if ($document === NULL) {
        return NULL;
    }
    return "https://".$document["mapFileUrl"];
}

function getMap($mapUrl) {
    GLOBAL $DB;
    return $DB->maps->findOne(
        ["mapUrl" => $mapUrl]
    );
}

function storeMap($mapUrl, $mapFileUrl, $policyNumber, $tags) {
    GLOBAL $DB;

    $result = $DB->maps->insertOne([
        "mapUrl" => $mapUrl,
        "mapFileUrl" => $mapFileUrl,
        "policyNumber" => $policyNumber,
        "tags" => $tags
    ]);

    // The insertion was successful if one document was inserted.
    // Return true then.

    return $result->getInsertedCount() === 1;
}
function getAllMaps() {
    GLOBAL $DB;
    return $DB->maps->find();
}
function removeMap($mapUrl) {
    GLOBAL $DB;
    $result = $DB->maps->deleteOne(["mapUrl" => $mapUrl]);
    return $result->getDeletedCount() === 1;
}
function getMapRedirect($mapUrl) {
    GLOBAL $DB;
    $result = $DB->mapRedirects->findOne([
        "mapUrl" => $mapUrl
    ]);
    if ($result !== NULL) {
        return $result["redirectUrl"];
    } else {
        return NULL;
    }
}
function addMapRedirect($mapUrl, $redirectUrl) {
    GLOBAL $DB;
    $result = $DB->mapRedirects->insertOne([
        "mapUrl" => $mapUrl,
        "redirectUrl" => $redirectUrl
    ]);
    return $result->getInsertedCount() === 1;
}
function getAllMapRedirects() {
    GLOBAL $DB;
    return $DB->mapRedirects->find();
}
function removeMapRedirect($redirect) {
    GLOBAL $DB;
    $result = $DB->mapRedirects->deleteOne([
        "mapUrl" => $redirect
    ]);
    return $result->getDeletedCount() === 1;
}
function removeUserMessage($uuid, $message) {
    GLOBAL $DB;
    $user = getUserData($uuid);

    if ($user === NULL) {
        return false;
    }

    $user = iterator_to_array($user);

    $messages = iterator_to_array($user["messages"]);
    $messageToRemove = array($message);
    $messages = array_diff($messages, $messageToRemove);

    $result = $DB->users->updateOne(
        ['uuid' => $uuid],
        ['$set' => ['messages' => $messages]]
    );

    return $result->getModifiedCount() === 1;
}
function storeUserMessage($uuid, $message) {
    GLOBAL $DB;
    $user = getUserData($uuid);

    if ($user === NULL) {
        return false;
    }

    $user = iterator_to_array($user);
    $messages = array();

    if (array_key_exists("messages", $user)) {
        $messages = iterator_to_array($user["messages"]);
    }

    array_push($messages, $message);

    $result = $DB->users->updateOne(
        ['uuid' => $uuid],
        ['$set' => ['messages' => $messages]]
    );

    return $result->getModifiedCount() === 1;
}
function texturesStored() {
    GLOBAL $DB;
    $textures = getTextures();
    $numberOfTextures = 0;
    foreach($textures as $texture) {
        $numberOfTextures++;
    }
    return $numberOfTextures > 0;
}
function getTextures() {
    GLOBAL $DB;
    return $DB->textures->find();
}
function removeTexture($id) {
    GLOBAL $DB;
    $result = $DB->textures->deleteOne(['_id' => new ObjectId($id)]);
    return $result->getDeletedCount() === 1;
}
function storeTexture($id, $layer, $url, $tags) {
    GLOBAL $DB;
    $result = $DB->textures->insertOne([
        "waId" => $id,
        "layer" => $layer,
        "url" => $url,
        "tags" => $tags
    ]);
    return $result->getInsertedCount() === 1;
}
function getTexturesByLayer($layer) {
    GLOBAL $DB;
    return $DB->textures->find(
        ["layer" => $layer]
    );
}
