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
    if (!isset($_POST["role"]) || !$_POST["role"]) {
    ?>
        <aside class="alert alert-danger" role="alert">
            <p>Role not specified</p>
        </aside>
    <?php
        die();
    }
    $role = $_POST["role"];
    ?>
    <p class="fs-3">Manage Role-Tag-Mappings for "<?php echo $role ?>"</p>
    <main>
        <?php
        // Handle Actions
        if ((isset($_POST["action"])) && (isset($_POST["role"])) && (isset($_POST["tag"]))) {
            // Handle 'Remove Tag'
            if ((htmlspecialchars($_POST["action"]) == "remove_tag")) {
                $role = htmlspecialchars($_POST["role"]);
                $tag = htmlspecialchars($_POST["tag"]);
                $error = removeTagForRole($role, $tag) ? "" : "Error removing tag.";
                if ($error) { ?>
                    <aside class="alert alert-danger" role="alert">
                        <p><?php echo $error; ?> </p>
                    </aside> <?php
                            } else { ?>
                    <aside class="alert alert-success" role="alert">
                        <p>Successfully removed Tag "<?php echo $tag; ?>"!</p>
                    </aside> <?php
                            }
                        }
                        // Handle 'Add Tag'
                        if (htmlspecialchars($_POST["action"]) == "add_tag") {
                            $role = htmlspecialchars($_POST["role"]);
                            $tag = htmlspecialchars($_POST["tag"]);
                            if (strlen(trim($tag)) > 0) {
                                $error = addTagForLtiRole($role, $tag) ? "" : "Error adding tag.";
                                if ($error) { ?>
                        <aside class="alert alert-danger" role="alert">
                            <p><?php echo $error; ?> </p>
                        </aside> <?php
                                } else { ?>
                        <aside class="alert alert-success" role="alert">
                            <p>Successfully added Tag "<?php echo $tag; ?>"!</p>
                        </aside> <?php
                                }
                            } else { ?>
                    <aside class="alert alert-danger" role="alert">
                        <p>Cannot store empty tags!</p>
                    </aside> <?php
                            }
                        }
                    }

                                ?>
        <section class="mb-3">
            <?php
            $tags = getTagsForLtiRole($role);
            if ($tags) {
            ?>
                <p>Tags (click to remove):</p>
                <?php
                foreach ($tags as $currentTag) { ?>
                    <button class="tag btn btn-primary" onclick="removeTag('<?php echo $role; ?>', '<?php echo $currentTag; ?>');">
                        <?php echo $currentTag; ?>
                    </button>
            <?php
                }
                echo "<br><br>";
            }
            ?>
        </section>
        <section>
            <p>Add tag:</p>
            <form action="javascript:void(0)">
                <input class="form-control" type="text" id="newTag"><br>
                <button class="btn btn-primary" onclick="addTag('<?php echo $role; ?>');">Add tag</button>
            </form>
        </section>
    </main>
</body>

</html>
<?php $DB = NULL; ?>