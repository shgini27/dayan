<style type="text/css">
    .fc td, .fc th { font-weight: 500 !important; }
</style>

<div class="white-area-content">
    <?php if ($this->common->has_permissions(array("admin", "class_manager"), $this->user)) : ?>
    <div class="row">
        <div class="col-md-4">
            <div class="dashboard-window clearfix" style="background: #9e3c9b; border-left: 5px solid #82227f;">
                <div class="d-w-icon">
                    <span class="glyphicon glyphicon-bell giant-white-icon"></span>
                </div>
                <div class="d-w-text">
                    <span class="d-w-num"><?php echo $todays_student_count ?></span><br /><?php echo lang("ctn_1011") ?>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="dashboard-window clearfix" style="background: #4fd9cd; border-left: 5px solid #3abbb0;">
                <div class="d-w-icon">
                    <span class="glyphicon glyphicon-bell giant-white-icon"></span>
                </div>
                <div class="d-w-text">
                    <span class="d-w-num"><?php echo $todays_missing_student_count ?></span><br /><?php echo lang("ctn_1012") ?>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="dashboard-window clearfix" style="background: #924fd9; border-left: 5px solid #7435b7;">
                <div class="d-w-icon">
                    <span class="glyphicon glyphicon-bell giant-white-icon"></span>
                </div>
                <div class="d-w-text">
                    <span class="d-w-num"><?php echo (intval($todays_student_count) - intval($todays_missing_student_count)) ?></span><br /><?php echo lang("ctn_1013") ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">

        <div class="col-md-3">
            <div class="dashboard-window clearfix" style="background: #62acec; border-left: 5px solid #5798d1;">
                <div class="d-w-icon">
                    <span class="glyphicon glyphicon-send giant-white-icon"></span>
                </div>
                <div class="d-w-text">
                    <span class="d-w-num"><?php echo number_format($stats->students) ?></span><br /><?php echo lang("ctn_481") ?>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="dashboard-window clearfix" style="background: #5cb85c; border-left: 5px solid #4f9f4f;">
                <div class="d-w-icon">
                    <span class="glyphicon glyphicon-wrench giant-white-icon"></span>
                </div>
                <div class="d-w-text">
                    <span class="d-w-num"><?php echo number_format($stats->teachers) ?></span><br /><?php echo lang("ctn_559") ?>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="dashboard-window clearfix" style="background: #f0ad4e; border-left: 5px solid #d89b45;">
                <div class="d-w-icon">
                    <span class="glyphicon glyphicon-folder-close giant-white-icon"></span>
                </div>
                <div class="d-w-text">
                    <span class="d-w-num"><?php echo number_format($stats->classes) ?></span><br /><?php echo lang("ctn_471") ?>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="dashboard-window clearfix" style="background: #d9534f; border-left: 5px solid #b94643;">
                <div class="d-w-icon">
                    <span class="glyphicon glyphicon-user giant-white-icon"></span>
                </div>
                <div class="d-w-text">
                    <span class="d-w-num"><?php echo number_format($online_count) ?></span><br /><?php echo lang("ctn_139") ?>
                </div>
            </div>
        </div>

    </div>
    <?php endif; ?>
    <hr>

    <div class="row">
        <div class="col-md-9">

            <?php if ($this->settings->info->finance_section && $this->common->has_permissions(array("admin", "finance_manager", "finance_viewer"), $this->user)) : ?>
                <!-- <div class="block-area align-center">
                <h4 class="home-label"><?php echo lang("ctn_606") ?></h4>
                <div class="finance-blob">
                <p class="finance-blob-unit"><?php echo $this->settings->info->fp_currency_symbol ?><span id="num1"><?php echo $total_revenue ?></span></p>
                <?php echo lang("ctn_610") ?>
                </div>
                <div class="finance-blob">
                <p class="finance-blob-unit"><?php echo $this->settings->info->fp_currency_symbol ?><span id="num2"><?php echo $total_expense ?></span></p>
                <?php echo lang("ctn_611") ?>
                </div>
                <div class="finance-blob">
                <p class="finance-blob-unit"><?php echo $this->settings->info->fp_currency_symbol ?><span id="num3"><?php echo $profit ?></span></p>
                <?php echo lang("ctn_612") ?>
                </div>
                <canvas id="myChart" class="graph-height"></canvas>
                </div> -->
            <?php endif; ?>

            <?php if ($this->settings->info->classes_section && $this->common->has_permissions(array("admin", "class_manager", "class_viewer"), $this->user)) : ?>
                <div class="block-area align-center content-separator">
                    <h4 class="home-label"><?php echo lang("ctn_613") ?></h4>
                    <div id="calendar">
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($this->settings->info->invoices_section && $this->common->has_permissions(array("parent"), $this->user)) : ?>
                <div class="content-separator block-area">
                    <h4 class="home-label"><?php echo lang("ctn_861") ?></h4>
                    <div class="table-responsive">
                        <table class="table small-text table-bordered table-striped table-hover">
                            <tr class="table-header"><td><?php echo lang("ctn_339") ?></td><td><?php echo lang("ctn_52") ?></td></tr>
                            <?php foreach ($children->result() as $r) : ?>

                                <tr><td><?php echo $this->common->get_user_display(array("username" => $r->username, "avatar" => $r->avatar, "online_timestamp" => $r->online_timestamp, "first_name" => $r->first_name, "last_name" => $r->last_name)) ?></td><td><a href="<?php echo site_url("students/view/" . $r->studentid) ?>" class="btn btn-primary btn-xs"><?php echo lang("ctn_552") ?></a></td></tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                </div>
            <?php endif; ?>


            <?php if ($this->settings->info->invoices_section && $this->common->has_permissions(array("admin", "invoice_manager", "invoice_viewer"), $this->user)) : ?>
                <!-- <div class="content-separator block-area">
                <h4 class="home-label"><?php echo lang("ctn_614") ?></h4>
                <div class="table-responsive">
                <table class="table small-text table-bordered table-striped table-hover">
                <tr class="table-header"><td><?php echo lang("ctn_615") ?></td><td width="60"><?php echo lang("ctn_616") ?></td><td width="60"><?php echo lang("ctn_617") ?></td><td><?php echo lang("ctn_618") ?></td><td><?php echo lang("ctn_510") ?></td></tr>
                <?php foreach ($invoices->result() as $r) : ?>
                    <?php
                    if ($r->status == 1) {
                        $status = "<label class='label label-danger'>" . lang("ctn_619") . "</label>";
                    } elseif ($r->status == 2) {
                        $status = "<label class='label label-success'>" . lang("ctn_620") . "</label>";
                    } elseif ($r->status == 3) {
                        $status = "<label class='label label-default'>" . lang("ctn_621") . "</label>";
                    } elseif ($r->status == 4) {
                        $status = "<label class='label label-warning'>" . lang("ctn_922") . "</label>";
                    }
                    ?>
                    <tr><td><a href="<?php echo site_url("invoices/view/" . $r->ID . "/" . $r->hash) ?>"><?php echo $r->title ?></a></td><td><?php echo $r->symbol ?><?php echo number_format($r->total, 2) ?></td><td><?php echo $status ?></td><td> <?php echo $this->common->get_user_display(array("username" => $r->client_username, "avatar" => $r->client_avatar, "online_timestamp" => $r->client_online_timestamp)) ?></td><td><?php echo date($this->settings->info->date_format, $r->due_date) ?></td></tr>
                <?php endforeach; ?>
                </table>
                </div>
                </div> -->
            <?php endif; ?>

            <div class="block-area">
                <?php echo lang("ctn_326") ?> <b><?php echo date($this->settings->info->date_format, $this->user->info->online_timestamp); ?></b>
            </div>

        </div>
        <div class="col-md-3">

            <?php if ($this->settings->info->announcements_section) : ?>
                <div class="panel panel-default">
                    <div class="panel-body">
                        <h4 class="home-label"><?php echo lang("ctn_622") ?></h4>


                        <table class="table">
                            <?php foreach ($news->result() as $r) : ?>
                                <tr><td width="30"><?php echo $this->common->get_user_display(array("username" => $r->username, "avatar" => $r->avatar, "online_timestamp" => $r->online_timestamp)) ?></td><td><p class="small-text"><a href="<?php echo site_url("announcements/view/" . $r->ID) ?>"><?php echo $r->title ?></a></p>
                                    </td></tr>
                            <?php endforeach; ?>
                        </table>

                    </div>
                </div>
            <?php endif; ?>

            <!-- Birthdate Panel -->
            <?php if ($this->settings->info->classes_section && $this->common->has_permissions(array("admin", "class_manager", "class_viewer"), $this->user)) : ?>
                <div class="panel panel-default">
                    <div class="panel-body">
                        <h4 class="home-label"><?php echo lang("ctn_1010") ?></h4>

                        <table class="table">
                            <?php foreach ($birthdays as $r) : ?>
                                <tr><td width="30"><?php echo $this->common->get_user_display(array("username" => $r->username, "avatar" => $r->avatar, "online_timestamp" => $r->online_timestamp)) ?>
                                    </td><td><p class="small-text"><?php echo $r->first_name . " " . $r->last_name . ' (' . date_format(date_create($r->birth_date), "d/m/Y") . ')' ?></p></td></tr>
                            <?php endforeach; ?>
                        </table>

                    </div>
                </div>
            <?php endif; ?>
            <!-- End of Birthdate Panel -->

            <?php if ($this->settings->info->classes_section && $this->common->has_permissions(array("admin", "class_manager", "class_viewer"), $this->user)) : ?>
                <div class="panel panel-default">
                    <div class="panel-body">
                        <h4 class="home-label"><?php echo lang("ctn_623") ?></h4>


                        <table class="table">
                            <?php foreach ($assignments->result() as $r) : ?>
                                <tr><td><p class="small-text"><a href="<?php echo site_url("classes/view_assignment/" . $r->ID) ?>"><?php echo $r->title ?></a></p>
                                        <p class="small-text"><?php echo lang("ctn_624") ?> <strong><?php echo date($this->settings->info->date_format, $r->due_date) ?></strong></p>
                                    </td></tr>
                            <?php endforeach; ?>
                        </table>

                    </div>
                </div>
            <?php endif; ?>


            <?php if ($this->settings->info->classes_section && $this->common->has_permissions(array("admin", "class_manager", "class_viewer"), $this->user)) : ?>
                <div class="panel panel-default">
                    <div class="panel-body">
                        <h4 class="home-label"><?php echo lang("ctn_625") ?></h4>


                        <table class="table">
                            <?php foreach ($classes->result() as $r) : ?>
                                <tr><td><p class="small-text"><a href="<?php echo site_url("classes/view/" . $r->ID) ?>"><?php echo $r->name ?></a></p>
                                        <p class="small-text"><label class="label label-default"><strong><?php echo $r->subject ?></strong></label></p>
                                    </td></tr>
                            <?php endforeach; ?>
                        </table>

                    </div>
                </div>
            <?php endif; ?>


        </div>
    </div>

