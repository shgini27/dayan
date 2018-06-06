<div class="white-area-content">

<div class="db-header clearfix">
    <div class="page-header-title"> <span class="glyphicon glyphicon-home"></span> <?php echo lang("ctn_923") ?></div>
    <div class="db-header-extra form-inline">

</div>
</div>


<div class="panel panel-default">
  <div class="panel-body">
  <?php echo form_open(site_url("hostel/edit_booking_pro/" . $booking->ID), array("class" => "form-horizontal")) ?>
      <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_925") ?></label>
                    <div class="col-md-8 ui-front">
                        <select name="hostelid" class="form-control" id="hostel-select">
                          <option value="0"><?php echo lang("ctn_356") ?> ...</option>
                          <?php foreach($hostels->result() as $r) : ?>
                            <option value="<?php echo $r->ID ?>" <?php if($r->ID == $booking->hostelid) echo "selected" ?>><?php echo $r->name ?></option>
                          <?php endforeach; ?>
                        </select>
                    </div>
            </div>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_537") ?></label>
                    <div class="col-md-8" id="hostel-rooms">
                         <select name="roomid" class="form-control">
                            <option value="<?php echo $booking->roomid ?>"><?php echo $booking->room_name ?></option>
                        </select>
                    </div>
            </div>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_339") ?></label>
                    <div class="col-md-8 ui-front">
                        <input type="text" name="username" class="form-control" id="username-search" value="<?php if(isset($booking->username)) : ?><?php echo $booking->username ?><?php endif; ?>">
                    </div>
            </div>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_928") ?></label>
                    <div class="col-md-4">
                        <input type="text" name="guest_name" class="form-control" placeholder="<?php echo lang("ctn_879") ?>..." value="<?php echo $booking->guest_name ?>">
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="guest_email" class="form-control" placeholder="<?php echo lang("ctn_880") ?>..." value="<?php echo $booking->guest_email ?>">
                    </div>
            </div>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_926") ?></label>
                    <div class="col-md-8">
                        <input type="text" name="checkin" class="form-control datepicker" value="<?php echo date($this->settings->info->date_picker_format, $booking->checkin) ?>">
                    </div>
            </div>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_927") ?></label>
                    <div class="col-md-8">
                        <input type="text" name="checkout" class="form-control datepicker" value="<?php echo date($this->settings->info->date_picker_format, $booking->checkout) ?>">
                    </div>
            </div>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_495") ?></label>
                    <div class="col-md-8 ui-front">
                        <textarea name="notes" class="form-control"><?php echo $booking->notes ?></textarea>
                    </div>
            </div>
     <input type="submit" name="s" value="<?php echo lang("ctn_13") ?>" class="btn btn-primary form-control" />
  <?php echo form_close() ?>
  </div>
  </div>


</div>
<script type="text/javascript">
$(document).ready(function() {

  $('#hostel-select').on("change", function() {
    var hostelid = $('#hostel-select').val();

    $.ajax({
      url : global_base_url + "hostel/ajax_get_hostel_rooms/" + hostelid,
      success: function(msg) {
        $('#hostel-rooms').html(msg);
      }
    })
  })

});
</script>
