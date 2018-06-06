<?php include("class_header.php") ?>
<div class="white-area-content">

<div class="db-header clearfix">
    <div class="page-header-title"> <span class="glyphicon glyphicon-bell"></span> <?php echo lang("ctn_489") ?></div>
    <div class="db-header-extra form-inline"> 
  
  <a href="<?php echo site_url("classes/add_attendance/" . $class->ID) ?>" class="btn btn-primary btn-sm"><?php echo lang("ctn_490") ?></a> 


</div>
</div>

<p><?php echo lang("ctn_491") ?> <strong><a href="<?php echo site_url("classes/view/" . $class->ID) ?>"><?php echo $class->name ?></a></strong></p>


<div class="panel panel-default">
  <div class="panel-body">
  <?php echo form_open(site_url("classes/add_attendance_pro/" . $class->ID), array("class" => "form-horizontal")) ?>
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label"><?php echo lang("ctn_492") ?></label>
        <div class="col-sm-10">
          <select name="eventid" class="form-control">
            <?php foreach($events->result() as $r) : ?>
              <option value="<?php echo $r->ID ?>"><?php echo $r->title ?> <?php echo $r->start ?></option>
            <?php endforeach; ?>
          </select>
        </div>
    </div>
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label"><?php echo lang("ctn_467") ?></label>
        <div class="col-sm-10">
          <input type="text" class="form-control" name="teacher" id="username-search" placeholder="<?php echo lang("ctn_493") ?>" value="<?php echo $this->user->info->username ?>">
        </div>
    </div>
    <table class="table table-bordered table-striped table-hover">
    <tr class="table-header"><td><?php echo lang("ctn_456") ?></td><td><?php echo lang("ctn_494") ?></td><td><?php echo lang("ctn_495") ?></td></tr>
    <?php foreach($students->result() as $r) : ?>
    	<?php if(!$r->teacher_flag) : ?>
    <tr><td>
    	<?php echo $this->common->get_user_display(array("username" => $r->username, "avatar" => $r->avatar, "online_timestamp" => $r->online_timestamp, "first_name" => $r->first_name, "last_name" => $r->last_name)) ?>
    </td><td>
    	<input type="radio" name="attendance_<?php echo $r->userid ?>" value="0"> <?php echo lang("ctn_496") ?>
    	<input type="radio" name="attendance_<?php echo $r->userid ?>" value="1"> <?php echo lang("ctn_497") ?>
    	<input type="radio" name="attendance_<?php echo $r->userid ?>" value="2"> <?php echo lang("ctn_498") ?>
    	<input type="radio" name="attendance_<?php echo $r->userid ?>" value="3"> <?php echo lang("ctn_499") ?>
    </td><td><textarea name="notes_<?php echo $r->userid ?>" class="form-control"></textarea></td></tr>
<?php endif;?>
    <?php endforeach; ?>
</table>

    <input type="submit" name="s" value="<?php echo lang("ctn_490") ?>" class="btn btn-primary form-control" />
  <?php echo form_close() ?>
  </div>
  </div>

</div>
<script type="text/javascript">
$(document).ready(function() {

   /* Get list of usernames */
  $('#username-search').autocomplete({
    delay : 300,
    minLength: 2,
    source: function (request, response) {
         $.ajax({
             type: "GET",
             url: global_base_url + "subjects/get_teachers",
             data: {
                query : request.term
             },
             dataType: 'JSON',
             success: function (msg) {
                 response(msg);
             }
         });
      }
  });

  });
</script>