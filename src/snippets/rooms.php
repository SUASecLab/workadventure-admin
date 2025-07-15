<?php
session_start();
?>
<!doctype html>
<html lang="en">
<body>
    <?php
require_once ('../util/database_operations.php');
require_once ('../util/web_login_functions.php');
// Connect to database
$DB = getDatabaseHandleOrPrintError();
if (!isLoggedIn()) {
    $DB = NULL;
    die();
}
// Check CSRF data
isCSRFDataValidOrDie();

// Get action
$action = "";
if (isset($_POST["action"])) {
    $action = $_POST["action"];

    if (gettype($action) !== "string") {
        die("Invalid data provided");
    }
    $action = htmlspecialchars($action);
}
// we use the old naming scheme: mapUrl -> .wam; mapFileUrl: -> .json files
// map should be removed
if ((isset($_POST["action"])) && ($action === "removeMap") && (isset($_POST["mapUrl"])) && (isset($_POST["csrf_token"]))) {
    // Check CSRF token
    if ($_SESSION["csrf_token"] !== $_POST["csrf_token"]) {
      die("Invalid data received");
    }

    $mapToRemove = htmlspecialchars($_POST["mapUrl"]);
    if (removeMap($mapToRemove)) { ?>
            <aside class="alert alert-success" role="alert">
                Sucessfully removed <?php echo $mapToRemove; ?>
            </aside>
        <?php
    } else { ?>
            <aside class="alert alert-danger" role="alert">
                Could not remove <?php echo $mapToRemove; ?>
            </aside>
            <?php
    }
}
// map should be added
if ((isset($_POST["action"])) && ($action === "addMap") && (isset($_POST["mapUrl"])) && (isset($_POST["fileUrl"])) && (isset($_POST["access"])) && (isset($_POST["csrf_token"]))) {
    // Check CSRF token
    if ($_SESSION["csrf_token"] !== $_POST["csrf_token"]) {
      die("Invalid data received");
    }

    $validInput = true;
    $mapUrl = htmlspecialchars($_POST["mapUrl"]);
    $mapFileUrl = htmlspecialchars($_POST["fileUrl"]);
    $accessRestriction = intval(htmlspecialchars($_POST["access"]));
    $tagsArray = array();
    // validate input
    if ((($accessRestriction) < 1) || ($accessRestriction > 3)) {
        $validInput = false;
    }
    if (strlen(trim($mapUrl)) === 0) {
        $validInput = false;
    }
    if (strlen(trim($mapFileUrl)) === 0) {
        $validInput = false;
    }
    if (($validInput) && ($accessRestriction === 3)) {
        if (isset($_POST["tags"])) {
            $tags = json_decode($_POST["tags"]);
            if (gettype($tags) !== "array") {
                $validInput = false;
            } else {
                if (sizeof($tags) === 0) {
                    $validInput = false;
                } else {
                    // add tags
                    foreach ($tags as $tag) {
                        if (strlen(trim($tag)) === 0) {
                            $validInput = false;
                        } else {
                            array_push($tagsArray, trim($tag));
                        }
                    }
                }
            }
        } else {
            $validInput = false;
        }
    }
    // add map
    if ($validInput) {
        $success = storeMap("/".$mapUrl, $mapFileUrl, $accessRestriction, $tagsArray);
        // check whether adding the map worked
        if ($success) { ?>
                <aside class="alert alert-success" role="alert">
                    Sucessfully stored <?php echo $mapUrl; ?>
                </aside>
            <?php
        } else { ?>
                <aside class="alert alert-danger" role="alert">
                    Could not store <?php echo $mapUrl; ?>
                </aside>
            <?php
        }
    } else { ?>
            <aside class="alert alert-danger" role="alert">
                Could not store <?php echo trim($mapUrl); ?>: invalid input
            </aside>
        <?php
    }
}
// show alert if start room map has not been set so far
if (getMapFileUrl(getenv('START_ROOM_URL')) === NULL) { ?>
        <aside class="alert alert-danger" role="alert">
            The map file for the start room has not been set so far!
        </aside>
    <?php
} else { ?>
        <p class="fs-4">Enumeration of existing rooms</p>
    <?php
}
// list maps and show input dialog

?>
    <table class="table">
        <?php
