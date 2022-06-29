<?php
session_start();
?>
<!doctype html>
<html lang="en">

<body>
    <?php
    require_once('../util/database_operations.php');
    require_once('../util/web_login_functions.php');
    require_once('../util/lti_helper_functions.php');

    // Connect to database
    $DB = getDatabaseHandleOrPrintError();
    if (!isLoggedIn()) {
        $DB = NULL;
        die();
    }

    // Get Registration-ID
    if (!isset($_POST["registration_id"]) || !$_POST["registration_id"]) {
    ?>
        <aside class="alert alert-danger" role="alert">
            <p>Registration-ID not specified</p>
        </aside>
    <?php
        die();
    }
    $registrationId = $_POST["registration_id"];

    // Check Registration-ID
    if (!registrationExists($registrationId)) {
    ?>
        <aside class="alert alert-danger" role="alert">
            <p>Registration does not exist.</p>
        </aside>
    <?php
        die();
    } ?>

    <p class="fs-3">LTI-Platform Details</p>

    <?php
    // Handle Warning / Info / Error Messages
    if (isset($_POST["action"])) {
        // Handle 'Cancel Edit Platform'
        if (htmlspecialchars($_POST["action"]) == "discard_edit_platform") { ?>
            <aside class="alert alert-warning" role="alert">
                <p>Canceled Editing.</p>
            </aside> <?php
                    }

                    // Handle 'Edit Registration'
                    if ((htmlspecialchars($_POST["action"]) == "edit_registration")
                        && (isset($_POST["name"]))
                        && (isset($_POST["platform_id"]))
                        && (isset($_POST["client_id"]))
                        && (isset($_POST["authentication_request_url"]))
                        && (isset($_POST["access_token_url"]))
                        && (isset($_POST["key_set_url"]))
                    ) {
                        $name = trim(htmlspecialchars($_POST["name"]));
                        $platformId = htmlspecialchars($_POST["platform_id"]);
                        $clientId = htmlspecialchars($_POST["client_id"]);
                        $authenticationRequestUrl = htmlspecialchars($_POST["authentication_request_url"]);
                        $accessTokenUrl = htmlspecialchars($_POST["access_token_url"]);
                        $keySetUrl = htmlspecialchars($_POST["key_set_url"]);

                        $error = "";
                        if (strlen($name) == 0) {
                            $error = "Invalid platform name.";
                        } else {
                            $error = editLTIRegistration($registrationId, $name, $platformId, $clientId, $authenticationRequestUrl, $accessTokenUrl, $keySetUrl) ? "" : "Error updating platform.";
                        }
                        if ($error) { ?>
                <aside class="alert alert-danger" role="alert">
                    <p><?php echo $error; ?> </p>
                </aside> <?php
                        } else { ?>
                <aside class="alert alert-success" role="alert">
                    <p>Successfully updated Registration!</p>
                </aside> <?php
                        }
                    }
                }

                // Retrieve Registration
                $registration = getLTIRegistrationData($registrationId);
                if (!isset($_POST["action"])) {
                    if (registrationComplete($registrationId)) { ?>
            <aside class="alert alert-success" role="alert">
                Platform-Registration is complete! Change values here, if something is not working.<?php
                                                                                                } else {
                                                                                                    if ($registration['auto_registration']) { ?>
                <aside class="alert alert-danger" role="alert">
                    Automatic Registration not completed! <br> Check if registration was completed on the platform-side
                    before doing manual changes! <?php
                                                                                                    } else { ?>
                    <aside class="alert alert-warning" role="alert">
                        Values missing. This platform is manually registered. Please add missing values! <?php
                                                                                                        }
                                                                                                    };
                                                                                                            ?>
                    </aside>
                <?php
                } ?>
                <main>
                    <section>
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo $registration['name']; ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="platformId" class="form-label">Platform-ID</label>
                            <input type="text" class="form-control" id="platformId" name="platformId" value="<?php echo $registration['platform_id']; ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="clientId" class="form-label">Client-ID</label>
                            <input type="text" class="form-control" id="clientId" name="clientId" value="<?php echo $registration['client_id']; ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="authRequestUrl" class="form-label">Authentication Request URL</label>
                            <input type="text" class="form-control" id="authRequestUrl" name="authRequestUrl" value="<?php echo $registration['authentication_request_url']; ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="accessTokenUrl" class="form-label">Access-Token URL</label>
                            <input type="text" class="form-control" id="accessTokenUrl" name="accessTokenUrl" value="<?php echo $registration['access_token_url']; ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="keySetUrl" class="form-label">Keyset URL</label>
                            <input type="text" class="form-control" id="keySetUrl" name="keySetUrl" value="<?php echo $registration['jwks_url']; ?>" readonly>
                        </div>
                        <div id="editActions" style="display: none">
                            <a class="btn btn-danger" id="cancelEditing" onclick="discardEdits(<?php echo $registrationId; ?>)">Cancel Editing</a>
                            <a class="btn btn-success" id="saveChanges" onclick="saveEdits(<?php echo $registrationId; ?>)">Save
                                Changes</a>
                        </div>
                        <a class="btn btn-primary" id="startEditingButton" onclick="startEditMode()">Edit Platform
                            Details</a>
                        <br><br>

                    </section>
                    <section>
                        <p class="fs-3">LTI-Platform Deployments:</p>
                        <?php

                        // Handle 'Add new Deployment'
                        if ((isset($_POST["action"])) && (htmlspecialchars($_POST["action"]) == "add_deployment") && (isset($_POST["name"])) && (isset($_POST["deployment_id"]))) {
                            $name = trim(htmlspecialchars($_POST["name"]));
                            $deploymentId = htmlspecialchars($_POST["deployment_id"]);
                            $error = "";
                            if (strlen($name) == 0) {
                                $error = "Invalid deployment name.";
                            } else if (strlen(trim($deploymentId)) == 0) {
                                $error = "Invalid Deployment-ID.";
                            } else {
                                $error = addLtiDeployment($registrationId, $name, $deploymentId) ? "" : "Error adding deployment.";
                            }
                            if ($error) { ?>
                                <aside class="alert alert-danger" role="alert">
                                    <p><?php echo $error; ?> </p>
                                </aside> <?php
                                        } else { ?>
                                <aside class="alert alert-success" role="alert">
                                    <p>Successfully added Deployment!</p>
                                </aside> <?php
                                        }
                                    }

                                    // Handle 'Remove Deployment'
                                    if ((isset($_POST["action"])) && (htmlspecialchars($_POST["action"]) == "remove_deployment") && (isset($_POST["deployment_id"]))) {
                                        $deploymentId = htmlspecialchars($_POST["deployment_id"]);
                                        $error = removeLtiDeploymentWithDependencies($deploymentId, $registrationId) ? "" : "Error removing deployment.";

                                        if ($error) { ?>
                                <aside class="alert alert-danger" role="alert">
                                    <p><?php echo $error; ?> </p>
                                </aside> <?php
                                        } else { ?>
                                <aside class="alert alert-success" role="alert">
                                    <p>Successfully removed Deployment!</p>
                                </aside> <?php
                                        }
                                    }

                                    // Add Warning if no deployments are found (only if no action was performed)
                                    if (!isset($_POST["action"]) && getLTIDeploymentCount($registrationId) == 0) {
                                            ?>
                            <aside class="alert alert-warning" role="alert">
                                <p>No Deployments yet. Add Deployments below.</p>
                            </aside> <?php
                                    }

                                    $deployments = getAllLTIDeployments($registrationId);
                                    if (getLTIDeploymentCount($registrationId) > 0) { ?>
                            <article>
                                <table class="table">
                                    <tr>
                                        <th scope="col">Name</th>
                                        <th scope="col">Deployment-ID</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                    <?php
                                        while ($row = $deployments->fetch(PDO::FETCH_ASSOC)) { ?>
                                        <tr>
                                            <td>
                                                <p class="fw-normal">
                                                    <?php echo $row["name"]; ?>
                                                </p>
                                            </td>
                                            <td>
                                                <p class="fw-normal">
                                                    <?php echo $row["deployment_id"]; ?>
                                                </p>
                                            </td>
                                            <td>
                                                <button class="tag btn btn-danger" onclick="removeDeployment('<?php echo $registrationId; ?>', '<?php echo $row['deployment_id']; ?>')">
                                                    Remove
                                                </button>
                                            </td>
                                        </tr>
                                <?php
                                        }
                                        echo "</table></article>";
                                    }
                                ?>
                                <article>
                                    <form action="javascript:void(0);" style="margin-bottom: 1rem;">
                                        <div class="mb-3">
                                            <label for="deploymentName" class="form-label">Deployment Name</label>
                                            <input type="text" class="form-control" id="deploymentName">
                                        </div>

                                        <label for="deploymentId" class="form-label">Deployment ID</label>
                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control" id="deploymentId">
                                        </div>

                                        <button class="btn btn-primary" id="addDeploymentButton" onclick="addDeployment(<?php echo $registrationId; ?>)">
                                            Add deployment
                                        </button>
                                    </form>
                                </article>
                    </section>

                </main>
</body>

</html>
<?php $DB = NULL; ?>