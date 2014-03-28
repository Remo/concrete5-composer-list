$(document).ready(function() {

    var tableSortHelper = function(e, ui) {
        ui.children().each(function() {
            $(this).width($(this).width());
        });
        return ui;
    };

    $(".composer-list-sortable > tbody").sortable({
        helper: tableSortHelper,
        update: function(event, ui) {
            var queryString = $(this).sortable("serialize");

            $.getJSON(CCM_TOOLS_PATH + '/dashboard/sitemap_update.php', queryString, function(resp) {
                ccm_parseJSON(resp, function() {
                    ccmAlert.hud(resp.message, 5000);
                });
            });

        }
    }).disableSelection();

});