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

    // Connect to DB
    $DB = getDatabaseHandleOrPrintError();
    if (!isLoggedIn()) {
        $DB = NULL;
        die();
    } ?>
    <p class="fs-3">Manage LTI Platform Registrations</p>
    <?php
    // Handle 'Add new Platform'
    if ((isset($_POST["action"])) && (htmlspecialchars($_POST["action"]) == "add_platform") && (isset($_POST["name"])) && (isset($_POST["auto_registration"]))) {
        $name = trim(htmlspecialchars($_POST["name"]));
        $autoRegistration = htmlspecialchars($_POST["auto_registration"]);
        $error = "";
        if (strlen($name) == 0) {
            $error = "Invalid platform name.";
        } else if ($autoRegistration != "true" && $autoRegistration != "false") {
            $error = "Invalid registration method.";
        } else {
            $autoRegistrationBit = $autoRegistration == "true";
            $error = addEmptyLtiRegistration($name, $autoRegistrationBit) ? "" : "Error adding platform.";
        }
        if ($error) { ?>
            <aside class="alert alert-danger" role="alert">
                <p><?php echo $error; ?> </p>
            </aside> <?php
                    } else { ?>
            <aside class="alert alert-success" role="alert">
                <p>Successfully added Platform!</p>
            </aside> <?php
                    }
                }

                // Handle 'Remove Platform'
                if ((isset($_POST["action"])) && (htmlspecialchars($_POST["action"]) == "remove_platform") && (isset($_POST["registration_id"]))) {
                    $registrationId = htmlspecialchars($_POST["registration_id"]);
                    $error = removeLtiRegistrationWithDependencies($registrationId) ? "" : "Error removing platform.";

                    if ($error) { ?>
            <aside class="alert alert-danger" role="alert">
                <p><?php echo $error; ?> </p>
            </aside> <?php
                    } else { ?>
            <aside class="alert alert-success" role="alert">
                <p>Successfully removed Platform!</p>
            </aside> <?php
                    }
                }

                // Show Warning if no Platforms are added
                if (!isset($_POST["action"]) && getLTIRegistrationCount() == 0) {
                        ?>
        <aside class="alert alert-warning" role="alert">
            <p>No Platforms registered yet. Add a new Platform below.</p>
        </aside> <?php
                }
                    ?>

    <main>
        <?php
        // Get all users
        $registrations = getAllLTIRegistrations();
        if ($registrations == NULL) {
        ?>
            <aside class="alert alert-danger" role="alert">
                <p>Could not fetch registrations</p>
            </aside>
        <?php
            $DB = NULL;
            die();
        }
        // Display all accounts

        if (getLTIRegistrationCount() > 0) { ?>
            <article>
                <table class="table">
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col">Platform ID</th>
                        <th scope="col">Client ID</th>
                        <th scope="col">Deployments</th>
                        <th scope="col">Status</th>
                        <th scope="col">Actions</th>
                    </tr>

                    <?php
                    while ($row = $registrations->fetch(PDO::FETCH_ASSOC)) { ?>
                        <tr>
                            <td>
                                <p class="fw-normal">
                                    <?php echo $row["name"]; ?>
                                </p>
                            </td>
                            <td>
                                <p class="fw-normal">
                                    <?php echo $row["platform_id"]; ?>
                                </p>
                            </td>
                            <td>
                                <p class="fw-normal">
                                    <?php echo $row["client_id"]; ?>
                                </p>
                            </td>
                            <td>
                                <p class="fw-normal">
                                    <?php echo getLTIDeploymentCount($row["registration_id"]); ?>
                                </p>
                            </td>
                            <td>
                                <?php
                                if (registrationComplete($row["registration_id"])) {
                                ?> <p class="fw-normal badge rounded-pill bg-success tag"> <?php
                                                                                    } else {
                                                                                        ?>
                                    <p class="fw-normal badge rounded-pill bg-warning tag"> <?php
                                                                                        }
                                                                                            ?>
                                    <?php echo getRegistrationStatusMessage($row["registration_id"]); ?>
                                    </p>
                            </td>
                            <td>
                                <a class="btn btn-dark" role="button" href="./lti-platform-details?registration_id=<?php echo rawurlencode($row["registration_id"]); ?>">Details / Edit</a>
                                <a class="btn btn-danger" role="button" onclick="removePlatform(<?php echo rawurlencode($row["registration_id"]); ?>)">Delete</a>
                            </td>

                        <?php
                    }
                        ?>
                </table>
            </article>
        <?php
        }
        ?>
        <article>
            <form action="javascript:void(0)">
                <p class="fs-4" id="add-platform-paragraph">Add a new Platform</p>

                <label for="platformName" class="form-label">Platform Name</label>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="platformName" name="platform_name">
                </div>


                <p class="fs-6">Registration Method - Hover with Mouse for Details</p>
                <div class="form-check" data-toggle="tooltip" title="All details need to be exchanged manually with the platform. Only use if the platform is not supporting automatic registration." data-placement="right">
                    <input class="form-check-input" type="radio" name="radio" id="radioManual" value="manual" checked />
                    <label class="form-check-label" for="radioManual">Manual Registration</label>
                </div>
                <div class="form-check" id="radioRegistration" data-toggle="tooltip" title="You'll get a registration link for platforms with auto registration support. Details are exchanged automatically. - Not yet implemented." data-placement="right">
                    <input class="form-check-input" type="radio" name="radio" id="radioAuto" value="auto" disabled>
                    <label class="form-check-label" for="radioAuto">Automatic Registration</label>
                </div>

                <button class="btn btn-primary" id="addPlatformButton" onclick="addPlatform()" style="margin-top: 1rem;">Add new Platform
                </button>
            </form>
        </article>
    </main>
</body>

</html>
<?php $DB = NULL; ?>