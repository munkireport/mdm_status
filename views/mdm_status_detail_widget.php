<div class="col-lg-4">
    <h4><i class="fa fa-gear"></i> <span data-i18n="mdm_status.title"></span></h4>
    <table id="mdm_status-data" class="table"></table>
</div>

<script>
$(document).on('appReady', function(){
    // Get mdm_status data
    $.getJSON( appUrl + '/module/mdm_status/get_data/' + serialNumber, function( data ) {

        // Check if we have data
        if( data.length == 0 ){
            $('#mdm_status-data')
                .append($('<tbody>')
                    .append($('<tr>')
                        .append($('<th>')
                            .text(i18n.t('mdm_status.enrolled_in_mdm')))
                        .append($('<td>')
                            .text(i18n.t('no')))));
        } else {

            $.each(data, function(index, item){

                if (item.mdm_enrolled == "Yes"){
                    item.mdm_enrolled = '<span class="label label-warning">'+i18n.t('yes')+" - "+i18n.t('mdm_status.not_uamdm')+'</span>';
                } else if (item.mdm_enrolled == "Yes (User Approved)"){
                    item.mdm_enrolled = '<span class="label label-success">'+i18n.t('yes')+'</span>';
                } else {
                    item.mdm_enrolled = '<span class="label label-danger">'+i18n.t('no')+'</span>'
                }

                if (item.mdm_enrolled_via_dep == "Yes"){
                    item.mdm_enrolled_via_dep = '<span class="label label-success">'+i18n.t('yes')+'</span>';
                } else {
                    item.mdm_enrolled_via_dep = '<span class="label label-danger">'+i18n.t('no')+'</span>'
                }

                $('#mdm_status-data')
                    .append($('<tbody>')
                        .append($('<tr>')
                            .append($('<th>')
                                .text(i18n.t('mdm_status.enrolled_in_mdm')))
                            .append($('<td>')
                                .html(item.mdm_enrolled)))
                        .append($('<tr>')
                            .append($('<th>')
                                .text(i18n.t('mdm_status.mdm_enrolled_via_dep')))
                            .append($('<td>')
                                .html(item.mdm_enrolled_via_dep)))
                        .append($('<tr>')
                            .append($('<th>')
                                .text(i18n.t('mdm_status.mdm_server_url')))
                            .append($('<td>')
                                .text(item.mdm_server_url))));
            });
        }
    });
});
</script>
