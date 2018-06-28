<?php include("class_header.php") ?>
<div class="white-area-content">

<div class="db-header clearfix">
    <div class="page-header-title"> <span class="glyphicon glyphicon-bell"></span> <?php echo lang("ctn_507") ?></div>
    <div class="db-header-extra form-inline"> 


</div>
</div>



<div class="panel panel-default">
  <div class="panel-body">
  <?php echo form_open(site_url("classes/edit_assignment_pro/" . $assignment->ID), array("class" => "form-horizontal")) ?>
       <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_508") ?></label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="title" value="<?php echo $assignment->title ?>">
                    </div>
            </div>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_509") ?></label>
                    <div class="col-md-8">
                        <textarea name="assignment" id="ann-area"><?php echo $assignment->body ?></textarea>
                    </div>
            </div>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_510") ?></label>
                    <div class="col-md-8">
                        <input type="text" name="due_date" class="form-control datepicker" value="<?php echo date($this->settings->info->date_picker_format, $assignment->due_date) ?>">
                    </div>
            </div>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_852") ?></label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="weighting" value="<?php echo $assignment->weighting ?>">
                        <span class="help-block"><?php echo lang("ctn_853") ?></span>
                    </div>
            </div>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_854") ?></label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="max_mark" value="<?php echo $assignment->max_mark ?>">
                        <span class="help-block"><?php echo lang("ctn_855") ?></span>
                    </div>
            </div>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_11") ?></label>
                    <div class="col-md-8">
                        <input type="text" name="file_types" class="form-control" value="<?php echo $assignment->file_types ?>">
                    </div>
            </div>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_512") ?></label>
                    <div class="col-md-8">
                        <input type="checkbox" name="reupload" value="1" <?php if($assignment->reupload) echo "checked" ?>>
                        <span class="help-block"><?php echo lang("ctn_513") ?></span>
                    </div>
            </div>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_514") ?></label>
                    <div class="col-md-8">
                        <input type="checkbox" name="deny_upload" value="1" <?php if($assignment->deny_upload) echo "checked" ?>>
                        <span class="help-block"><?php echo lang("ctn_515") ?></span>
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
<script type="text/javascript">
$('.datepicker').datepicker({
    format: "dd/mm/yyyy"
});
</script>