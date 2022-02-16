window.onload = function () {
    $("#reports").load("snippets/reports.php");
}

function removeReport(id) {
    $("#reports").load("snippets/reports.php", {
        "reportId": id
    });
}