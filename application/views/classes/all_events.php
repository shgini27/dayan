<div class="white-area-content">

    <div class="db-header clearfix">
        <div class="page-header-title"> <span class="glyphicon glyphicon-bell"></span> 
            <?php echo lang("ctn_1008") ?></div>
        <div class="db-header-extra form-inline"> 

        </div>
    </div>
    <div id="all_calendar">

    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        
        $('#all_calendar').fullCalendar({
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
<?php foreach ($classes as $class) : ?>
                    {
                        events: function (start, end, timezone, callback) {
                            $.ajax({
                                url: global_base_url + 'classes/get_class_events/',
                                dataType: 'json',
                                data: {
                                    // our hypothetical feed requires UNIX timestamps
                                    start: start.unix(),
                                    end: end.unix(),
                                    classid: <?php echo $class->ID; ?>
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

</script>