<div class="white-area-content">

<div class="db-header clearfix">
    <div class="page-header-title"> <span class="glyphicon glyphicon-book"></span> <?php echo lang("ctn_712") ?></div>
    <div class="db-header-extra form-inline"> 

</div>
</div>


<div class="panel panel-default">
  <div class="panel-body">
  <?php echo form_open(site_url("subjects/edit_teacher_subject_pro/" . $teacher->ID), array("class" => "form-horizontal")) ?>
     <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_467") ?></label>
                    <div class="col-md-8 ui-front">
                        <input type="text" class="form-control" name="username" id="username-search" value="<?php echo $teacher->username ?>">
                    </div>
            </div>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_476") ?></label>
                    <div class="col-md-8">
                        <select name="subjectid" class="form-control">
                        <?php foreach($subjects->result() as $r) : ?>
                          <option value="<?php echo $r->ID ?>" <?php if($r->ID == $teacher->subjectid) echo "selected" ?>><?php echo $r->name ?></option>
                        <?php endforeach; ?>
                        </select>
                    </div>
            </div>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_771") ?></label>
                    <div class="col-md-8 ui-front">
                        <input type="checkbox" name="head" value="1" <?php if($teacher->head) echo "checked" ?>>
                    </div>
            </div>
     <input type="submit" name="s" value="<?php echo lang("ctn_772") ?>" class="btn btn-primary form-control" />
  <?php echo form_close() ?>
  </div>
  </div>



</div>
<script type="text/javascript">
$(document).ready(function() {
CKEDITOR.replace('desc-area', { height: '150'});
});
</script>