<div class="white-area-content">

<div class="db-header clearfix">
    <div class="page-header-title"> <span class="glyphicon glyphicon-flag"></span> <?php echo lang("ctn_463") ?></div>
    <div class="db-header-extra form-inline"> 

</div>
</div>


<div class="panel panel-default">
  <div class="panel-body">
  <?php echo form_open(site_url("behaviour/edit_rule_pro/" . $rule->ID), array("class" => "form-horizontal")) ?>
       <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_464") ?></label>
                    <div class="col-md-8 ui-front">
                        <input type="text" class="form-control" name="name" value="<?php echo $rule->name ?>">
                    </div>
            </div>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_465") ?></label>
                    <div class="col-md-8 ui-front">
                         <input type="text" class="form-control" name="points" value="<?php echo $rule->points ?>">
                    </div>
            </div>
     <input type="submit" name="s" value="<?php echo lang("ctn_466") ?>" class="btn btn-primary form-control" />
  <?php echo form_close() ?>
  </div>
  </div>




</div>