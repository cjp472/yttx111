<script type="text/javascript">
    var chart1;
    $(document).ready(function() {
        chart1 = new Highcharts.Chart({
            chart: {
                renderTo: 'container_total',
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false
            },
            title: {
                text: '各推广类型占比(%)'
            },
            tooltip: {
                formatter: function() {
                    return '<b>'+ this.point.name +'</b>: '+ this.y +' %';
                }
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        color: '#000000',
                        connectorColor: '#000000',
                        formatter: function() {
                            return '<b>'+ this.point.name +'</b>: '+ this.y +' %';
                        }
                    }
                }
            },
            series: [{
                type: 'pie',
                colorByPoint: true,
                name: 'generalize log',
                data: [
                    <?php
                    for($i = 0; $i<count($ginfo);$i++){
                    echo '10,';
                    }
                    ?>
                ]
            }]
        });
    });
</script>
<div id="container_total" style="width: 700px; height: 400px; margin: 0 auto"></div>