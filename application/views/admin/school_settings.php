<div class="white-area-content">
<div class="db-header clearfix">
    <div class="page-header-title"> <span class="glyphicon glyphicon-user"></span> <?php echo lang("ctn_1") ?></div>
    <div class="db-header-extra"> 
</div>
</div>

<ol class="breadcrumb">
  <li><a href="<?php echo site_url() ?>"><?php echo lang("ctn_2") ?></a></li>
  <li><a href="<?php echo site_url("admin") ?>"><?php echo lang("ctn_1") ?></a></li>
  <li class="active"><?php echo lang("ctn_431") ?></li>
</ol>


<hr>

<div class="panel panel-default">
<div class="panel-body">
<?php echo form_open(site_url("admin/school_settings_pro"), array("class" => "form-horizontal")) ?>

<h4>Student Groups</h4>
<div class="form-group">
    <label for="name-in" class="col-sm-2 control-label"><?php echo lang("ctn_432") ?></label>
    <div class="col-sm-10">
    	<input type="checkbox" id="name-in" name="student_view_groups" value="1" <?php if($this->settings->info->student_view_groups) echo "checked" ?>>
    	<span class="help-block"><?php echo lang("ctn_433") ?></span>
    </div>
</div>
<div class="form-group">
    <label for="name-in" class="col-sm-2 control-label"><?php echo lang("ctn_434") ?></label>
    <div class="col-sm-10">
      <input type="checkbox" id="name-in" name="teacher_group_manage" value="1" <?php if($this->settings->info->teacher_group_manage) echo "checked" ?>>
      <span class="help-block"><?php echo lang("ctn_435") ?></span>
    </div>
</div>
<div class="form-group">
    <label for="name-in" class="col-sm-2 control-label"><?php echo lang("ctn_436") ?></label>
    <div class="col-sm-10">
      <input type="checkbox" id="name-in" name="teacher_class_manage" value="1" <?php if($this->settings->info->teacher_class_manage) echo "checked" ?>>
      <span class="help-block"><?php echo lang("ctn_437") ?></span>
    </div>
</div>
<div class="form-group">
    <label for="name-in" class="col-sm-2 control-label"><?php echo lang("ctn_438") ?></label>
    <div class="col-sm-10">
      <input type="checkbox" id="name-in" name="teacher_class" value="1" <?php if($this->settings->info->teacher_class) echo "checked" ?>>
      <span class="help-block"><?php echo lang("ctn_439") ?></span>
    </div>
</div>
<div class="form-group">
    <label for="name-in" class="col-sm-2 control-label"><?php echo lang("ctn_440") ?></label>
    <div class="col-sm-10">
      <input type="text" id="name-in" name="reserve_books" class="form-control" value="<?php echo $this->settings->info->reserve_books ?>">
      <span class="help-block"><?php echo lang("ctn_441") ?></span>
    </div>
</div>
<div class="form-group">
    <label for="name-in" class="col-sm-2 control-label"><?php echo lang("ctn_919") ?></label>
    <div class="col-sm-10">
      <input type="checkbox" id="name-in" name="allow_student_profile" value="1" <?php if($this->settings->info->allow_student_profile) echo "checked" ?>>
      <span class="help-block"><?php echo lang("ctn_920") ?></span>
    </div>
</div>
<input type="submit" class="btn btn-primary form-control" value="<?php echo lang("ctn_13") ?>" />
<?php echo form_close() ?>

</div>
</div>
</div>