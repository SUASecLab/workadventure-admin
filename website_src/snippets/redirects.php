<?php
session_start();
?>
<!doctype html>
<html lang="en">
    <body>
        <?php
require_once ('../util/database_operations.php');
require_once ('../util/web_login_functions.php');
$DB = getDatabaseHandleOrPrintError();
if (!isLoggedIn()) {
    $DB = NULL;
    die();
}

// add redirection
if ((isset($_POST["action"])) &&
    (htmlspecialchars($_POST["action"]) == "addRedirection") &&
    (isset($_POST["source"])) &&
    (isset($_POST["destination"]))) {
        $sourceRaw = trim(htmlspecialchars($_POST["source"]));
        $destinationRaw = trim(htmlspecialchars($_POST["destination"]));
        $source = "/@/org/".getenv('DOMAIN')."/".$sourceRaw;
        $destination = "https://".getenv('DOMAIN').$destinationRaw;
        if (strlen($sourceRaw) == 0) { ?>
            <aside class="alert alert-danger" role="alert">
                The redirected URL must at least be one character long
            </aside>
            <?php
        } else if (getMapFileUrl($destinationRaw) == NULL) { ?>
            <aside class="alert alert-danger" role="alert">
                The specified destination map does not exist
            </aside>
            <?php
        } else {
            $success = addMapRedirect($source, $destination);
            if ($success) { ?>
                <aside class="alert alert-success" role="alert">
                    Successfully set up redirection from <?php echo $source." to ".$destination; ?>
                </aside>
                <?php
            } else { ?>
                <aside class="alert alert-danger" role="alert">
                    Could not set up redirection from <?php echo $source." to ".$destination; ?>
                </aside>
                <?php
            }
        }
}

// remove redirection
if ((isset($_POST["action"])) &&
    (htmlspecialchars($_POST["action"]) == "removeRedirection") &&
    (isset($_POST["source"]))) {
        $mapToRemove = htmlspecialchars($_POST["source"]);
        $removalSuccess = removeMapRedirect($mapToRemove);
        if ($removalSuccess) { ?>
                <aside class="alert alert-success" role="alert">
                    Successfully removed redirection from <?php echo $mapToRemove; ?>
                </aside>
            <?php
        } else {
?>
                <aside class="alert alert-danger" role="alert">
                    Could not remove <?php echo $mapToRemove; ?>
                </aside>
            <?php
        }
}

// list all redirects

$redirects = getAllMapRedirects();
if ($redirects == NULL) {
?>
<aside class="alert alert-danger" role="alert">
    Could not fetch redirect entries
</aside>
<?php
} else {
    if ($redirects->rowCount() > 0) {
?>
        <p class="fs-4">Enumeration of room redirections</p>
        <table class="table" style="margin-bottom: 1rem;">
        <?php
        $firstIteration = true;
        while ($row = $redirects->fetch(PDO::FETCH_ASSOC)) {
            if ($firstIteration) {
                $firstIteration = false;
?>
                <thead>
                    <tr>
                        <th>Source</th>
                        <th>Destination</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
            <?php
            }
?>
            <tr>
                <td>
                    <p class="fw-normal">
                        <?php echo $row["map_url"]; ?>
                    </p>
                </td>
                <td>
                    <p class="fw-normal">
                        <?php echo $row["redirect_url"]; ?>
                    </p>
                </td>
                <td>
                    <button class="tag btn btn-danger" onclick="removeRedirection(encodeURI('<?php echo $row['map_url']; ?>'));">
                        Remove
                    </button>
                </td>
            </tr>
            <?php
        }
    }
}
?>
            </tbody>
    </table>
<?php
$baseUrl = "https://" . getenv('DOMAIN') . "/@/org/" . getenv('DOMAIN') . "/";
$maps = getAllMaps();
if ($maps == NULL) { ?>
    <aside class="alert alert-danger" role="alert">
         Could not load maps
    </aside>
<?php
    die();
}
?>
        <form action="javascript:void(0);">
            <p class="fs-4">Add room redirection</p>
            <label for="mapRedirectUrl" class="form-label">To be redirected URL</label>
            <div class="input-group mb-3">
                <span class="input-group-text" id="mapRedirectUrlPrefix"><?php echo $baseUrl; ?></span>
                <input type="text" class="form-control" id="mapRedirectUrl" aria-describedby="mapRedirectUrlPrefix">
            </div>
            <label for="dropdownMenu" class="form-label">Select destination map</label>
            <div class="dropdown" id="dropdownMenu">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="sourceMapsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    Select
                </button>
                <ul class="dropdown-menu" aria-labelledby="sourceMapsDropdown">
                    <?php
                    while ($row = $maps->fetch(PDO::FETCH_ASSOC)) {
                        $mapUrl = $row["map_url"];
?>
                        <li>
                            <button class="dropdown-item" onclick="destinationMapSelect('<?php echo $mapUrl; ?>');"><?php echo $mapUrl; ?></button>
                        </li>
                        <?php
                    }
?>
                </ul>
            </div>
            <button class="btn btn-primary" type="submit" style="margin-top: 1rem;" id="addRedirection">Set up redirection</button>
        </form>
    </body>
</html>