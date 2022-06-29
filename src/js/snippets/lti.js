window.onload = function () {
    $("#lti").load("snippets/lti.php");
}

function addPlatform() {
    const nameInput = document.getElementById("platformName").value;
    const radioInput = document.querySelector('input[name="radio"]:checked').value;
    const data = {
        "action": "add_platform",
        "name": nameInput,
        "auto_registration": radioInput === "auto"
    }
    $("#lti").load("snippets/lti.php", data);
}

function removePlatform(registrationId) {
    const data = {
        "action": "remove_platform",
        "registration_id": registrationId
    }
    $("#lti").load("snippets/lti.php", data);
}