</div>
<div class="modal fade" id="editEventModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-folder-open"></span> <?php echo lang("ctn_626") ?></h4>
            </div>
            <div class="modal-body form-horizontal">
                <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_81") ?></label>
                    <div class="col-md-8 ui-front">
                        <input type="text" class="form-control" name="name" value="" id="event_name">
                    </div>
                </div>
                <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_495") ?></label>
                    <div class="col-md-8 ui-front">
                        <input type="text" class="form-control" name="description" id="event_desc">
                    </div>
                </div>
                <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_535") ?></label>
                    <div class="col-md-8">
                        <input type="text" class="form-control datetimepicker" name="start_date" id="event_start_date">
                    </div>
                </div>
                <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_536") ?></label>
                    <div class="col-md-8">
                        <input type="text" class="form-control datetimepicker" name="end_date" id="event_end_date">
                    </div>
                </div>
                <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_537") ?></label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="room" id="event_room">
                    </div>
                </div>
                <div class="form-group ui-front">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_538") ?></label>
                    <div class="col-md-8">
                        <input type="text" class="form-control jscolor" name="color" id="event_color">
                    </div>
                </div>
                <div class="form-group ui-front">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_532") ?></label>
                    <div class="col-md-8" id='class_area'>

                    </div>
                </div>
                <input type="hidden" name="eventid" id="event_id" value="0" />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo lang("ctn_60") ?></button>  
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    /*var ctx = $("#myChart");
     var data = {
     labels: ["<?php echo lang("ctn_627") ?>", "<?php echo lang("ctn_628") ?>", "<?php echo lang("ctn_629") ?>", "<?php echo lang("ctn_630") ?>", "<?php echo lang("ctn_631") ?>", "<?php echo lang("ctn_632") ?>", "<?php echo lang("ctn_633") ?>", "<?php echo lang("ctn_634") ?>", "<?php echo lang("ctn_635") ?>", "<?php echo lang("ctn_636") ?>", "<?php echo lang("ctn_637") ?>", "<?php echo lang("ctn_638") ?>"],
     datasets: [
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
     data: [<?php foreach ($expense as $i) : ?>
    <?php echo $i['count'] ?>,
<?php endforeach; ?>],
     spanGaps: false,
     },
     {
     label: "<?php echo lang("ctn_640") ?>",
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
     data: [<?php foreach ($income as $i) : ?>
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
     $(document).ready(function() {
     var comma_separator_number_step = $.animateNumber.numberStepFactories.separator(',');
     $('#num1').animateNumber(
     {
     number: <?php echo $total_revenue ?>,
     easing: 'easeInQuad', // require jquery.easing
     numberStep: comma_separator_number_step
     },
     1500
     );
     $('#num2').animateNumber(
     {
     number: <?php echo $total_expense ?>,
     easing: 'easeInQuad', // require jquery.easing
     numberStep: comma_separator_number_step
     },
     1500
     );
     $('#num3').animateNumber(
     {
     number: <?php echo $profit ?>,
     easing: 'easeInQuad', // require jquery.easing
     numberStep: comma_separator_number_step
     },
     1500
     );
     });*/
