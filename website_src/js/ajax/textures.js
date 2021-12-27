var tags = [];

window.onload = function () {
    $("#textures").load("snippets/textures.php", addTagsElements);
}

function removeTexture(id) {
    $("#textures").load("snippets/textures.php",
    {
        "texture_table_id": id,
    }, addTagsElements);
}

function addTexture() {
    const textureId = document.getElementById("textureId").value;
    const textureLevel = document.getElementById("textureLevel").value;
    const textureUrl = document.getElementById("textureUrl").value;
    const rights = document.getElementById("rights").value;
    const notice = document.getElementById("notice").value;

    $("#textures").load("snippets/textures.php",
    {
        "textureId": textureId,
        "textureLevel": textureLevel,
        "textureUrl": textureUrl,
        "textureRights": rights,
        "textureNotice": notice,
        "textureTags": JSON.stringify(tags)
    }, function() {
        addTagsElements();
        tags = []
    });
}


function addTagsElements() {
    var container = document.getElementById('tagsArea');
    var tagsArea = document.createElement('div');
    tagsArea.id = 'tagsAreaDiv';
    tagsArea.innerHTML = '<div class="input-group mb-3" style="margin-top: 1rem;"><input type="text" class="form-control" placeholder="Tag" aria-label="Tag" aria-describedby="buttonTag" id="tagInput"><button class="btn btn-primary" type="button" id="buttonTag">Add</button></div>';
    container.appendChild(tagsArea);
    document.getElementById("buttonTag").addEventListener("click", addTag);
}

function addTag() {
    var tagInputForm = document.getElementById("tagInput");
    var tagToAdd = tagInputForm.value;
    tagInputForm.value = "";

    if ((!(tags.includes(tagToAdd))) && (tagToAdd.length > 0)) {
        tags.push(tagToAdd);

        //add tag
        var tagsArea = document.getElementById("tagsAreaDiv");
        var tagElement = document.createElement('div');
        tagElement.classList.add("input-group", "mb-3");
        tagElement.style.marginTop = "1rem;";
        tagElement.id = "divTag" + tagToAdd;

        var tagInput = document.createElement('input');
        tagInput.type = "text";
        tagInput.classList.add("form-control");
        tagInput.setAttribute("aria-describedby", "buttonTag");
        tagInput.id = "tagIntermediate" + tagToAdd;
        tagInput.disabled = true;
        tagInput.value = tagToAdd;

        var removeButton = document.createElement('button');
        removeButton.classList.add("btn", "btn-primary");
        removeButton.type = "button";
        removeButton.id = "tagIntermediate" + tagToAdd;
        removeButton.innerText = "Remove";

        tagElement.appendChild(tagInput);
        tagElement.appendChild(removeButton);
        tagsArea.appendChild(tagElement);

        removeButton.addEventListener("click", function () {
            tags = tags.filter(currentTag => currentTag !== tagToAdd);
            tagsArea.removeChild(tagElement);
        });
    }
}