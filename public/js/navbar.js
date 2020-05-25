$(document).ready(function () {
    $(document).on("show.bs.dropdown", "#navbarSupportedContent .dropdown", function () {
        $("#navbarDropdown").addClass("open");
    });
    $(document).on("hide.bs.dropdown", "#navbarSupportedContent .dropdown", function () {
        $("#navbarDropdown").removeClass("open");
    });
});