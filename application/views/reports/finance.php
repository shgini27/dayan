<div class="white-area-content">

<div class="db-header clearfix">
    <div class="page-header-title"> <span class="glyphicon glyphicon-list-alt"></span> <?php echo lang("ctn_732") ?></div>
    <div class="db-header-extra form-inline"> 
     
<?php echo form_open(site_url("reports/finance")) ?>


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
<h4 class="home-label"><?php echo lang("ctn_726") ?></h4>
<div class="finance-blob">
<p class="finance-blob-unit">$<span id="num1"><?php echo number_format($total_revenue) ?></span></p>
<?php echo lang("ctn_610") ?>
</div>
<div class="finance-blob">
<p class="finance-blob-unit">$<span id="num2"><?php echo number_format($total_expense) ?></span></p>
<?php echo lang("ctn_611") ?>
</div>
<div class="finance-blob">
<p class="finance-blob-unit">$<span id="num3"><?php echo number_format($total_revenue + $total_expense) ?></span></p>
<?php echo lang("ctn_612") ?>
</div>
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
            <?php if($results2) : ?>
            {
                label: "<?php echo lang("ctn_639") ?>",
                fill: true,
                lineTension: 0.2,
                backgroundColor: "rgba(32,113,210,0.4)",
                borderColor: "rgba(32,113,210,0.9)",
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
                data: [<?php foreach($results2 as $i) : ?>
                    <?php echo $i['count'] ?>,
                <?php endforeach; ?>],
                spanGaps: false,
            },
            <?php endif; ?>
            {
                label: "<?php echo lang("ctn_757") ?>",
                fill: true,
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
                    <?php echo $i['count'] ?>,
                <?php endforeach; ?>],
                spanGaps: false,
            },
        ]
    };
    Chart.defaults.global.defaultFontFamily = "'Open Sans'";
    Chart.defaults.global.defaultFontSize = 8;
    var options = { title : { text: "" }};
    var myLineChart = new Chart(ctx, {
        type: 'line',
        data: data,
        options: {
            tooltips: {
                enabled: true,
                mode: 'single',
                callbacks: {
                    label: function(tooltipItems, data) { 
                        return "<?php echo $this->settings->info->fp_currency_symbol ?>" + tooltipItems.yLabel.toLocaleString("en");
                       
                    }
                }
            },
            defaultFontSize: 8,
            responsive: true,
            hover : {
                mode : "single"
            },
            legend : {
                display : false,
                labels : {
                    boxWidth: 15,
                    padding: 10,
                    fontSize: 11,
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
                        fontSize: 11
                    },
                    gridLines : {
                        display : true
                    }
                }],
                xAxes : [{
                    display : true,
                    scaleLabel : {
                        display : false
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