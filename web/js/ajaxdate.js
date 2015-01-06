function updateDate() {
    $.get("http://localhost/blogea/web/app_dev.php/api/date", function(data) {
        $("#ajaxdate").html(data.date);
    });

    // TODO setTimeout(updateDate, 1000);
}

updateDate();
