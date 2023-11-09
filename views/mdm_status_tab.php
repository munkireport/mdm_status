<div id="mdm_status-tab"></div>
<h2 data-i18n="mdm_status.title"></h2>

<div id="mdm_status-msg" data-i18n="listing.loading" class="col-lg-12 text-center"></div>

<script>
$(document).on('appReady', function(){
    $.getJSON(appUrl + '/module/mdm_status/get_tab_data/' + serialNumber, function(data){
        
        // Check if we have data
        if( data.length == 0 ){
            $('#mdm_status-msg').text(i18n.t('no_data'));
        } else {
            // Hide loading message
            $('#mdm_status-msg').text('');
            
            // var skipThese = [];
            $.each(data, function(i,d){

                // Generate rows from data
                var rows = ''
                for (var prop in d){
                    if ((d[prop] == '' || d[prop] == null) && d[prop] !== 0) {
                       // Do nothing for empty values to blank them
                    }
                    else if((prop == 'is_user_enrollment') && (d[prop] == 1 || d[prop] == "Yes")){
                       rows = rows + '<tr><th>'+i18n.t('mdm_status.'+prop)+'</th><td><span class="label label-warning">'+i18n.t('yes')+'</span></td></tr>';
                    }
                    else if((prop == 'is_user_enrollment') && (d[prop] == 0 || d[prop] == "No")){
                       rows = rows + '<tr><th>'+i18n.t('mdm_status.'+prop)+'</th><td><span class="label label-success">'+i18n.t('no')+'</span></td></tr>';
                    }

                    else if((prop == 'is_supervised' || prop == 'denies_activation_lock' || prop == 'activation_lock_manageable' || prop == 'is_user_approved' || prop == 'mdm_enrolled_via_dep') && (d[prop] == 1 || d[prop] == "Yes")){
                       rows = rows + '<tr><th>'+i18n.t('mdm_status.'+prop)+'</th><td><span class="label label-success">'+i18n.t('yes')+'</span></td></tr>';
                    }
                    else if((prop == 'is_supervised' || prop == 'denies_activation_lock' || prop == 'activation_lock_manageable' || prop == 'is_user_approved' || prop == 'mdm_enrolled_via_dep') && (d[prop] == 0 || d[prop] == "No")){
                       rows = rows + '<tr><th>'+i18n.t('mdm_status.'+prop)+'</th><td><span class="label label-danger">'+i18n.t('no')+'</span></td></tr>';
                    }

                    else if(prop == 'mdm_enrolled' && d[prop] == "Yes"){
                       rows = rows + '<tr><th>'+i18n.t('mdm_status.'+prop)+'</th><td><span class="label label-warning">'+i18n.t('yes')+" - "+i18n.t('mdm_status.not_uamdm')+'</span></td></tr>';
                    }
                    else if(prop == 'mdm_enrolled' && d[prop] == "Yes (User Approved)"){
                       rows = rows + '<tr><th>'+i18n.t('mdm_status.'+prop)+'</th><td><span class="label label-success">'+i18n.t('yes')+'</span></td></tr>';
                    }
                    else if(prop == 'mdm_enrolled' && d[prop] == "No"){
                       rows = rows + '<tr><th>'+i18n.t('mdm_status.'+prop)+'</th><td><span class="label label-danger">'+i18n.t('no')+'</span></td></tr>';
                    }

                    else if((prop == 'last_mdm_kickstart' || prop == 'last_software_update_kickstart') && d[prop] > 0){
                       var date = new Date(d[prop] * 1000);
                       rows = rows + '<tr><th>'+i18n.t('mdm_status.'+prop)+'</th><td><span title="'+moment(date).fromNow()+'">'+moment(date).format('llll')+'</span></td></tr>';
                    }

                    else {
                        rows = rows + '<tr><th>'+i18n.t('mdm_status.'+prop)+'</th><td>'+d[prop]+'</td></tr>';
                    }
                }
                $('#mdm_status-tab')
                    .append($('<div style="max-width:750px;">')
                        .addClass('table-responsive')
                        .append($('<table>')
                            .addClass('table table-striped table-condensed')
                            .append($('<tbody>')
                                .append(rows))))
            })
        }
    });
});
</script>