</script>
<script tye="text/javascript">
    $(document).ready(function () {
        // page is now ready, initialize the calendar...
        var date_last_clicked = null;
        $.fn.modal.Constructor.prototype.enforceFocus = function () {};
        $('.datetimepicker').datetimepicker({
            format: '<?php echo $this->settings->info->calendar_picker_format ?>'
        });

        $(document).ready(function () {

            $('#calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'listDay,listWeek,month'
                },

                // customize the button names,
                // otherwise they'd all just say "list"
                views: {
                    listDay: {buttonText: 'list day'},
                    listWeek: {buttonText: 'list week'}
                },

                defaultView: 'listWeek',
                //defaultDate: '2018-06-08',
                navLinks: true, // can click day/week names to navigate views
                editable: true,
                eventLimit: true, // allow "more" link when too many events
                eventSources: [
<?php foreach ($classes_events as $class) : ?>
                        {
                            events: function (start, end, timezone, callback) {
                                $.ajax({
                                    url: global_base_url + 'classes/get_class_events/',
                                    dataType: 'json',
                                    data: {
                                        // our hypothetical feed requires UNIX timestamps
                                        start: start.unix(),
                                        end: end.unix(),
                                        classid: <?php echo $class['classid']; ?>
                                    },
                                    success: function (msg) {
                                        var events = msg.events;
                                        callback(events);
                                    }
                                });
                            }
                        },
<?php endforeach; ?>

                ],
                eventRender: function (event, element) {
                    element.attr('title', event.description + ' Room: ' + event.room);
                    element.attr('data-toggle', "tooltip");
                    element.attr('data-placement', "bottom");
                    element.tooltip();
                },
                timeFormat: 'HH:mm',
            });

        });

        /*$('#calendar').fullCalendar({
         
         eventSources: [
<?php foreach ($classes_events as $class) : ?>
             {
             events: function(start, end, timezone, callback) {
             $.ajax({
             url: global_base_url + 'classes/get_class_events/',
             dataType: 'json',
             data: {
             // our hypothetical feed requires UNIX timestamps
             start: start.unix(),
             end: end.unix(),
             classid : <?php echo $class['classid'] ?>
             },
             success: function(msg) {
             var events = msg.events;
             callback(events);
             }
             });
             }
             },
<?php endforeach; ?>
         
         ],
         eventRender: function(event, element) {
         element.attr('title', event.room);
         element.attr('data-toggle', "tooltip");
         element.attr('data-placement', "bottom");
         element.tooltip();
         },
         timezone: 'UTC',
         dayClick: function(date, jsEvent, view) {
         return false;
         },
         columnFormat: {
         'month' : 'ddd'
         },
         timeFormat: 'HH:mm',
         eventClick: function(event, jsEvent, view) {
         $('#event_name').val(event.title);
         $('#event_desc').val(event.description);
         $('#event_room').val(event.room);
         $('#event_color').val(event.color);
         $('#event_start_date').val(moment(event.start).format('<?php echo $this->common->date_php_to_momentjs($this->settings->info->calendar_picker_format) ?>'));
         if(event.end) {
         $('#event_end_date').val(moment(event.end).format('<?php echo $this->common->date_php_to_momentjs($this->settings->info->calendar_picker_format) ?>'));
         } else {
         $('#event_end_date').val(moment(event.start).format('<?php echo $this->common->date_php_to_momentjs($this->settings->info->calendar_picker_format) ?>'));
         }
         $('#event_id').val(event.id);
         $('#class_area').html("<a href='<?php echo site_url("classes/view/") ?>"+event.classid+"'>" + event.class_name + "</a>");
         $('#editEventModal').modal();
         if (event.url) {
         $('#event_url').attr("href", event.url);
         return false;
         }
         },
         nextDayThreshold : '01:00:00'
         })*/

        $('#addEventModal').on('hidden.bs.modal', function () {
            // do something…
            date_last_clicked.css('background-color', '#ffffff');
        });

    });
</script>
<?php if (isset($site['fullcalendar_lang']) && !empty($site['fullcalendar_lang'])) : ?>
    <script src="<?php echo base_url() . $site['fullcalendar_lang'] ?>"></script>
<?php endif; ?>
<?php if (isset($site['datetimepicker']) && !empty($site['datetimepicker'])) : ?>
    <script type="text/javascript">
        jQuery.datetimepicker.setLocale('<?php echo $site['datetimepicker'] ?>');
    </script>
<?php endif; ?>