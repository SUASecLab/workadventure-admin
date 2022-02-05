var tags = [];

window.onload = function () {
    $("#textures").load("snippets/textures.php", setup);
}

function setup() {
    const addTextureButton = document.getElementById("addTextureButton");
    addTextureButton.addEventListener("click", addTexture);

    const buttonTag = document.getElementById("buttonTag");
    buttonTag.addEventListener("click", addTag);

    tags = [];
}

function removeTexture(id) {
    $("#textures").load("snippets/textures.php", {
        "action": "removeTexture",
        "id": id,
    }, setup);
}

function addTexture() {
    const textureId = document.getElementById("textureId").value;
    const textureLevel = document.getElementById("textureLevel").value;
    const textureUrl = document.getElementById("textureUrl").value;
    const rights = document.getElementById("rights").value;
    const notice = document.getElementById("notice").value;

    $("#textures").load("snippets/textures.php", {
        "action": "addTexture",
        "textureId": textureId,
        "textureLevel": textureLevel,
        "textureUrl": textureUrl,
        "textureRights": rights,
        "textureNotice": notice,
        "textureTags": JSON.stringify(tags)
    }, setup);
}

function addTag() {
    var tagInputForm = document.getElementById("tagInput");
    var tagToAdd = tagInputForm.value;
    tagInputForm.value = "";

    if ((!(tags.includes(tagToAdd))) && (tagToAdd.length > 0)) {
        tags.push(tagToAdd);

        var tagsArea = document.getElementById("tagsAreaDiv");
        var tagElement = document.createElement('div');
        tagsArea.appendChild(tagElement);

        const tagElementId = "divTag" + tagToAdd;
        const tagDescription = "tagIntermediate" + tagToAdd;

        tagElement.outerHTML =
            '<div class="input-group mb-3" style="margin-top: 1rem;" id="' + tagElementId + '">' +
            '<input type="text" class="form-control" aria-describedby="' + tagDescription +
            '" disabled value="' + tagToAdd + '">' +
            '<button class="btn btn-primary" type="button" id="' + tagDescription + '">' +
            'Remove</button>' +
            '</div>';

        document.getElementById(tagDescription).addEventListener("click", function () {
            tags = tags.filter(currentTag => currentTag !== tagToAdd);
            tagsArea.removeChild(document.getElementById(tagElementId));
        });
    }
}