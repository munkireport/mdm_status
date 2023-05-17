<div class="col-lg-4 col-md-6">
    <div class="card" id="user-approved-status-widget">
        <div class="card-heading" data-container="body" title="">
            <i class="fa fa-cogs"></i>
            <span data-i18n="mdm_status.mdm_user_approved"></span>
            <a href="/show/listing/mdm_status/mdm_status" class="pull-right"><i class="fa fa-list"></i></a>
        </div>
		<div class="card-body text-center"></div>
    </div><!-- /card -->
</div><!-- /col -->

<script>
$(document).on('appUpdate', function(e, lang) {

    $.getJSON( appUrl + '/module/mdm_status/get_mdm_legacy_stats', function( data ) {
        
    	if(data.error){
    		//alert(data.error);
    		return;
    	}
		
		var card = $('#user-approved-status-widget div.card-body'),
			baseUrl = appUrl + '/show/listing/mdm_status/mdm_status#';
		card.empty();

		// Set statuses
        if(data.mdm_no || data.non_uamdm){
        	var mdm_no_ints = parseInt(data.mdm_no) + parseInt(data.non_uamdm);
			card.append(' <a href="'+baseUrl+'" class="btn btn-danger"><span class="bigger-150">'+mdm_no_ints+'</span><br>'+i18n.t('mdm_status.not_uamdm')+'</a>');
		}
		if(data.dep_enrolled || data.uamdm){
        	var mdm_ints = parseInt(data.dep_enrolled) + parseInt(data.uamdm);
			card.append(' <a href="'+baseUrl+'Approved" class="btn btn-success"><span class="bigger-150">'+mdm_ints+'</span><br>'+i18n.t('mdm_status.user_approved')+'</a>');
		}
    });
});
</script>
