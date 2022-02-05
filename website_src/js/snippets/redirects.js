function setupRedirects() {
    const addRedirectButton = document.getElementById("addRedirection");
    addRedirectButton.addEventListener("click", addRedirection);
}

function destinationMapSelect(destination) {
    const dropdown = document.getElementById("sourceMapsDropdown");
    dropdown.innerHTML = destination;
}

function addRedirection() {
    const mapRedirectUrlInput = document.getElementById("mapRedirectUrl");
    const mapRedirectionUrl = mapRedirectUrlInput.value;

    const dropdown = document.getElementById("sourceMapsDropdown");
    destination = dropdown.innerHTML;

    $("#redirectsContainer").load("snippets/redirects.php", {
        "action": "addRedirection",
        "source": mapRedirectionUrl,
        "destination": destination
    }, setupRedirects);
}

function removeRedirection(source) {
    $("#redirectsContainer").load("snippets/redirects.php", {
        "action": "removeRedirection",
        "source": source
    }, setupRedirects);
}