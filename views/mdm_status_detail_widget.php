<div class="col-lg-4">
    <h4 data-i18n="mdm_status.title"></h4>
    <table id="mdm_status-data" class="table"></table>
</div>

<script>
$(document).on('appReady', function(){
	// Get mdm_status data
	$.getJSON( appUrl + '/module/mdm_status/get_data/' + serialNumber, function( data ) {
		$.each(data, function(index, item){
            $('#mdm_status-data')
                .append($('<tr>')
                    .append($('<th>')
                        .text(i18n.t('mdm_status.enrolled_in_mdm')))
                    .append($('<td>')
                        .text(item.mdm_enrolled)))
                .append($('<tr>')
                    .append($('<th>')
                        .text(i18n.t('mdm_status.mdm_enrolled_via_dep')))
                    .append($('<td>')
                        .text(item.mdm_enrolled_via_dep)))
                .append($('<tr>')
                    .append($('<th>')
                        .text(i18n.t('mdm_status.mdm_server_url')))
                    .append($('<td>')
                        .text(item.mdm_server_url)));
		});
    });
});
</script>
