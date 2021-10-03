var tagsElementsShown = false;
var newMapsTags = [];

window.onload = function () {
    loadMaps();

    document.getElementById("radioPublic").addEventListener("click", removeTagsElements);
    document.getElementById("radioMembers").addEventListener("click", removeTagsElements);
    document.getElementById("radioMembersTags").addEventListener("click", addTagsElements);
    document.getElementById("addMapButton").addEventListener("click", addMap);
}

function loadMaps() {
    $("#maps").hide();
    $.get("ajax_rooms.php", function (data, status) {
        var contentBox = document.getElementById("maps");
        contentBox.innerHTML = "";

        var addMapParagraph = document.getElementById("add-map-paragraph");
        var mapUrlInput = document.getElementById("mapURL");

        if (data.startRoomSet == false) {
            var alertBox = document.getElementById("alert");
            alertBox.classList.add("alert-danger");
            alertBox.innerText = "The map file for the start room has not been set so far!";
            addMapParagraph.innerText = "Set start room URL";

            mapUrlInput.value = data.startRoom.split("/")[4];
            mapUrlInput.disabled = true;
        } else {
            addMapParagraph.innerText = "Add a new room";
            mapUrlInput.disabled = false;
            mapUrlInput.value = "";
            document.getElementById("mapURLFile").value = "";
        }

        if (data.maps.length > 0) {
            // show maps

            // add heading
            var heading = document.createElement('p');
            heading.classList.add("fs-3");
            heading.textContent = "Enumeration of existing rooms";
            contentBox.appendChild(heading);

            // add maps table
            var table = document.createElement('table');
            table.classList.add("table");
            var tableHead = document.createElement('tr');

            var mapUrlColumn = document.createElement('th');
            mapUrlColumn.textContent = "Map URL";

            var mapFileUrl = document.createElement('th');
            mapFileUrl.textContent = "Map File URL";

            var mapAccessRestriction = document.createElement('th');
            mapAccessRestriction.textContent = "Access restriction";

            var mapActions = document.createElement('th');
            mapActions.textContent = "Actions";

            tableHead.appendChild(mapUrlColumn);
            tableHead.appendChild(mapFileUrl);
            tableHead.appendChild(mapAccessRestriction);
            tableHead.appendChild(mapActions);

            var tableHeadDivider = document.createElement('thead');
            tableHeadDivider.appendChild(tableHead);

            table.appendChild(tableHeadDivider);

            var tableBody = document.createElement('tbody');

            // add maps to table
            data.maps.forEach(element => {
                var tableRow = document.createElement('tr');

                // add domain
                var tableDataDomain = document.createElement('td');
                var tableDomainParagraph = document.createElement('p');
                tableDomainParagraph.classList.add("fw-normal");
                tableDomainParagraph.textContent = element.url;
                tableDataDomain.appendChild(tableDomainParagraph);
                tableRow.appendChild(tableDataDomain);

                // add file url
                var tableDataFile = document.createElement('td');
                var tableDataFileParagraph = document.createElement('p');
                tableDataFileParagraph.classList.add("fw-normal");
                tableDataFileParagraph.textContent = element.file;
                tableDataFile.appendChild(tableDataFileParagraph);
                tableRow.appendChild(tableDataFile);

                // add access restriction
                var tableAccessRestriction = document.createElement('td');
                var tableAccessRestrictionParagraph = document.createElement('p');
                tableAccessRestrictionParagraph.classList.add("fw-normal");
                if (element.policy == 1) {
                    tableAccessRestrictionParagraph.textContent = "Public";
                } else if (element.policy == 2) {
                    tableAccessRestrictionParagraph.textContent = "Members";
                } else if (element.policy == 3) {
                    tableAccessRestrictionParagraph.textContent = "Members with tags";

                    var tags = element.tags;
                    tags.forEach(tag => {
                        var tagPill = document.createElement('div');
                        tagPill.classList.add("badge", "rounded-pill", "bg-primary", "tag");
                        tagPill.textContent = tag;
                        tableAccessRestrictionParagraph.appendChild(tagPill);
                    });
                }
                tableAccessRestriction.appendChild(tableAccessRestrictionParagraph);
                tableRow.appendChild(tableAccessRestriction);


                // add remove action
                var tableRemoveAction = document.createElement('td');
                var tableRemoveActionButton = document.createElement('button');
                tableRemoveActionButton.classList.add("tag", "btn", "btn-danger");
                tableRemoveActionButton.textContent = "Remove";
                tableRemoveActionButton.addEventListener("click", function () { removeMap(element.url); });

                tableRemoveAction.appendChild(tableRemoveActionButton);
                tableRow.appendChild(tableRemoveAction);

                tableBody.appendChild(tableRow);
            });

            // append table
            table.appendChild(tableBody);
            contentBox.append(table);

            // select default map access restriction
            document.getElementById("radioMembers").click();
            $("#maps").show(750, "swing");
        }
    });
}

function removeMap(url) {
    $.get("ajax_rooms.php?removeMap=" + url, function (result, status) {
        // check if removal was successful
        if ("deleteSuccess" in result) {
            var alertBox = document.getElementById("alert");
            alertBox.classList.remove("alert-danger");
            alertBox.classList.remove("alert-success");
            if (result.deleteSuccess == true) {
                alertBox.classList.add("alert-success");
                alertBox.innerText = "The map " + result.mapToRemove.split("/")[4] + " has been removed."
            } else {
                alertBox.classList.add("alert-danger");
                alertBox.innerText = "The map " + result.mapToRemove.split("/")[4] + " could not be removed."
            }
        }
    });
    loadMaps();
}

function addMap() {
    var mapUrl = document.getElementById("mapURL").value;
    var mapFileUrl = document.getElementById("mapURLFile").value;
    var accessRestrictionRadios = document.getElementsByName("radio");
    var accessRestriction = -1;

    for (var i = 0; i < accessRestrictionRadios.length; i++) {
        if (accessRestrictionRadios[i].checked) {
            accessRestriction = i + 1;
        }
    }

    var ajaxUrl = "ajax_rooms.php?addMap=" + encodeURI(mapUrl) +
        "&fileUrl=" + encodeURI(mapFileUrl) +
        "&access=" + encodeURI(accessRestriction);
    if (accessRestriction == 3) {
        // add tags when access is "member with tags"
        var tags = JSON.stringify(newMapsTags);
        ajaxUrl += "&tags=" + encodeURI(tags);
        newMapsTags = [];
    }

    $.get(ajaxUrl, function (data, status) {
        var alertBox = document.getElementById("alert");
        alertBox.classList.remove("alert-danger");
        alertBox.classList.remove("alert-success");
        if (data.storeSuccess) {
            alertBox.classList.add("alert-success");
            alertBox.innerText = "Successfully stored map " + mapUrl;
        } else {
            alertBox.classList.add("alert-danger");
            console.log("error: " + data.errorMessage)
            alertBox.innerText = "Could not store map " + mapUrl + ": " + data.errorMessage;
        }
    });

    loadMaps();
}

function addTagsElements() {
    if (!tagsElementsShown) {
        // create tags section
        var container = document.getElementById('tagsArea');
        var tagsArea = document.createElement('div');
        tagsArea.id = 'tagsAreaDiv';
        tagsArea.innerHTML = '<div class="input-group mb-3" style="margin-top: 1rem;"><input type="text" class="form-control" placeholder="Tag" aria-label="Tag" aria-describedby="buttonTag" id="tagInput"><button class="btn btn-primary" type="button" id="buttonTag">Add</button></div>';
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