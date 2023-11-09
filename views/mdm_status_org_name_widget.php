<div class="col-md-4">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-building-o"></i>
                <span data-i18n="mdm_status.org_name"></span>
                <list-link data-url="/show/listing/mdm_status/mdm_status"></list-link>
            </h3>
        </div>
        <div id="ip-panel" class="panel-body text-center">
            <svg id="mdm_status-org-name" style="width:100%; height: 300px"></svg>
        </div>
    </div><!-- /panel -->
</div><!-- /col -->

<script>
    $(document).on('appReady', function() {

        function isnotzero(point)
        {
            return point.count > 0;
        }

        var url = appUrl + '/module/mdm_status/get_org_name'
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

                d3.select("#mdm_status-org-name")
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
