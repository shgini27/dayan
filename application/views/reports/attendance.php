<div class="white-area-content">

<div class="db-header clearfix">
    <div class="page-header-title"> <span class="glyphicon glyphicon-list-alt"></span> <?php echo lang("ctn_732") ?></div>
    <div class="db-header-extra form-inline"> 
     
<?php echo form_open(site_url("reports/attendance")) ?>


    <div class="form-group">
      <input type="text" name="start_date" class="input-sm form-control datepicker" value="<?php echo $range1 ?>">
    </div>
    <div class="form-group">
      <input type="text" name="end_date" class="input-sm form-control datepicker" value="<?php echo $range2 ?>">
    </div>
    <div class="form-group">
      <input type="submit" class="btn btn-primary btn-sm" value="<?php echo lang("ctn_756") ?>" >
    </div>
    <?php echo form_close() ?>
      
</div>
</div>


<hr>

<div class="panel panel-default">
<div class="panel-body">
<h4 class="home-label"><?php echo lang("ctn_727") ?></h4>
<canvas id="myChart" class="graph-height"></canvas>
</div>
</div>

</div>
<script type="text/javascript">
var ctx = $("#myChart");
    var data = {
        labels: [<?php foreach($dates as $i) : ?>
        "<?php echo $i['date'] ?>",
        <?php endforeach; ?>],
        datasets: [
            {
                label: "<?php echo lang("ctn_496") ?>",
                fill: false,
                lineTension: 0.2,
                backgroundColor: "rgba(29,210,142,0.5)",
                borderColor: "rgba(29,210,142,0.9)",
                borderCapStyle: 'butt',
                borderDash: [],
                borderDashOffset: 0.0,
                borderJoinStyle: 'miter',
                pointBorderColor: "rgba(75,192,192,1)",
                pointBackgroundColor: "#fff",
                pointBorderWidth: 1,
                pointHoverRadius: 5,
                pointHoverBackgroundColor: "rgba(75,192,192,1)",
                pointHoverBorderColor: "rgba(220,220,220,1)",
                pointHoverBorderWidth: 2,
                pointRadius: 1,
                pointHitRadius: 10,
                data: [<?php foreach($results as $i) : ?>
                    <?php echo $i['present'] ?>,
                <?php endforeach; ?>],
                spanGaps: false,
            },
            {
                label: "<?php echo lang("ctn_498") ?>",
                fill: false,
                lineTension: 0.2,
                backgroundColor: "rgba(156,64,235,0.5)",
                borderColor: "rgba(156,64,235,0.9)",
                borderCapStyle: 'butt',
                borderDash: [],
                borderDashOffset: 0.0,
                borderJoinStyle: 'miter',
                pointBorderColor: "rgba(75,192,192,1)",
                pointBackgroundColor: "#fff",
                pointBorderWidth: 1,
                pointHoverRadius: 5,
                pointHoverBackgroundColor: "rgba(75,192,192,1)",
                pointHoverBorderColor: "rgba(220,220,220,1)",
                pointHoverBorderWidth: 2,
                pointRadius: 1,
                pointHitRadius: 10,
                data: [<?php foreach($results as $i) : ?>
                    <?php echo $i['late'] ?>,
                <?php endforeach; ?>],
                spanGaps: false,
            },
            {
                label: "<?php echo lang("ctn_497") ?>",
                fill: false,
                lineTension: 0.2,
                backgroundColor: "rgba(235,54,34,0.5)",
                borderColor: "rgba(235,54,34,0.9)",
                borderCapStyle: 'butt',
                borderDash: [],
                borderDashOffset: 0.0,
                borderJoinStyle: 'miter',
                pointBorderColor: "rgba(75,192,192,1)",
                pointBackgroundColor: "#fff",
                pointBorderWidth: 1,
                pointHoverRadius: 5,
                pointHoverBackgroundColor: "rgba(75,192,192,1)",
                pointHoverBorderColor: "rgba(220,220,220,1)",
                pointHoverBorderWidth: 2,
                pointRadius: 1,
                pointHitRadius: 10,
                data: [<?php foreach($results as $i) : ?>
                    <?php echo $i['absent'] ?>,
                <?php endforeach; ?>],
                spanGaps: false,
            },
            {
                label: "<?php echo lang("ctn_499") ?>",
                fill: false,
                lineTension: 0.2,
                backgroundColor: "rgba(235,190,107,0.5)",
                borderColor: "rgba(235,190,107,0.9)",
                borderCapStyle: 'butt',
                borderDash: [],
                borderDashOffset: 0.0,
                borderJoinStyle: 'miter',
                pointBorderColor: "rgba(75,192,192,1)",
                pointBackgroundColor: "#fff",
                pointBorderWidth: 1,
                pointHoverRadius: 5,
                pointHoverBackgroundColor: "rgba(75,192,192,1)",
                pointHoverBorderColor: "rgba(220,220,220,1)",
                pointHoverBorderWidth: 2,
                pointRadius: 1,
                pointHitRadius: 10,
                data: [<?php foreach($results as $i) : ?>
                    <?php echo $i['holiday'] ?>,
                <?php endforeach; ?>],
                spanGaps: false,
            },
        ]
    };
    Chart.defaults.global.defaultFontSize = 14;
    var options = { title : { text: "" }};
    var myLineChart = new Chart(ctx, {
        type: 'line',
        data: data,
        options: {
            tooltips: {
                enabled: true,
                mode: 'single',
                callbacks: {
                    title: function(tooltipItems, data) {
                      return 'Amount';
                    },
                    label: function(tooltipItems, data) { 
                        return tooltipItems.yLabel.toLocaleString("en");
                       
                    }
                }
            },
            defaultFontSize: 14,
            responsive: true,
            hover : {
                mode : "single"
            },
            legend : {
                display : true,
                labels : {
                    boxWidth: 25,
                    padding: 15,
                    margin: 10,
                    fontSize: 24,
                    usePointStyle : false
                }
            },
            animation : {
                duration: 2000,
                easing: "easeOutElastic"
            },
            scales : {
                yAxes : [{
                    display: true,
                    title : {
                        fontSize: 18
                    },
                    gridLines : {
                        display : true
                    }
                }],
                xAxes : [{
                    display : true,
                    title : {
                        fontSize: 18
                    },
                    scaleLabel : {
                        display : true
                    },
                    ticks : {
                        display : true
                    },
                    gridLines : {
                        display : true,
                        drawTicks : false,
                        tickMarkLength: 5,
                        zeroLineWidth: 0,
                    }
                }]
            }
        }
    });
</script>