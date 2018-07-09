<?php include("class_header.php") ?>
<div class="white-area-content">

    <div class="db-header clearfix">
        <div class="page-header-title"> <span class="glyphicon glyphicon-bell"></span> <?php echo lang("ctn_533") ?></div>
        <div class="db-header-extra form-inline"> 

            <a href="<?php echo site_url("classes/view/" . $class->ID) ?>" class="btn btn-primary btn-sm"><?php echo $class->name ?></a>


        </div>
    </div>

</div>


<div class="white-area-content content-separator">

    <div id="calendar">

    </div>

</div>
<?php if ($this->common->has_permissions(array("admin", "class_manager"), $this->user) || ($this->settings->info->teacher_class_manage && $teacher_flag)) : ?>
    <!-- Modal -->
    <div class="modal fade" id="addEventModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-folder-open"></span> <?php echo lang("ctn_534") ?></h4>
                </div>
                <div class="modal-body">
                    <?php echo form_open(site_url("classes/add_class_event/" . $class->ID), array("class" => "form-horizontal")) ?>
                    <div class="form-group">
                        <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_81") ?></label>
                        <div class="col-md-8 ui-front">
                            <input type="text" class="form-control" name="name" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_495") ?></label>
                        <div class="col-md-8 ui-front">
                            <input type="text" class="form-control" name="description">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_535") ?></label>
                        <div class="col-md-8">
                            <input type="text" class="form-control datetimepicker" name="start_date" id="start_date">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_536") ?></label>
                        <div class="col-md-8">
                            <input type="text" class="form-control datetimepicker" name="end_date" id="end_date">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_537") ?></label>
                        <div class="col-md-8">
                            <select name="room" class="form-control">
                                <option value=""><?php echo lang('ctn_1001'); ?></option>
                                <?php foreach ($rooms->result() as $r) : ?>
                                    <option value="<?php echo $r->code ?>" ><?php echo $r->code . ' - ' . $r->branch_name ;?></option>
                                <?php endforeach; ?>
                            </select>
                            <!-- <input type="text" class="form-control" name="room"> -->
                        </div>
                    </div>
                    <div class="form-group ui-front">
                        <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_538") ?></label>
                        <div class="col-md-8">
                            <input type="text" class="form-control jscolor" name="color" value="b453f3">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo lang("ctn_60") ?></button>

                    <input type="submit" class="btn btn-primary" value="<?php echo lang("ctn_539") ?>">
                    <?php echo form_close() ?>

                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Modal -->
<div class="modal fade" id="editEventModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-folder-open"></span> <?php echo lang("ctn_540") ?></h4>
            </div>
            <div class="modal-body form-horizontal">
                <?php if ($this->common->has_permissions(array("admin", "class_manager"), $this->user) || ($this->settings->info->teacher_class_manage && $teacher_flag)) : ?>
                    <?php echo form_open(site_url("classes/update_class_event/" . $class->ID), array("class" => "form-horizontal")) ?>
                <?php endif; ?>
                <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_81") ?></label>
                    <div class="col-md-8 ui-front">
                        <input type="text" class="form-control" name="name" value="<?php echo $class->name ?>" id="event_name">
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
                        <select name="room" class="form-control" id="event_room">
                            <option value=""><?php echo lang('ctn_1001'); ?></option>
                            <?php foreach ($rooms->result() as $r) : ?>
                                <option value="<?php echo $r->code ?>"><?php echo $r->code . ' - ' . $r->branch_name ;?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group ui-front">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_538") ?></label>
                    <div class="col-md-8">
                        <input type="text" class="form-control jscolor" name="color" id="event_color">
                    </div>
                </div>
                <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_541") ?></label>
                    <div class="col-md-8">
                        <input type="checkbox" name="delete" value="1">
                    </div>
                </div>
                <input type="hidden" name="eventid" id="event_id" value="0" />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo lang("ctn_60") ?></button>
                <?php if ($this->common->has_permissions(array("admin", "class_manager"), $this->user) || ($this->settings->info->teacher_class_manage && $teacher_flag)) : ?>
                    <input type="submit" class="btn btn-primary" value="<?php echo lang("ctn_542") ?>">
                    <?php echo form_close() ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<script tye="text/javascript">
    $(document).ready(function () {
        // page is now ready, initialize the calendar...
        var date_last_clicked = null;
        $.fn.modal.Constructor.prototype.enforceFocus = function () {};
        $('.datetimepicker').datetimepicker({
            format: '<?php echo $this->settings->info->calendar_picker_format ?>'
        });

        $('#calendar').fullCalendar({
            eventSources: [
                {
                    events: function (start, end, timezone, callback) {
                        $.ajax({
                            url: global_base_url + 'classes/get_class_events/',
                            dataType: 'json',
                            data: {
                                // our hypothetical feed requires UNIX timestamps
                                start: start.unix(),
                                end: end.unix(),
                                classid: <?php echo $class->ID ?>
                            },
                            success: function (msg) {
                                var events = msg.events;
                                callback(events);
                            }
                        });
                    }
                }
            ],
            eventRender: function (event, element) {
                element.attr('title', event.description + ' Room: ' + event.room);
                element.attr('data-toggle', "tooltip");
                element.attr('data-placement', "bottom");
                element.tooltip();
            },
            timezone: 'UTC',
            dayClick: function (date, jsEvent, view) {
<?php if ($this->common->has_permissions(array("admin", "class_manager"), $this->user) || ($this->settings->info->teacher_class_manage && $teacher_flag)) : ?>
                    var start_date = moment(date).format('<?php echo $this->common->date_php_to_momentjs($this->settings->info->calendar_picker_format) ?>');
                    $('#start_date').val(start_date);
                    $('#end_date').val(start_date);
                    date_last_clicked = $(this);
                    $(this).css('background-color', '#bed7f3');
                    $('#addEventModal').modal();
<?php else : ?>
                    return false;
<?php endif; ?>
            },
            columnFormat: {
                'month': 'ddd'
            },
            timeFormat: 'HH:mm',
            eventClick: function (event, jsEvent, view) {
                if (event.lesson_flag === '1') {
                    return false;
                }
                $('#event_name').val(event.title);
                $('#event_desc').val(event.description);
                $('#event_room').val(event.room);
                $('#event_color').val(event.color.substr(1, event.color.length));
                $('#event_start_date').val(moment(event.start).format('<?php echo $this->common->date_php_to_momentjs($this->settings->info->calendar_picker_format) ?>'));
                if (event.end) {
                    $('#event_end_date').val(moment(event.end).format('<?php echo $this->common->date_php_to_momentjs($this->settings->info->calendar_picker_format) ?>'));
                } else {
                    $('#event_end_date').val(moment(event.start).format('<?php echo $this->common->date_php_to_momentjs($this->settings->info->calendar_picker_format) ?>'));
                }
                $('#event_id').val(event.id);
                $('#project-name').html(event.project_name);
                $('#editEventModal').modal();
                if (event.url) {
                    $('#event_url').attr("href", event.url);
                    return false;
                }
            },
            nextDayThreshold: '01:00:00'
        });

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