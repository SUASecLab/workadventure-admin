var tagsElementsShown = false;
var newMapsTags = [];

window.onload = function () {
    $("#maps").load("snippets/rooms.php");
    tagsElementsShown = false;
}

function remove(mapUrl) {
    $("#maps").load("snippets/rooms.php",
    {
        "removeMap": mapUrl
    });

    tagsElementsShown = false;
}

function addMap() {
    const mapUrl = document.getElementById("mapURL").value;
    const mapFileUrl = document.getElementById("mapURLFile").value;
    const accessRestrictionRadios = document.getElementsByName("radio");
    var accessRestriction = -1;

    for (var i = 0; i < accessRestrictionRadios.length; i++) {
        if (accessRestrictionRadios[i].checked) {
            accessRestriction = i + 1;
        }
    }

    if (accessRestriction == 3) {
        $("#maps").load("snippets/rooms.php",
        {
            "addMap": mapUrl,
            "fileUrl": mapFileUrl,
            "access": accessRestriction,
            "tags": JSON.stringify(newMapsTags)
        }, function() {
            newMapsTags = [];
        });
    } else {
        $("#maps").load("snippets/rooms.php",
        {
            "addMap": mapUrl,
            "fileUrl": mapFileUrl,
            "access": accessRestriction
        }, function() {
            newMapsTags = [];
        });
    }

    tagsElementsShown = false;
}

function addTagsElements() {
    if (!tagsElementsShown) {
        // create tags section
        var container = document.getElementById('tagsArea');
        var tagsArea = document.createElement('div');
        tagsArea.id = 'tagsAreaDiv';
        tagsArea.innerHTML =
            '<div class="input-group mb-3" style="margin-top: 1rem;">' +
                '<input type="text" class="form-control" placeholder="Tag" aria-label="Tag" aria-describedby="buttonTag" id="tagInput">' +
                '<button class="btn btn-primary" type="button" id="buttonTag">Add</button>' +
            '</div>';
        container.appendChild(tagsArea);
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
            newMapsTags = newMapsTags.filter(currentTag => currentTag !== tagToAdd);
            tagsArea.removeChild(tagElement);
        });
    }
}