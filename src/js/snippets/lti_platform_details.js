window.onload = function () {
    const queryString = window.location.search;
    const parameters = new URLSearchParams(queryString);
    const registration_id = parameters.get('registration_id');

    $("#lti_platform_details").load("snippets/lti_platform_details.php", {
        "registration_id": registration_id,
    }, function () {
        const navbarMain = document.getElementById("navbarMain");
        const divider = document.createElement('a')
        navbarMain.appendChild(divider);
        divider.outerHTML = '<a class="navbar-brand" href="#">/</a>';

        const lti = document.createElement('a');
        navbarMain.appendChild(lti);
        lti.outerHTML = '<a class="navbar-brand" href="lti">LTI</a>';
    });
}

function removeDeployment(registrationId, deploymentId) {
    $("#lti_platform_details").load("snippets/lti_platform_details.php", {
        "action": "remove_deployment",
        "registration_id": registrationId,
        "deployment_id": deploymentId
    });
}

function addDeployment(registrationId) {
    const name = document.getElementById("deploymentName").value;
    const deploymentId = document.getElementById("deploymentId").value;
    $("#lti_platform_details").load("snippets/lti_platform_details.php", {
        "action": "add_deployment",
        "registration_id": registrationId,
        "name": name,
        "deployment_id": deploymentId
    });
}

function startEditMode() {
    document.getElementById("startEditingButton").style.display = "none";
    document.getElementById("editActions").style.display = "block";
    document.getElementById("name").removeAttribute("readonly");
    document.getElementById("platformId").removeAttribute("readonly");
    document.getElementById("clientId").removeAttribute("readonly");
    document.getElementById("authRequestUrl").removeAttribute("readonly");
    document.getElementById("accessTokenUrl").removeAttribute("readonly");
    document.getElementById("keySetUrl").removeAttribute("readonly");
}

function stopEditMode() {
    document.getElementById("startEditingButton").style.display = "inline-block";
    document.getElementById("editActions").style.display = "none";
    document.getElementById("name").setAttribute("readonly", true);
    document.getElementById("platformId").setAttribute("readonly", true);
    document.getElementById("clientId").setAttribute("readonly", true);
    document.getElementById("authRequestUrl").setAttribute("readonly", true);
    document.getElementById("accessTokenUrl").setAttribute("readonly", true);
    document.getElementById("keySetUrl").setAttribute("readonly", true);
}

function saveEdits(registrationId) {
    const name = document.getElementById("name").value;
    const platformId = document.getElementById("platformId").value;
    const clientId = document.getElementById("clientId").value;
    const authRequestUrl = document.getElementById("authRequestUrl").value;
    const accessTokenUrl = document.getElementById("accessTokenUrl").value;
    const keySetUrl = document.getElementById("keySetUrl").value;
    $("#lti_platform_details").load("snippets/lti_platform_details.php", {
        "action": "edit_registration",
        "registration_id": registrationId,
        "name": name,
        "platform_id": platformId,
        "client_id": clientId,
        "authentication_request_url": authRequestUrl,
        "access_token_url": accessTokenUrl,
        "key_set_url": keySetUrl
    });
}

function discardEdits(registrationId) {
    stopEditMode();
    $("#lti_platform_details").load("snippets/lti_platform_details.php", {
        "action": "discard_edit_platform",
        "registration_id": registrationId,
    });
}