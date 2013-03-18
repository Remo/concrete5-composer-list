$(document).ready(function() {
    $("ul.composer-list-sortable").sortable({
        update: function(event, ui) {
            var queryString = $(this).sortable("serialize");

            $.getJSON(CCM_TOOLS_PATH + '/dashboard/sitemap_update.php', queryString, function(resp) {
                ccm_parseJSON(resp, function() {
                    ccmAlert.hud(resp.message, 2000);
                });
            });

        }
    });

});