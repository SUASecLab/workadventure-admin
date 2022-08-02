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
// remove texture if requested
if ((isset($_POST["action"])) && (htmlspecialchars($_POST["action"]) == "removeTexture") && (isset($_POST["id"]))) {
    $textureToRemoveId = htmlspecialchars($_POST["id"]);
    if (removeTexture($textureToRemoveId)) { ?>
      <aside class="alert alert-success" role="alert">
        <p>Texture has been removed</p>
      </aside>
    <?php
    } else { ?>
      <aside class="alert alert-danger" role="alert">
        <p>Could not remove texture</p>
      </aside>
    <?php
    }
}
// check whether texture should be added
if ((isset($_POST["action"])) && (htmlspecialchars($_POST["action"]) == "addTexture") && (isset($_POST["textureId"])) && (isset($_POST["textureLayer"])) && (isset($_POST["textureUrl"]))) {
    $textureId = htmlspecialchars($_POST["textureId"]);
    $textureLayer = htmlspecialchars($_POST["textureLayer"]);
    $textureUrl = htmlspecialchars($_POST["textureUrl"]);
    
    if (isset($_POST["textureTags"])) {
      $textureTags = (array) json_decode($_POST["textureTags"]);
    } else {
      $textureTags = array();
    }

    if (strlen(trim(htmlspecialchars($_POST["textureUrl"]))) == 0) { ?>
      <aside class="alert alert-danger" role="alert">
        Invalid URL
      </aside>
    <?php
    } else if (strlen(trim($textureId)) == 0) { ?>
      <aside class="alert alert-danger" role="alert">
        Invalid ID
      </aside>
      <?php
    } else {
      if (storeTexture($textureId, $textureLayer, $textureUrl, $textureTags)) { ?>
        <aside class="alert alert-success" role="alert">
          <p>Stored texture</p>
        </aside>
    <?php
      } else{ ?>
        <aside class="alert alert-danger" role="alert">
          <p>Could not store texture</p>
        </aside>
      <?php
      }
    }
  }
  ?>
          <article>
            <p class="fs-3">Add texture:</p>
            <form action="javascript:void(0);" style="margin-bottom: 1rem;">
              <div class="mb-3">
                <label for="textureId" class="form-label">Texture ID</label>
                <input type="text" class="form-control" id="textureId">
              </div>
              <div class="mb-3">
                <label for="textureLayer" class="form-label">Texture Layer</label>
                <select class="form-control" id="textureLayer">
                  <option value="woka">Woka</option>
                  <option value="body">Body</option>
                  <option value="eyes">Eyes</option>
                  <option value="hair">Hair</option>
                  <option value="clothes">Clothes</option>
                  <option value="hat">Hat</option>
                  <option value="accessory">Accessory</option>
                </select>
              </div>
              <label for="textureUrl" class="form-label">Texture URL</label>
              <div class="input-group mb-3">
                <input type="text" class="form-control" id="textureUrl">
              </div>
              <div class="mb-3">
                <label for="tagsInput" class="form-label">Tags (optional):</label>
              </div>
              <div id="tagsArea" class="input-group mb-3">
                <div id="tagsAreaDiv">
                  <div class="input-group mb-3" style="margin-top: 1rem;">
                    <input type="text" class="form-control" placeholder="Tag" aria-label="Tag" aria-describedby="buttonTag" id="tagInput">
                    <button class="btn btn-primary" type="button" id="buttonTag">Add</button>
                  </div>
                </div>
              </div>
              <button class="btn btn-primary" id="addTextureButton">
                Add texture
              </button>
            </form>
          </article>
<?php

if (texturesStored()) {
    $textures = getTextures();
    if ($textures == NULL) { ?>
      <aside class="alert alert-danger" role="alert">
        <p>Could not fetch textures</p>
      </aside>
      <main>
      <?php
    } else { ?>
        <main>
          <article>
            <p class="fs-3">Textures:</p>
            <table class="table">
              <thead>
                <th scope="col">ID</th>
                <th scope="col">Layer</th>
                <th scope="col">URL</th>
                <th scope="col">Image</th>
                <th scope="col">Restriction</th>
                <th scope="col">Action</th>
              </thead>
              <?php
        foreach ($textures as $texture) {
            $tags = NULL;
            $texture = iterator_to_array($texture);
            if (array_key_exists("tags", $texture)) {
              $tags = iterator_to_array($texture["tags"]);
            }
            ?>
                <tr>
                  <td>
                    <p class="fw-normal">
                      <?php echo $texture["waId"]; ?>
                    </p>
                  </td>
                  <td>
                    <p class="fw-normal">
                      <?php echo $texture["layer"]; ?>
                    </p>
                  </td>
                  <td>
                    <p class="fw-normal">
                      <?php echo $texture["url"]; ?>
                    </p>
                  </td>
                  <td>
                    <?php
                      $textureUrl = (string) $texture["url"];
                      if (str_starts_with($texture["url"], "resources")) {
                        $textureUrl = "https://".getenv("DOMAIN")."/".$textureUrl;
                      }
                      echo '<img src="'.$textureUrl.'">';
                    ?>
                  </td>
                  <?php if ($tags == NULL) { ?>
                    <td>
                      <p class="fw-normal">Public</p>
                    </td>
                  <?php
            } else {
                $tagsAsString = "";
                foreach ($tags as $tag) {
                    $tagsAsString = $tagsAsString . "<div class=\"badge rounded-pill bg-primary tag\">" . $tag . "</div>";
                }
                echo "<td>" . $tagsAsString . "</td>";
            } ?>
                  <td>
                    <button class="tag btn btn-danger" onclick="removeTexture('<?php echo $texture['_id']; ?>');">
                      Remove
                    </button>
                  </td>
                </tr>
          <?php
        }
        echo "</table></article>";
    }
}
?>
</body>

</html>
<?php $DB = NULL; ?>