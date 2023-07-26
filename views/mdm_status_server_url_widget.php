<div class="col-md-4">
    <div class="card">
        <div class="card-header" data-container="body">
            <i class="fa fa-cutlery"></i>
            <span data-i18n="mdm_status.mdm_server_url"></span>
            <a href="/show/listing/mdm_status/mdm_status" class="pull-right"><i class="fa fa-list"></i></a>
        </div>
        <div id="ip-card" class="card-body text-center">
            <svg id="mdm_status-mdm-server-url" style="width:100%; height: 300px"></svg>
        </div>
    </div><!-- /card -->
</div><!-- /col -->

<script>
    $(document).on('appReady', function() {

        function isnotzero(point)
        {
            return point.count > 0;
        }

        var url = appUrl + '/module/mdm_status/get_mdm_server_url'
        var chart;
        d3.json(url, function(err, data){

            var height = 300;
            var width = 350;

               // Filter data
            data = data.filter(isnotzero);

            nv.addGraph(function() {
                var chart = nv.models.pieChart()
                    .x(function(d) { return d.key })
                    .y(function(d) { return d.count })
                    .showLabels(false);

                chart.title("" + d3.sum(data, function(d){
                    return d.count;
                }));

                chart.pie.donut(true);

                d3.select("#mdm_status-mdm-server-url")
                    .datum(data)
                    .transition().duration(1200)
                    .style('height', height)
                    .call(chart);

                // Adjust title (count) depending on active slices
                chart.dispatch.on('stateChange.legend', function (newState) {
                    var disabled = newState.disabled;
                    chart.title("" + d3.sum(data, function(d, i){
                        return d.count * !disabled[i];
                    }));
                });

                return chart;

            });
        });
    });
</script>