$firstIteration = true;
$maps = getAllMaps();
if ($maps === NULL) { ?>
    <aside class="alert alert-danger" role="alert">
        Could not load maps
    </aside>
<?php
    die();
}
foreach ($maps as $map) {
    $myMap = (array) $map;
    if ($firstIteration) {
        $firstIteration = false;
?>
                <thead>
                    <tr>
                        <th>WAM link URL</th>
                        <th>JSON file URL</th>
                        <th>Access Restriction</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php
    }
?>
                <tr>
                    <td>
                        <p class="fw-normal">
                            <?php echo $myMap["wamUrl"]; ?>
                        </p>
                    </td>
                    <td>
                        <p class="fw-normal">
                            <?php echo $myMap["mapUrl"]; ?>
                        </p>
                    </td>
                    <td>
                        <?php
    $policy = $myMap["policyNumber"];
    if ($policy === 1) { ?>
                            <p class="fw-normal">
                                Public
                            </p>
                        <?php
    } else if ($policy === 2) { ?>
                            <p class="fw-normal">
                                Members
                            </p>
                        <?php
    } else { ?>
                            <p class="fw-normal">
                                Members with tags
                                <?php
        if (array_key_exists("tags", $myMap)) {
            $mapTags = $myMap["tags"];
            foreach ($mapTags as $tag) { ?>
                                <div class="badge rounded-pill bg-primary tag">
                                    <?php echo $tag; ?>
                                </div>
                            <?php
            }
        } else {
            echo "No tags stored";
        }
?>
                        </p>
                    <?php
    }
?>
                    </td>
                    <td>
                        <button class="tag btn btn-danger" onclick="removeMap('<?php echo $myMap['wamUrl']; ?>', '<?php echo $_SESSION['csrf_token']; /* @phpstan-ignore echo.nonString */ ?>');">
                            Remove
                        </button>
                    </td>
                <?php
}
?>
                </tbody>
    </table>
    <article>
        <?php
            $baseUrl = "https://" . getenv('DOMAIN') ."/"; // . "/@/org/" . getenv('DOMAIN') . "/";
            $startRoom = getenv('START_ROOM_URL');
            if ($startRoom === false) { ?>
                <aside class="alert alert-danger" role="alert">
                    The environment variable "START_ROOM_URL" has not been set so far! Can not create maps!
                </aside>
            <?php } else {
?>
        <form action="javascript:void(0)">
            <p class="fs-4" id="add-map-paragraph">Add a new room</p>
            <label for="mapURL" class="form-label">WAM link URL</label>
            <div class="input-group mb-3">
                <span class="input-group-text" id="map_url_prefix"><?php echo $baseUrl; ?></span>
                <?php
$startRoom = explode("/", $startRoom) [4];
if (getMapFileUrl(getenv('START_ROOM_URL')) !== NULL) { ?>
                    <input type="text" class="form-control" id="mapURL" aria-describedby="map_url_prefix" name="map_url">
                <?php
} else { ?>
                    <input type="text" class="form-control" id="mapURL" aria-describedby="map_url_prefix" name="map_url" value="<?php echo $startRoom; ?>" readonly>
                <?php
} ?>
            </div>
            <label for="mapURLFile" class="form-label">JSON file URL</label>
            <div class="input-group mb-3">
                <span class="input-group-text" id="map_url_file_prefix">https://</span>
                <input type="text" class="form-control" id="mapURLFile" aria-describedby="map_url_file_prefix" name="map_file_url">
            </div>
            <p class="fs-6">Access restriction</p>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="radio" id="radioPublic" value="public" />
                <label class="form-check-label" for="radioPublic">Public</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="radio" id="radioMembers" value="members" checked />
                <label class="form-check-label" for="radioMembers">Members</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="radio" id="radioMembersTags" value="tags" />
                <label class="form-check-label" for="radioMembersTags">Members with tags</label>
            </div>
            <div id="tagsArea" class="input-group mb-3">
            </div>
            <input type="hidden" id="csrf_token" value="<?php echo $_SESSION["csrf_token"]; /* @phpstan-ignore echo.nonString */?>">
            <button class="btn btn-primary" id="addMapButton" style="margin-top: 1rem;">Add map</button>
        </form>
    </article>
</body>

</html>
<?php
}
$DB = NULL;
?>