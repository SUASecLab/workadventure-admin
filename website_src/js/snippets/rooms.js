window.onload = function () {
    $("#rooms").load("/snippets/rooms.php", function() {
        $("#mapContainer").load("/snippets/maps.php", setupMaps);
        $("#redirectsContainer").load("/snippets/redirects.php", setupRedirects);

        const mapsTabButton = document.getElementById("maps-tab");
        mapsTabButton.addEventListener("click", function() {
            $("#mapContainer").load("/snippets/maps.php", setupMaps);
        });
        const redirectsTabButton = document.getElementById("redirects-tab");
        redirectsTabButton.addEventListener("click", function() {
            $("#redirectsContainer").load("/snippets/redirects.php", setupRedirects);
        });
    });
}