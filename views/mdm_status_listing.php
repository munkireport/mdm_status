<?php $this->view('partials/head'); ?>

<div class="container">
  <div class="row">
      <div class="col-lg-12">
          <h3><span data-i18n="mdm_status.title"></span> <span id="total-count" class='label label-primary'>…</span></h3>
          <table class="table table-striped table-condensed table-bordered">
            <thead>
              <tr>
                <th data-i18n="listing.computername" data-colname='machine.computer_name'></th>
                <th data-i18n="serial" data-colname='reportdata.serial_number'></th>
                <th data-i18n="username" data-colname='reportdata.long_username'></th>
                <th data-i18n="mdm_status.mdm_enrollment" data-colname='mdm_status.mdm_enrolled'></th>
                <th data-i18n="mdm_status.mdm_enrolled_via_dep_status" data-colname='mdm_status.mdm_enrolled_via_dep'></th>
                <th data-i18n="mdm_status.mdm_server_url" data-colname='mdm_status.mdm_server_url'></th>
                <th data-i18n="mdm_status.last_mdm_kickstart" data-colname='mdm_status.last_mdm_kickstart'></th>
                <th data-i18n="mdm_status.last_software_update_kickstart" data-colname='mdm_status.last_software_update_kickstart'></th>
              </tr>
            </thead>
            <tbody>
                <tr>
                    <td data-i18n="listing.loading" colspan="8" class="dataTables_empty"></td>
                </tr>
            </tbody>
          </table>
    </div> <!-- /span 12 -->
  </div> <!-- /row -->
</div>  <!-- /container -->

<script type="text/javascript">

    $(document).on('appUpdate', function(e){

        var oTable = $('.table').DataTable();
        oTable.ajax.reload();
        return;

    });

    $(document).on('appReady', function(e, lang) {

        // Get modifiers from data attribute
        var mySort = [], // Initial sort
            hideThese = [], // Hidden columns
            col = 0, // Column counter
            runtypes = [], // Array for runtype column
            columnDefs = [{ visible: false, targets: hideThese }]; //Column Definitions

        $('.table th').map(function(){

            columnDefs.push({name: $(this).data('colname'), targets: col, render: $.fn.dataTable.render.text()});

            if($(this).data('sort')){
              mySort.push([col, $(this).data('sort')])
            }

            if($(this).data('hide')){
              hideThese.push(col);
            }

            col++
        });

        oTable = $('.table').dataTable( {
            ajax: {
                url: appUrl + '/datatables/data',
                type: "POST",
                data: function( d ){
                  d.mrColNotEmpty = "mdm_status.id"
                }
            },
            dom: mr.dt.buttonDom,
            buttons: mr.dt.buttons,
            order: mySort,
            columnDefs: columnDefs,
            createdRow: function( nRow, aData, iDataIndex ) {
                // Update name in first column to link
                var name=$('td:eq(0)', nRow).html();
                if(name == ''){name = "No Name"};
                var sn=$('td:eq(1)', nRow).html();
                var link = mr.getClientDetailLink(name, sn);
                $('td:eq(0)', nRow).html(link);

                var dep_enrolled = $('td:eq(4)', nRow).html();
                $('td:eq(4)', nRow).html(function(){
                    if( dep_enrolled == 'Yes'){
                        return '<span class="label label-success">'+i18n.t('mdm_status.mdm_enrolled_via_dep')+'</span>';
                    }
                    return '<span class="label label-danger">'+i18n.t('mdm_status.mdm_not_enrolled_via_dep')+'</span>';
                });

                var mdm_enrolled = $('td:eq(3)', nRow).html();
                $('td:eq(3)', nRow).html(function(){
                    if( mdm_enrolled == 'Yes'){
                        return '<span class="label label-warning">'+i18n.t('mdm_status.not_uamdm')+'</span>';
                                    } else if( mdm_enrolled == 'Yes (User Approved)'){
                                            return '<span class="label label-success">'+i18n.t('mdm_status.user_approved')+'</span>';
                                    }
                    return '<span class="label label-danger">'+i18n.t('mdm_status.not_enrolled')+'</span>';
                });

                // Format last_mdm_kickstart timestamp
                var checkin = parseInt($('td:eq(6)', nRow).html());
                if (checkin > 0){
                    var date = new Date(checkin * 1000);
                    $('td:eq(6)', nRow).html('<span title="'+moment(date).fromNow()+'">'+moment(date).format('llll')+'</span>');
                }

                // Format last_software_update_kickstart timestamp
                var checkin = parseInt($('td:eq(7)', nRow).html());
                if (checkin > 0){
                    var date = new Date(checkin * 1000);
                    $('td:eq(7)', nRow).html('<span title="'+moment(date).fromNow()+'">'+moment(date).format('llll')+'</span>');
                }
            }
        });
    });
</script>

<?php $this->view('partials/foot'); ?>
