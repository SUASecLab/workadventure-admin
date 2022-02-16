<?php
session_start();
?>
<!doctype html>
<html lang="en">
    <body>
        <?php
require_once ('../util/database_operations.php');
require_once ('../util/web_login_functions.php');

// Connect to DB
$DB = getDatabaseHandleOrPrintError();
if(!isLoggedIn()) {
    $DB = NULL;
    die();
} ?>
        <p class="fs-3">Manage rooms</p>
        <ul class="nav nav-tabs" id="navigationTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="maps-tab" data-bs-toggle="tab"
                    data-bs-target="#maps" type="button" role="tab" aria-controls="maps"
                    aria-selected="true">Rooms</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="redirects-tab" data-bs-toggle="tab"
                    data-bs-target="#redirects" type="button" role="tab" aria-controls="redirects"
                    aria-selected="false">Room redirections</button>
            </li>
        </ul>
        <div class="tab-content" id="navigationTabsContent">
            <div class="tab-pane fade show active" id="maps" role="tabpanel" aria-labelledby="maps-tab">
                <div id="mapContainer" style="padding-top: 1rem;">
                </div>
            </div>
            <div class="tab-pane fade" style="padding-top: 1rem;" id="redirects" role="tabpanel" aria-labelledby="redirects-tab">
                <div id="redirectsContainer">
                </div>
            </div>
        </div>
    </body>
</html>
<?php $DB = NULL; ?>