<?php include("class_header.php") ?>
<div class="white-area-content">

<div class="db-header clearfix">
    <div class="page-header-title"> <span class="glyphicon glyphicon-bell"></span> <?php echo lang("ctn_507") ?></div>
    <div class="db-header-extra form-inline"> 


</div>
</div>



<div class="panel panel-default">
  <div class="panel-body">
  <?php echo form_open_multipart(site_url("classes/edit_user_assignment_pro/" . $assignment->ID), array("class" => "form-horizontal")) ?>
  <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_525") ?></label>
                    <div class="col-md-8 ui-front">
                        <table class="table table-bordered table-hover table-striped">
                        <tr><td><?php echo lang("ctn_526") ?></td><td><?php echo $assignment->file_name ?></td></tr>
                        <tr><td><?php echo lang("ctn_527") ?></td><td><?php echo $assignment->file_size ?> kb</td></tr>
                        <tr><td><?php echo lang("ctn_528") ?></td><td><?php echo $assignment->file_type ?></td></tr>
                        <tr><td><?php echo lang("ctn_529") ?></td><td><?php echo date($this->settings->info->date_format, $assignment->timestamp) ?></td></tr>
                        <tr><td><?php echo lang("ctn_37") ?></td><td><?php echo $_SERVER['REMOTE_ADDR'] ?></td></tr>
                        </table>
                    </div>
            </div>
          <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_530") ?></label>
                    <div class="col-md-8 ui-front">
                        <input type="file" name="userfile" class="form-control">
                    </div>
            </div>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_495") ?></label>
                    <div class="col-md-8 ui-front">
                        <textarea name="notes" id="ann-area"><?php echo $assignment->notes ?></textarea>
                    </div>
            </div>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_850") ?></label>
                    <div class="col-md-8">
                        <input type="text" name="mark" class="form-control" value="<?php echo $assignment->mark ?>">
                        <span class="help-text"><?php echo lang("ctn_851") ?> <strong><?php echo $assignment->max_mark ?></strong></span>
                    </div>
            </div>
     <input type="submit" name="s" value="<?php echo lang("ctn_516") ?>" class="btn btn-primary form-control" />
  <?php echo form_close() ?>
  </div>
  </div>



</div>
<script type="text/javascript">
$(document).ready(function() {
CKEDITOR.replace('ann-area', { height: '150'});

});
</script>