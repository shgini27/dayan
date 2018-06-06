<div class="white-area-content">

<div class="db-header clearfix">
    <div class="page-header-title"> <span class="glyphicon glyphicon-bell"></span> <?php echo lang("ctn_471") ?></div>
    <div class="db-header-extra"> 
</div>
</div>

<div class="panel panel-default">
<div class="panel-body">

<?php echo form_open(site_url("classes/edit_class_pro/" . $class->ID), array("class" => "form-horizontal")) ?>
               <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label"><?php echo lang("ctn_473") ?></label>
        <div class="col-sm-10">
          <input type="text" class="form-control" name="name" value="<?php echo $class->name ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label"><?php echo lang("ctn_474") ?></label>
        <div class="col-sm-10">
          <textarea name="description" id="desc-area"><?php echo $class->description ?></textarea>
        </div>
    </div>
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label"><?php echo lang("ctn_475") ?></label>
        <div class="col-sm-10">
          <textarea name="content" id="content-area"><?php echo $class->content ?></textarea>
        </div>
    </div>
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label"><?php echo lang("ctn_476") ?></label>
        <div class="col-sm-10">
          <select name="subjectid" class="form-control">
          <?php foreach($subjects->result() as $r) : ?>
            <option value="<?php echo $r->ID ?>" <?php if($r->ID == $class->subjectid) echo "selected" ?>><?php echo $r->name ?></option>
          <?php endforeach; ?>
          </select>
        </div>
    </div>
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label"><?php echo lang("ctn_477") ?></label>
        <div class="col-sm-10">
          <select name="categoryid" class="form-control">
          <?php foreach($categories->result() as $r) : ?>
            <option value="<?php echo $r->ID ?>" <?php if($r->ID == $class->categoryid) echo "selected" ?>><?php echo $r->name ?></option>
          <?php endforeach; ?>
          </select>
        </div>
    </div>
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label"><?php echo lang("ctn_485") ?></label>
        <div class="col-sm-10">
          <input type="checkbox" name="allow_signups" value="1" <?php if($class->allow_signups) echo "checked" ?>>
          <span class="help-block"><?php echo lang("ctn_486") ?></span>
        </div>
    </div>
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label"><?php echo lang("ctn_487") ?></label>
        <div class="col-sm-10">
          <input type="text" name="max_students" class="form-control" value="<?php echo $class->max_students ?>">
          <span class="help-block"><?php echo lang("ctn_488") ?></span>
        </div>
    </div>
          
    <input type="submit" class="btn btn-primary form-control" value="<?php echo lang("ctn_524") ?>">
    <?php echo form_close() ?>

</div>
</div>


</div>

<script type="text/javascript">
$(document).ready(function() {
CKEDITOR.replace('desc-area', { height: '150'});
CKEDITOR.replace('content-area', { height: '150'});
});
</script>