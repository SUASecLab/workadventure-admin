<?php
session_start();
?>
<!doctype html>
<html lang="en">

<body>
    <?php
    require_once('../util/database_operations.php');
    require_once('../util/web_login_functions.php');
    // Connect to database
    $DB = getDatabaseHandleOrPrintError();
    if (!isLoggedIn()) {
        $DB = NULL;
        die();
    }
    ?>

    <main>
        <p class="fs-3">
            Listing Lti-Roles
        </p>
        <article>

            <?php
            // Handle Actions
            if ((isset($_POST["action"]))) {
                // Handle 'Remove Role-Mapping'
                if ((htmlspecialchars($_POST["action"]) == "remove_role_mapping") && (isset($_POST["role"]))) {
                    $role = htmlspecialchars($_POST["role"]);
                    $error = removeAllTagsForRole($role) ? "" : "Error removing mapping.";
                    if ($error) { ?>
                        <aside class="alert alert-danger" role="alert">
                            <p><?php echo $error; ?> </p>
                        </aside> <?php
                                } else { ?>
                        <aside class="alert alert-success" role="alert">
                            <p>Successfully removed Mappings!</p>
                        </aside> <?php
                                }
                            }
                            // Handle 'Add Role-Mapping'
                            if ((htmlspecialchars($_POST["action"]) == "add_role_mapping") && (isset($_POST["role"])) && (isset($_POST["tag"]))) {
                                $role = htmlspecialchars($_POST["role"]);
                                $tag = htmlspecialchars($_POST["tag"]);
                                if ($role == "http://purl.imsglobal.org/vocab/lis/v2/") {
                                    ?>
                        <aside class="alert alert-danger" role="alert">
                            <p> Could not add mapping. Empty role is not valid.</p>
                        </aside> <?php
                                } else if (strlen(trim($tag)) == 0) {
                                    ?>
                        <aside class="alert alert-danger" role="alert">
                            <p> Could not add mapping. Empty tag is not valid.</p>
                        </aside> <?php
                                } else {
                                    $error = addTagForLtiRole($role, $tag) ? "" : "Error adding mapping.";
                                    if ($error) { ?>
                            <aside class="alert alert-danger" role="alert">
                                <p><?php echo $error; ?> </p>
                            </aside> <?php
                                    } else { ?>
                            <aside class="alert alert-success" role="alert">
                                <p>Successfully added Mapping!</p>
                            </aside> <?php
                                    }
                                }
                            }
                        }

                                        ?>
        </article>
        <?php
        // Get all users
        $mappedRoles = getAllMappedLtiRoles();
        if ($mappedRoles == NULL) {
        ?>
            <aside class="alert alert-danger" role="alert">
                <p>No Mapped Lti Roles saved.</p>
            </aside>
        <?php
            $DB = NULL;
            die();
        }
        // Display all accounts

        ?>
        <article>
            <table class="table">
                <tr>
                    <th scope="col">Role</th>
                    <th scope="col">Associated Tags</th>
                    <th scope="col">Actions</th>
                </tr>

                <?php
                foreach ($mappedRoles as $role) { ?>
                    <tr>
                        <td>
                            <p class="fw-normal">
                                <?php echo $role; ?>
                            </p>
                        </td>
                        <td>
                            <?php $tags = getTagsForLtiRole($role);
                            foreach ($tags as $currentTag) { ?>
                                <div class="badge rounded-pill bg-primary tag">
                                    <?php echo $currentTag; ?>
                                </div> <?php
                                    }
                                        ?>
                        </td>
                        <td>
                            <a class="btn btn-dark" role="button" href="./edit-lti-role-mapping?role=<?php echo rawurlencode($role); ?>">Edit</a>
                            <a class="btn btn-danger" role="button" onclick="removeRoleMapping('<?php echo $role; ?>')">Delete</a>
                        </td>

                    <?php
                }
                    ?>
            </table>
        </article>
        <article>
            <form action="javascript:void(0)">
                <p class="fs-4" id="add-platform-paragraph">Add a new LTI-Role-Mapping</p>
                <label for="role" class="form-label">Role Name</label>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="map_url_file_prefix">http://purl.imsglobal.org/vocab/lis/v2/</span>
                    <input type="text" class="form-control" id="role" name="role">
                </div>
                <label for="tag" class="form-label">Tag Name</label>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="tag" name="tag">
                </div>



                <button class="btn btn-primary" id="addMappingButton" onclick="addMapping()" style="margin-top: 1rem;">Add new Mapping
                </button>
            </form>
        </article>
    </main>
</body>

</html>
<?php $DB = NULL; ?>