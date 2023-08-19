<div class="col-lg-4">
    <h4 data-i18n="mdm_status.title"></h4>
    <table id="mdm_status-data" class="table"></table>
</div>

<script>
$(document).on('appReady', function(){
	// Get mdm_status data
	$.getJSON( appUrl + '/module/mdm_status/get_data/' + serialNumber, function( data ) {
		$.each(data, function(index, item){

            if (item.last_mdm_kickstart > 0){
                var date = new Date(item.last_mdm_kickstart * 1000);
                item.last_mdm_kickstart ='<span title="'+moment(date).fromNow()+'">'+moment(date).format('llll')+'</span>';
            } else {
                item.last_mdm_kickstart = ""
            }

            if (item.last_software_update_kickstart > 0){
                var date = new Date(item.last_software_update_kickstart * 1000);
                item.last_software_update_kickstart ='<span title="'+moment(date).fromNow()+'">'+moment(date).format('llll')+'</span>';
            } else {
                item.last_software_update_kickstart = ""
            }

            $('#mdm_status-data')
                .append($('<tbody>')
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
                            .text(item.mdm_server_url)))
                    .append($('<tr>')
                        .append($('<th>')
                            .text(i18n.t('mdm_status.last_mdm_kickstart')))
                        .append($('<td>')
                            .html(item.last_mdm_kickstart)))
                    .append($('<tr>')
                        .append($('<th>')
                            .text(i18n.t('mdm_status.last_software_update_kickstart')))
                        .append($('<td>')
                            .html(item.last_software_update_kickstart))));
		});
    });
});
</script>
