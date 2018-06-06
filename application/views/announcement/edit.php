<div class="white-area-content">

<div class="db-header clearfix">
    <div class="page-header-title"> <span class="glyphicon glyphicon-bullhorn"></span> <?php echo lang("ctn_447") ?></div>
    <div class="db-header-extra form-inline"> 

    <a href="<?php echo site_url("announcements") ?>" class="btn btn-primary btn-sm"><?php echo lang("ctn_447") ?></a>

</div>
</div>


<div class="panel panel-default">
<div class="panel-body">
  <?php echo form_open(site_url("announcements/edit_announcement_pro/" . $announcement->ID), array("class" => "form-horizontal")) ?>
       <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_448") ?></label>
                    <div class="col-md-8 ui-front">
                        <input type="text" class="form-control" name="title" value="<?php echo $announcement->title ?>">
                    </div>
            </div>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_449") ?></label>
                    <div class="col-md-8 ui-front">
                        <textarea name="announcement" id="ann-area"><?php echo $announcement->announcement ?></textarea>
                    </div>
            </div>
     <input type="submit" name="s" value="<?php echo lang("ctn_450") ?>" class="btn btn-primary form-control" />
  <?php echo form_close() ?>
</div>
</div>




</div>
<script type="text/javascript">
$(document).ready(function() {
CKEDITOR.replace('ann-area', { height: '150'});

});
</script>