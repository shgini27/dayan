<div class="white-area-content">

<div class="db-header clearfix">
    <div class="page-header-title"> <span class="glyphicon glyphicon-bell"></span> <?php echo lang("ctn_533") ?></div>
    <div class="db-header-extra form-inline"> 

</div>
</div>


<div id="calendar">

</div>

</div>

<!-- Modal -->
<div class="modal fade" id="editEventModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-folder-open"></span> <?php echo lang("ctn_540") ?></h4>
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
<script tye="text/javascript">
$(document).ready(function() {
    // page is now ready, initialize the calendar...
    var date_last_clicked = null;
    $.fn.modal.Constructor.prototype.enforceFocus = function() {};
    $('.datetimepicker').datetimepicker({
      format : '<?php echo $this->settings->info->calendar_picker_format ?>'
    });

    $('#calendar').fullCalendar({
      eventSources: [
           <?php foreach($classes as $class) : ?>
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
          $('#class_area').html("<a href='<?php echo site_url("classes/view/") ?>"+event.classid+"'>" + event.class_name + "</a>");
          $('#event_id').val(event.id);
          $('#project-name').html(event.project_name);
          $('#editEventModal').modal();
          if (event.url) {
              $('#event_url').attr("href", event.url);
              return false;
          }
       },
       nextDayThreshold : '01:00:00'
    })

    $('#addEventModal').on('hidden.bs.modal', function () {
        // do something…
        date_last_clicked.css('background-color', '#ffffff');
    });

});
</script>
<?php if(isset($site['fullcalendar_lang']) && !empty($site['fullcalendar_lang'])) : ?>
        <script src="<?php echo base_url() . $site['fullcalendar_lang'] ?>"></script>
        <?php endif; ?>
        <?php if(isset($site['datetimepicker']) && !empty($site['datetimepicker'])) : ?>
        <script type="text/javascript">
          jQuery.datetimepicker.setLocale('<?php echo $site['datetimepicker'] ?>');
        </script>
        <?php endif; ?>