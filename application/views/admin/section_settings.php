<div class="white-area-content">
<div class="db-header clearfix">
    <div class="page-header-title"> <span class="glyphicon glyphicon-user"></span> <?php echo lang("ctn_1") ?></div>
    <div class="db-header-extra"> 
</div>
</div>

<ol class="breadcrumb">
  <li><a href="<?php echo site_url() ?>"><?php echo lang("ctn_2") ?></a></li>
  <li><a href="<?php echo site_url("admin") ?>"><?php echo lang("ctn_1") ?></a></li>
  <li class="active"><?php echo lang("ctn_389") ?></li>
</ol>


<hr>

<div class="panel panel-default">
<div class="panel-body">
<?php echo form_open_multipart(site_url("admin/section_settings_pro"), array("class" => "form-horizontal")) ?>
<div class="form-group">
    <label for="name-in" class="col-sm-2 control-label"><?php echo lang("ctn_390") ?></label>
    <div class="col-sm-10">
        <input type="checkbox" name="announcements_section" value="1" <?php if($this->settings->info->announcements_section) echo "checked" ?> />
    </div>
</div>
<div class="form-group">
    <label for="name-in" class="col-sm-2 control-label"><?php echo lang("ctn_391") ?></label>
    <div class="col-sm-10">
        <input type="checkbox" name="classes_section" value="1" <?php if($this->settings->info->classes_section) echo "checked" ?> />
    </div>
</div>
<div class="form-group">
    <label for="name-in" class="col-sm-2 control-label"><?php echo lang("ctn_392") ?></label>
    <div class="col-sm-10">
        <input type="checkbox" name="subjects_section" value="1" <?php if($this->settings->info->subjects_section) echo "checked" ?> />
    </div>
</div>
<div class="form-group">
    <label for="name-in" class="col-sm-2 control-label"><?php echo lang("ctn_393") ?></label>
    <div class="col-sm-10">
        <input type="checkbox" name="students_section" value="1" <?php if($this->settings->info->students_section) echo "checked" ?> />
    </div>
</div>
<div class="form-group">
    <label for="name-in" class="col-sm-2 control-label"><?php echo lang("ctn_394") ?></label>
    <div class="col-sm-10">
        <input type="checkbox" name="files_section" value="1" <?php if($this->settings->info->files_section) echo "checked" ?> />
    </div>
</div>
<!-- <div class="form-group">
    <label for="name-in" class="col-sm-2 control-label"><?php echo lang("ctn_395") ?></label>
    <div class="col-sm-10">
        <input type="checkbox" name="finance_section" value="1" <?php if($this->settings->info->finance_section) echo "checked" ?> />
    </div>
</div> -->
<div class="form-group">
    <label for="name-in" class="col-sm-2 control-label"><?php echo lang("ctn_396") ?></label>
    <div class="col-sm-10">
        <input type="checkbox" name="invoices_section" value="1" <?php if($this->settings->info->invoices_section) echo "checked" ?> />
    </div>
</div>
<div class="form-group">
    <label for="name-in" class="col-sm-2 control-label"><?php echo lang("ctn_397") ?></label>
    <div class="col-sm-10">
        <input type="checkbox" name="behaviour_section" value="1" <?php if($this->settings->info->behaviour_section) echo "checked" ?> />
    </div>
</div>
<div class="form-group">
    <label for="name-in" class="col-sm-2 control-label"><?php echo lang("ctn_398") ?></label>
    <div class="col-sm-10">
        <input type="checkbox" name="library_section" value="1" <?php if($this->settings->info->library_section) echo "checked" ?> />
    </div>
</div>
<div class="form-group">
    <label for="name-in" class="col-sm-2 control-label"><?php echo lang("ctn_399") ?></label>
    <div class="col-sm-10">
        <input type="checkbox" name="reports_section" value="1" <?php if($this->settings->info->reports_section) echo "checked" ?> />
    </div>
</div>
<div class="form-group">
    <label for="name-in" class="col-sm-2 control-label"><?php echo lang("ctn_864") ?></label>
    <div class="col-sm-10">
        <input type="checkbox" name="parent_section" value="1" <?php if($this->settings->info->parent_section) echo "checked" ?> />
    </div>
</div>
 <div class="form-group">
    <label for="name-in" class="col-sm-2 control-label"><?php echo lang("ctn_952") ?></label>
    <div class="col-sm-10">
        <input type="checkbox" name="documents_section" value="1" <?php if($this->settings->info->documents_section) echo "checked" ?> />
    </div>
</div>
<!-- <div class="form-group">
    <label for="name-in" class="col-sm-2 control-label"><?php echo lang("ctn_921") ?></label>
    <div class="col-sm-10">
        <input type="checkbox" name="hostel_section" value="1" <?php if($this->settings->info->hostel_section) echo "checked" ?> />
    </div>
</div> -->
<input type="submit" class="btn btn-primary form-control" value="<?php echo lang("ctn_13") ?>" />
<?php echo form_close() ?>
</div>
</div>
</div>