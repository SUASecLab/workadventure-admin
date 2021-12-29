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
// remove message if requested
if ((isset($_POST["action"])) && (htmlspecialchars($_POST["action"]) == "removeMessage") && (isset($_POST["id"]))) {
    $id = htmlspecialchars($_POST["id"]);
    if (deleteGlobalMessage($id)) { ?>
            <aside class="alert alert-success" role="alert">
                <p>Removed message</p>
            </aside>
        <?php
    } else { ?>
            <aside class="alert alert-danger" role="alert">
                <p>Could not remove message</p>
            </aside>
        <?php
    }
}
// create new message if requested
if ((isset($_POST["action"])) && (htmlspecialchars($_POST["action"]) == "addMessage") && (isset($_POST["message"]))) {
    $message = htmlspecialchars($_POST["message"]);
    if (createNewGlobalMessage($message)) { ?>
            <aside class="alert alert-success" role="alert">
                <p>Created new message</p>
            </aside>
        <?php
    } else { ?>
            <aside class="alert alert-danger" role="alert">
                <p>Could not create new message</p>
            </aside>
        <?php
    }
}
// Get all messages
$messages = getGlobalMessages();
if ($messages == NULL) { ?>
        <aside class="alert alert-danger" role="alert">
            <p>Could not fetch global messages</p>
        </aside>
    <?php
    $DB = NULL;
    die();
} ?>
    <main>
        <article>
            <?php if (globalMessagesExist()) { ?>
                <p class="fs-3">Global messages:</p>
                <section class="alert alert-warning" role="alert">
                    <p>Only the top message will be shown to the user. If the user also receives a private message, the
                        global message will be shown instead of the private one!</p>
                </section>
                <section>
                    <table class="table">
                        <tr>
                            <th scope="col">Message</th>
                            <th scope="col">Actions</th>
                        </tr>
                        <?php
    $counter = 0;
    while ($row = $messages->fetch(PDO::FETCH_ASSOC)) { ?>
                            <tr>
                                <td>
                                    <div id="editor-container-<?php echo $counter; ?>" style="height: 150px;">
                                    </div>
                                    <script>
                                        var quill<?php echo $counter; ?> = new Quill('#editor-container-<?php echo $counter; ?>', {
                                            modules: {
                                                toolbar: false
                                            },
                                            theme: 'snow'
                                        });
                                        quill<?php echo $counter; ?>.setContents(<?php echo htmlspecialchars_decode($row["message"]); ?>.ops);
                                        quill<?php echo $counter; ?>.disable();
                                    </script>
                                </td>
                                <td>
                                    <button class="tag btn btn-danger" onclick="removeMessage('<?php echo $row['message_id']; ?>');">
                                        Remove
                                    </button>
                                </td>
                            </tr>
                        <?php
        $counter++;
    } ?>
                    </table>
                </section>
            <?php
} ?>
            <section>
                <p class="fs-3">Create new global message:</p>
                <form action="javascript:void(0);" id="create-new-message">
                    <div class="mb-3">
                        <div id="editor-container" style="height: 150px;"></div>
                        <script>
                            var quill = new Quill('#editor-container', {
                                modules: {
                                    toolbar: quillToolbarSettings
                                },
                                placeholder: 'Enter global message here...',
                                theme: 'snow'
                            });
                        </script>
                    </div>
                    <button type="submit" class="btn btn-primary" id="addMessageButton">
                        Create
                    </button>
                </form>
            </section>
        </article>
    </main>
</body>

</html>

<?php $DB = NULL; ?>