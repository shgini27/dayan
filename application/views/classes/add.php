<div class="white-area-content">

<div class="db-header clearfix">
    <div class="page-header-title"> <span class="glyphicon glyphicon-bell"></span> <?php echo lang("ctn_471") ?></div>
    <div class="db-header-extra form-inline"> 

<?php if(!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) : ?>
<a href="<?php echo site_url("classes/add") ?>" class="btn btn-primary btn-sm"><?php echo lang("ctn_472") ?></a>
<?php endif; ?>

</div>
</div>


<div class="panel panel-default">
  <div class="panel-body">
  <?php echo form_open(site_url("classes/add_class_pro"), array("class" => "form-horizontal")) ?>
          <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label"><?php echo lang("ctn_473") ?></label>
        <div class="col-sm-10">
          <input type="text" class="form-control" name="name">
        </div>
    </div>
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label"><?php echo lang("ctn_474") ?></label>
        <div class="col-sm-10">
          <textarea name="description" id="desc-area"></textarea>
        </div>
    </div>
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label"><?php echo lang("ctn_475") ?></label>
        <div class="col-sm-10">
          <textarea name="content" id="content-area"></textarea>
        </div>
    </div>
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label"><?php echo lang("ctn_476") ?></label>
        <div class="col-sm-10">
          <select name="subjectid" class="form-control">
          <?php foreach($subjects->result() as $r) : ?>
          	<option value="<?php echo $r->ID ?>"><?php echo $r->name ?></option>
          <?php endforeach; ?>
          </select>
        </div>
    </div>
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label"><?php echo lang("ctn_477") ?></label>
        <div class="col-sm-10">
          <select name="categoryid" class="form-control">
          <?php foreach($categories->result() as $r) : ?>
          	<option value="<?php echo $r->ID ?>"><?php echo $r->name ?></option>
          <?php endforeach; ?>
          </select>
        </div>
    </div>
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label"><?php echo lang("ctn_478") ?></label>
        <div class="col-sm-10">
          <select name="teachers[]" data-placeholder="<?php echo lang("ctn_479") ?>" multiple class="form-control chosen-select-no-single">
          <?php foreach($teachers->result() as $r) : ?>
            <option value="<?php echo $r->username ?>"><?php echo $r->username ?></option>
          <?php endforeach; ?>
          </select>
        </div>
    </div>
    <h4><?php echo lang("ctn_481") ?></h4>
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label"><?php echo lang("ctn_480") ?></label>
        <div class="col-sm-10">
          <select name="groupid" class="form-control">
            <option value="0"><?php echo lang("ctn_46") ?></option>
          <?php foreach($groups->result() as $r) : ?>
          	<option value="<?php echo $r->ID ?>"><?php echo $r->name ?></option>
          <?php endforeach; ?>
          </select>
          <span class="help-block"><?php echo lang("ctn_482") ?></span>
        </div>
    </div>
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label"><?php echo lang("ctn_483") ?></label>
        <div class="col-sm-10">
          <select name="students[]" data-placeholder="<?php echo lang("ctn_484") ?>" multiple class="form-control chosen-select-no-single">
          <?php foreach($students->result() as $r) : ?>
            <option value="<?php echo $r->username ?>"><?php echo $r->username ?></option>
          <?php endforeach; ?>
          </select>
        </div>
    </div>
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label"><?php echo lang("ctn_485") ?></label>
        <div class="col-sm-10">
          <input type="checkbox" name="allow_signups" value="1">
          <span class="help-block"><?php echo lang("ctn_486") ?></span>
        </div>
    </div>
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label"><?php echo lang("ctn_487") ?></label>
        <div class="col-sm-10">
          <input type="text" class="form-control" name="max_students" value="0">
          <span class="help-block"><?php echo lang("ctn_488") ?></span>
        </div>
    </div>

     <input type="submit" name="s" value="<?php echo lang("ctn_472") ?>" class="btn btn-primary form-control" />
  <?php echo form_close() ?>
  </div>
  </div>



</div>
<script type="text/javascript">
$(document).ready(function() {
CKEDITOR.replace('desc-area', { height: '150'});
CKEDITOR.replace('content-area', { height: '150'});


$(".chosen-select-no-single").chosen({
});
});
</script>