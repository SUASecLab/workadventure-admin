<?php
session_start();
?>
<!doctype html>
<html lang="en">

<body>
  <?php
  // Connect to database
  $DB = NULL;
  try {
    $DB = new PDO(
      "mysql:dbname=" . getenv('DB_MYSQL_DATABASE') . ";host=admin-db;port=3306",
      getenv('DB_MYSQL_USER'),
      getenv('DB_MYSQL_PASSWORD')
    );
  } catch (PDOException $exception) { ?>
    <aside class="alert alert-danger" role="alert">";
      Could not connect to database: <?php echo $exception->getMessage(); ?>
    </aside>
    <?php return;
  }
  require_once('../api/database_operations.php');
  require_once('../login_functions.php');

  if (!isLoggedIn()) {
    http_response_code(403);
    die();
  }

  // remove texture if requested
  if (isset($_POST["texture_table_id"])) {
    $textureToRemoveId = htmlspecialchars($_POST["texture_table_id"]);
    if (removeTexture($textureToRemoveId)) { ?>
      <aside class="alert alert-success" role="alert">
        <p>Texture has been removed</p>
      </aside>
    <?php } else { ?>
      <aside class="alert alert-danger" role="alert">
        <p>Could not remove texture</p>
      </aside>
    <?php }
  }

  // check whether url has been added
  if ((isset($_POST["textureUrl"])) && (strlen(htmlspecialchars($_POST["textureUrl"])) > 0) && (htmlspecialchars($_POST["textureUrl"]) != "https://")) {
    $textureId = htmlspecialchars($_POST["textureId"]);
    $textureLevel = htmlspecialchars($_POST["textureLevel"]);
    $textureUrl = htmlspecialchars($_POST["textureUrl"]);
    $textureRights = htmlspecialchars($_POST["textureRights"]);
    $textureNotice = htmlspecialchars($_POST["textureNotice"]);
    $error = false;
    if (storeTexture($textureId, $textureLevel, $textureUrl, $textureRights, $textureNotice)) {
      if ((isset($_POST["textureTags"])) && (!strlen(htmlspecialchars($_POST["textureTags"])) == 0)) {
        $tagsList = json_decode($_POST["textureTags"]);
        $lastTexture = getLastTextureId();
        if ($lastTexture != -1) {
          if (sizeof($tagsList) > 0) {
            foreach ($tagsList as $tag) {
              if (!storeTextureTag($lastTexture, trim(htmlspecialchars($tag)))) {
                $error = true;
              }
            }
          }
        } else {
          $error = true;
        }
      }
    } else {
      $error = true;
    }
    if ($error) { ?>
      <aside class="alert alert-danger" role="alert">
        <p>Could not store texture</p>
      </aside>
    <?php } else {
    ?>
      <aside class="alert alert-success" role="alert">
        <p>Stored texture</p>
      </aside>
    <?php
    }
  }

  if (customTexturesStored()) {
    $textures = getCustomTextures();
    if ($textures == NULL) { ?>
      <aside class="alert alert-danger" role="alert">
        <p>Could not fetch custom textures</p>
      </aside>
      <main>
      <?php } else { ?>
        <main>
          <article>
            <p class="fs-3">Custom textures:</p>
            <table class="table">
              <tr>
                <th scope="col">ID</th>
                <th scope="col">Level</th>
                <th scope="col">Url</th>
                <th scope="col">Rights</th>
                <th scope="col">Notice</th>
                <th scope="col">Restriction</th>
                <th scope="col">Action</th>
              </tr>
              <?php
              while ($row = $textures->fetch(PDO::FETCH_ASSOC)) {
                $tags = getTextureTags($row["texture_table_id"]); ?>
                <tr>
                  <td>
                    <p class="fw-normal">
                      <?php echo $row["texture_id"]; ?>
                    </p>
                  </td>
                  <td>
                    <p class="fw-normal">
                      <?php echo $row["texture_level"]; ?>
                    </p>
                  </td>
                  <td>
                    <p class="fw-normal">
                      https://<?php echo $row["url"]; ?>
                    </p>
                  </td>
                  <td>
                    <p class="fw-normal">
                      <?php echo $row["rights"]; ?>
                    </p>
                  </td>
                  <td>
                    <p class="fw-normal">
                      <?php echo $row["notice"]; ?>
                    </p>
                  </td>
                  <?php if (empty($tags)) { ?>
                    <td>
                      <p class="fw-normal">Public</p>
                    </td>
                  <?php } else {
                    $tagsAsString = "";
                    foreach ($tags as $tag) {
                      $tagsAsString = $tagsAsString . "<div class=\"badge rounded-pill bg-primary tag\">" . $tag . "</div>";
                    }
                    echo "<td>" . $tagsAsString . "</td>";
                  } ?>
                  <td>
                    <button class="tag btn btn-danger" onclick="removeTexture('<?php echo $row['texture_table_id']; ?>');">
                      Remove
                    </button>
                  </td>
                </tr>
          <?php }
              echo "</table></article>";
            }
          }
          ?>
          <article>
            <p class="fs-3">Add custom texture</p>
            <form action="javascript:void(0);" style="margin-bottom: 1rem;">
              <div class="mb-3">
                <label for="textureId" class="form-label">Position ID</label>
                <input type="number" min="0" step="1" class="form-control" id="textureId" value="0">
              </div>
              <div class="mb-3">
                <label for="textureLevel" class="form-label">Texture Level</label>
                <input type="number" min="0" max="5" step="1" class="form-control" id="textureLevel" value="0">
              </div>
              <label for="textureUrl" class="form-label">Texture URL</label>
              <div class="input-group mb-3">
                <span class="input-group-text" id="textureUrlPrefix">https://</span>
                <input type="text" class="form-control" id="textureUrl" aria-describedby="textureUrlPrefix">
              </div>
              <div class="mb-3">
                <label for="rights" class="form-label">Rights</label>
                <input type="text" class="form-control" id="rights">
              </div>
              <div class="mb-3">
                <label for="notice" class="form-label">Notice</label>
                <input type="text" class="form-control" id="notice">
              </div>
              <div class="mb-3">
                <label for="tagsInput" class="form-label">Tags (optional):</label>
              </div>
              <div id="tagsArea" class="input-group mb-3">
              </div>
              <button class="btn btn-primary" onclick="addTexture();">
                Add texture
              </button>
            </form>
          </article>
</body>

</html>
<?php $DB = NULL; ?>