window.onload = function () {
    $("#lti_role_mapping").load("snippets/lti_role_mapping.php");
}

function addMapping() {
    const role = "http://purl.imsglobal.org/vocab/lis/v2/" + document.getElementById("role").value;
    const tag = document.getElementById("tag").value;
    const data = {
        "action": "add_role_mapping",
        "role": role,
        "tag": tag
    }
    $("#lti_role_mapping").load("snippets/lti_role_mapping.php", data);
}

function removeRoleMapping(role) {
    const data = {
        "action": "remove_role_mapping",
        "role": role
    }
    $("#lti_role_mapping").load("snippets/lti_role_mapping.php", data);
}
