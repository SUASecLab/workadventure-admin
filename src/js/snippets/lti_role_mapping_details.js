window.onload = function () {
    const queryString = window.location.search;
    const parameters = new URLSearchParams(queryString);
    const role = parameters.get('role');

    $("#lti_role_mapping_details").load("snippets/lti_role_mapping_details.php", {
        "role": role,
    }, function () {
        const navbarMain = document.getElementById("navbarMain");
        const divider = document.createElement('a')
        navbarMain.appendChild(divider);
        divider.outerHTML = '<a class="navbar-brand" href="#">/</a>';

        const lti = document.createElement('a');
        navbarMain.appendChild(lti);
        lti.outerHTML = '<a class="navbar-brand" href="lti_role_mapping">LTI Role Mapping</a>';
    });
}

function addTag(role) {
    const tag = document.getElementById("newTag").value;
    const data = {
        "action": "add_tag",
        "role": role,
        "tag": tag
    }
    $("#lti_role_mapping_details").load("snippets/lti_role_mapping_details.php", data);
}

function removeTag(role, tag) {
    const data = {
        "action": "remove_tag",
        "role": role,
        "tag": tag
    }
    $("#lti_role_mapping_details").load("snippets/lti_role_mapping_details.php", data);
}