<?php include("class_header.php") ?>
<div class="white-area-content">

<div class="db-header clearfix">
    <div class="page-header-title"> <span class="glyphicon glyphicon-bell"></span> <?php echo lang("ctn_489") ?></div>
    <div class="db-header-extra form-inline"> 


</div>
</div>

<div class="panel panel-default">
  <div class="panel-body">
<?php echo form_open(site_url("classes/edit_grade_pro/" . $grade->ID), array("class" => "form-horizontal")) ?>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_531") ?></label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="grade" value="<?php echo $grade->grade ?>">
                    </div>
            </div>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_857") ?></label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="min_score" value="<?php echo $grade->min_score ?>">
                    </div>
            </div>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_858") ?></label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="max_score" value="<?php echo $grade->max_score ?>">
                    </div>
            </div>
              <input type="submit" name="s" value="<?php echo lang("ctn_860") ?>" class="btn btn-primary form-control" />
  <?php echo form_close() ?>
  </div>
  </div>

</div>