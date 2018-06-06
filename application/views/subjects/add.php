<div class="white-area-content">

<div class="db-header clearfix">
    <div class="page-header-title"> <span class="glyphicon glyphicon-book"></span> <?php echo lang("ctn_728") ?></div>
    <div class="db-header-extra form-inline"> 


<a href="<?php echo site_url("subjects/add") ?>" class="btn btn-primary btn-sm"><?php echo lang("ctn_768") ?></a>

</div>
</div>


<div class="panel panel-default">
  <div class="panel-body">
  <?php echo form_open(site_url("subjects/add_subject_pro"), array("class" => "form-horizontal")) ?>
          <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label"><?php echo lang("ctn_769") ?></label>
        <div class="col-sm-10">
          <input type="text" class="form-control" name="name">
        </div>
    </div>
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label"><?php echo lang("ctn_271") ?></label>
        <div class="col-sm-10">
          <textarea name="description" id="desc-area"></textarea>
        </div>
    </div>
     <input type="submit" name="s" value="<?php echo lang("ctn_768") ?>" class="btn btn-primary form-control" />
  <?php echo form_close() ?>
  </div>
  </div>



</div>
<script type="text/javascript">
$(document).ready(function() {
CKEDITOR.replace('desc-area', { height: '150'});
});
</script>