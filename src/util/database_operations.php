<?php 
require_once __DIR__ . '/../../vendor/autoload.php';
use MongoDB\Client;
use MongoDB\Database;
use MongoDB\BSON\ObjectId;
use MongoDB\Driver\Cursor;
use MongoDB\Model\BSONDocument;

function getDatabaseHandle():MongoDB\Database|null {
    $user = getenv("DB_USER");
    $pwd = getenv("DB_PASSWORD");
    $dbName = getenv("DB_NAME");
    
    try{
        $mongo = new Client("mongodb://${user}:${pwd}@admin-db:27017/".$dbName,
            [],
            [
                'typeMap' => [
                    'root' => 'array', 
                    'document' => 'array', 
                    'array' => 'array'
                ]
            ]);
        return $mongo->$dbName;
    } catch (Exception $e) {
        error_log("Could not set up database connection: " . $e->getMessage());
        return null;
    }
}

function getDatabaseHandleOrDie():MongoDB\Database {
    $handle = getDatabaseHandle();
    if ($handle === NULL) {
        die();
    }
    return $handle;
}
function getDatabaseHandleOrPrintError():MongoDB\Database {
    $handle = getDatabaseHandle();
    if ($handle === NULL) {
        echo "<aside class=\"alert alert-danger\" role=\"alert\">";
        echo "Could not connect to database";
        echo "</aside>";
        die();
    }
    return $handle;
}
function userExists(string $uuid): bool {
    GLOBAL $DB;
    $result = $DB->users->findOne([
        "uuid" => $uuid
    ]);
    return $result !== NULL;
}
function writeUuidToDatabase(string $uuid): bool {
    GLOBAL $DB;
    $result = $DB->users->insertOne([
        "uuid" => $uuid,
        "name" => "anonymous",
        "email" => $uuid . "@" . getenv('DOMAIN')
    ]);
    return $result->getInsertedCount() === 1;
}
function updateUserData(string $uuid, string $name, string $email, string $visitCardURL): bool {
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
function getUuidFromEmail(string $email): string {
    GLOBAL $DB;

    $result = $DB->users->findOne(["email" => $email]);
    return $result["uuid"];
}
function createAccountIfNotExistent(string $uuid): bool {
    if (!userExists($uuid)) {
        return writeUuidToDatabase($uuid);
    }
    return false;
}
function banUser(string $uuid, string $banReason): bool {
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
function liftBan(string $uuid): bool {
    GLOBAL $DB;
    $result = $DB->users->updateOne(
        ['uuid' => $uuid],
        ['$set' => ['banned' => false]]
    );
    
    return $result->getModifiedCount() === 1;
}
function getNumberOfUsers(): int|null {
    GLOBAL $DB;
    try {
        $users = $DB->users->find();
        $numberOfUsers = 0;
        foreach($users as $user) {
            $numberOfUsers++;
        }
        return $numberOfUsers;
    } catch (Exception $e) {
        error_log("Could not fetch user count: " . $e->getMessage());
        return null;
    }
}
/**
 * Get all users from the database
 * @return array{array{"_id": mixed, "uuid": string, "name": string,
 *  "email": string, "visitCardURL"?: string, "tags"?: string[],
 *  "messages"?: string[], "banned"?: bool, "banReason"?: string,
 *  "startMap"?: string}}
 */
function getAllUsers(): array|null {
    GLOBAL $DB;
    try {
        return $DB->users->find()->toArray();
    } catch (Exception $e) {
        error_log("Could not fetch users from database: " . $e->getMessage());
        return null;
    }
}
/**
 * Get a specific user from the database
 * @return array{"_id": mixed, "uuid": string, "name": string,
 *  "email": string, "visitCardURL"?: string, "tags"?: string[],
 *  "messages"?: string[], "banned"?: bool, "banReason"?: string,
 *  "startMap"?: string}
 */
function getUserData(string $uuid): array|null {
    GLOBAL $DB;
    try {
        $user = $DB->users->findOne([
            "uuid" => $uuid
        ]);
        return $user;
    } catch (Exception $e) {
        error_log("Could not fetch user data: " . $e->getMessage());
        return null;
    }
}
function addTag(string $uuid, string $newTag): bool {
    GLOBAL $DB;
    $user = getUserData($uuid);

    if ($user === NULL) {
        return false;
    }

    $tags = array();

    if (array_key_exists("tags", $user)) {
        $tags = $user["tags"];
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
function removeTag(string $uuid, string $remTag): bool {
    GLOBAL $DB;
    $user = getUserData($uuid);

    if ($user === NULL) {
        return false;
    }

    if (!array_key_exists("tags", $user)) {
        return true;
    }
    
    $tags = $user["tags"];
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
function removeMessages(string $uuid): bool {
    GLOBAL $DB;

    $result = $DB->users->updateOne(
        [ 'uuid' => $uuid ],
        [ '$set' => [ 'messages'  => array() ]]
    );

    return $result->getModifiedCount() === 1;
}
function websiteUserExists(string $user, string $password): bool {
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

function getNumberOfWebsiteUsers(): int {
    GLOBAL $DB;
    $users = $DB->websiteUsers->find();
    $numberOfUsers = 0;
    foreach($users as $user) {
        $numberOfUsers++;
    }
    return $numberOfUsers;
}

function createWebsiteUser(string $username, string $hashedPassword): bool {
    GLOBAL $DB;

    $result = $DB->websiteUsers->insertOne([
        "username" => $username,
        "password" => $hashedPassword
    ]);

    // The insertion was successful if one document was inserted.
    // Return true then.

    return $result->getInsertedCount() === 1;
}

function getMapFileUrl(string|bool $mapUrl): string|null {
    GLOBAL $DB;
    // START_ROOM_URL not set
    if (gettype($mapUrl) === "boolean") {
        error_log("START_ROOM_URL env var is not set");
        return null;
    }
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
/**
 * Get data for a specific map
 * @param string $mapUrl URL of the Map (for WA)
 * @return array{"_id": mixed, "mapUrl": string,
 *  "mapFileUrl": string, "policyNumber": int, "tags"?: string[]}
 */
function getMap(string $mapUrl): array|null {
    GLOBAL $DB;
    try {
        return $DB->maps->findOne(
            ["mapUrl" => $mapUrl]
        );
    } catch (Exception $e) {
        error_log("Could not get map URL: " . $e->getMessage());
        return null;
    }
}
/**
 * @param string $mapUrl URL of the map (for WA)
 * @param string $mapFileUrl URL where the file of the map is stored
 * @param int $policyNumber Policy number for access restrictions
 * @param string[] $tags Tags array for access restrictions
 */
function storeMap(string $mapUrl, string $mapFileUrl, int $policyNumber, array $tags): bool {
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
/**
 * Get array of all maps
 * @return array{array{"_id": mixed, "mapUrl": string,
 *  "mapFileUrl": string, "policyNumber": int, "tags"?: string[]}}
 */
function getAllMaps(): array|null {
    GLOBAL $DB;
    try {
        return $DB->maps->find()->toArray();
    } catch (Exception $e) {
        error_log("Could not fetch maps from database: " . $e->getMessage());
        return null;
    }
}
function removeMap(string $mapUrl): bool {
    GLOBAL $DB;
    $result = $DB->maps->deleteOne(["mapUrl" => $mapUrl]);
    return $result->getDeletedCount() === 1;
}
function getMapRedirect(string $mapUrl): string|null {
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
function addMapRedirect(string $mapUrl, string $redirectUrl): bool {
    GLOBAL $DB;
    $result = $DB->mapRedirects->insertOne([
        "mapUrl" => $mapUrl,
        "redirectUrl" => $redirectUrl
    ]);
    return $result->getInsertedCount() === 1;
}
/**
 * Get all map redirects
 * @return array{0?:array{"_id": mixed, "mapUrl": string, "redirectUrl": string}}
 */
function getAllMapRedirects(): array| null {
    GLOBAL $DB;
    try {
        return $DB->mapRedirects->find()->toArray();
    } catch (Exception $e) {
        error_log("Could not fetch map redirects: " . $e->getMessage());
        return null;
    }
}
function removeMapRedirect(string $redirect): bool {
    GLOBAL $DB;
    $result = $DB->mapRedirects->deleteOne([
        "mapUrl" => $redirect
    ]);
    return $result->getDeletedCount() === 1;
}
function removeUserMessage(string $uuid, string $message): bool {
    GLOBAL $DB;
    $user = getUserData($uuid);

    if ($user === NULL) {
        return false;
    }

    if (!array_key_exists("messages", $user)) {
        return true;
    }

    $messages = $user["messages"];
    $messageToRemove = array($message);
    $messages = array_diff($messages, $messageToRemove);

    $result = $DB->users->updateOne(
        ['uuid' => $uuid],
        ['$set' => ['messages' => $messages]]
    );

    return $result->getModifiedCount() === 1;
}
function storeUserMessage(string $uuid, string $message): bool {
    GLOBAL $DB;
    $user = getUserData($uuid);

    if ($user === NULL) {
        return false;
    }
    $messages = array();

    if (array_key_exists("messages", $user)) {
        $messages = $user["messages"];
    }

    array_push($messages, $message);

    $result = $DB->users->updateOne(
        ['uuid' => $uuid],
        ['$set' => ['messages' => $messages]]
    );

    return $result->getModifiedCount() === 1;
}
function texturesStored(): bool {
    GLOBAL $DB;
    $textures = getTextures();

    if ($textures === null) {
        error_log("Could not fetch textures from database");
        return false;
    }

    $numberOfTextures = 0;
    foreach($textures as $texture) {
        $numberOfTextures++;
    }
    return $numberOfTextures > 0;
}
/**
 * Get all textures
 * _id is the object id, can be converted to string
 * @return array{0?:array{"_id": string, "waId": string, "url": string, "layer": string, "tags"?: string[]}}
 */
function getTextures(): array|null {
    GLOBAL $DB;
    try {
        return $DB->textures->find()->toArray();
    } catch (Exception $e) {
        error_log("Could not fetch textures: " . $e->getMessage());
        return null;
    }
}
function removeTexture(string $id): bool {
    GLOBAL $DB;
    $result = $DB->textures->deleteOne(['_id' => new ObjectId($id)]);
    return $result->getDeletedCount() === 1;
}
/**
 * @param string $id id of the texture
 * @param string $layer layer of the texture
 * @param string $url URL where the layer is available
 * @param array{} $tags Tags for access restriction of the texture
 * @return bool indicating whether the texture could be stored (true = success, false = failure)
 */
function storeTexture(string $id, string $layer, string $url, array $tags): bool {
    GLOBAL $DB;
    $result = $DB->textures->insertOne([
        "waId" => $id,
        "layer" => $layer,
        "url" => $url,
        "tags" => $tags
    ]);
    return $result->getInsertedCount() === 1;
}
/**
 * Get all textures by layer
 * @return array{array{"_id": mixed, "waId": string, "url": string, "layer": string, "tags"?: string[]}}
 */
function getTexturesByLayer(string $layer): array|null {
    GLOBAL $DB;
    try {
        return $DB->textures->find(["layer" => $layer])->toArray();
    } catch (Exception $e) {
        error_log("Could not get textures by layer: " . $e->getMessage());
        return null;
    }
}
