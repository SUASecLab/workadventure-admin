var tagsElementsShown = false;
var newMapsTags = [];

window.onload = function () {
    $("#rooms").load("snippets/rooms.php", setupMaps);
}

function setupMaps() {
    const radioPublic = document.getElementById("radioPublic");
    const radioMembers = document.getElementById("radioMembers");
    const radioMembersTags = document.getElementById("radioMembersTags");
    const addMapButton = document.getElementById("addMapButton");

    radioPublic.addEventListener("click", removeTagsElements);
    radioMembers.addEventListener("click", removeTagsElements);
    radioMembersTags.addEventListener("click", addTagsElements);
    addMapButton.addEventListener("click", addMap);

    tagsElementsShown = false;
    newMapsTags = [];
}

function removeMap(mapUrl, csrf_token) {
    $("#rooms").load("snippets/rooms.php", {
        "action": "removeMap",
        "mapUrl": mapUrl,
        "csrf_token": csrf_token
    }, setupMaps);
}

function addMap() {
    const mapUrl = document.getElementById("mapURL").value;
    const mapFileUrl = document.getElementById("mapURLFile").value;
    const accessRestrictionRadios = document.getElementsByName("radio");
    const csrf_token = document.getElementById("csrf_token").value;
    var accessRestriction = -1;

    for (var i = 0; i < accessRestrictionRadios.length; i++) {
        if (accessRestrictionRadios[i].checked) {
            accessRestriction = i + 1;
        }
    }

    var data = {
        "action": "addMap",
        "mapUrl": mapUrl,
        "fileUrl": mapFileUrl,
        "access": accessRestriction,
        "csrf_token": csrf_token
    };

    if (accessRestriction === 3) {
        data.tags = JSON.stringify(newMapsTags);
    }

    $("#rooms").load("snippets/rooms.php", data, setupMaps);
}

function addTagsElements() {
    if (!tagsElementsShown) {
        var container = document.getElementById('tagsArea');
        var tagsArea = document.createElement('div');
        container.appendChild(tagsArea);

        tagsArea.outerHTML =
            '<div id="tagsAreaDiv">' +
            '<div class="input-group mb-3" style="margin-top: 1rem;">' +
            '<input type="text" class="form-control" placeholder="Tag" aria-label="Tag" aria-describedby="buttonTag" id="tagInput">' +
            '<button class="btn btn-primary" type="button" id="buttonTag">Add</button>' +
            '</div>' +
            '</div>';
        document.getElementById("buttonTag").addEventListener("click", addTag);
    }

    tagsElementsShown = true;
    newMapsTags = [];
}

function removeTagsElements() {
    if (tagsElementsShown) {
        document.getElementById('tagsAreaDiv').remove();
    }

    tagsElementsShown = false;
    newMapsTags = [];
}

function addTag() {
    var tagInputForm = document.getElementById("tagInput");
    var tagToAdd = tagInputForm.value;
    tagInputForm.value = "";

    if ((!(newMapsTags.includes(tagToAdd))) && (tagToAdd.length > 0)) {
        newMapsTags.push(tagToAdd);

        var tagsArea = document.getElementById("tagsAreaDiv");
        var tagElement = document.createElement("div");
        tagsArea.appendChild(tagElement);

        const tagElementId = "divTag" + tagToAdd;
        const tagDescription = "tagIntermediate" + tagToAdd;

        tagElement.outerHTML =
            '<div class="input-group mb-3" style="margin-top: 1rem;" id="' + tagElementId + '">' +
            '<input type="text" class="form-control" aria-described-by="' + tagDescription +
            '" disabled value="' + tagToAdd + '">' +
            '<button class="btn btn-primary" type="button" id="' + tagDescription + '">' +
            'Remove</button>' +
            '</div>';

        document.getElementById(tagDescription).addEventListener("click", function () {
            newMapsTags = newMapsTags.filter(currentTag => currentTag !== tagToAdd);
            tagsArea.removeChild(document.getElementById(tagElementId));
        });
    }
}