<div class="white-area-content">

<div class="db-header clearfix">
    <div class="page-header-title"> <span class="glyphicon glyphicon-file"></span> <?php echo lang("ctn_596") ?></div>
    <div class="db-header-extra"> 
</div>
</div>

<div class="panel panel-default">
<div class="panel-body">
<?php echo form_open_multipart(site_url("files/add_file_pro"), array("class" => "form-horizontal")) ?>
<div class="form-group">
        <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_597") ?></label>
        <div class="col-md-8 ui-front">
            <input type="text" class="form-control" name="file_name">
        </div>
</div>
<div class="form-group">
        <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_598") ?></label>
        <div class="col-md-8 ui-front">
            <input type="file" class="form-control" name="userfile">
        </div>
</div>
<div class="form-group">
        <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_599") ?></label>
        <div class="col-md-8 ui-front">
            <input type="text" class="form-control" name="file_url">
            <span class="help-block"><?php echo lang("ctn_600") ?></span>
        </div>
</div>
<div class="form-group">
        <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_601") ?></label>
        <div class="col-md-8">
            <textarea name="notes" id="file-area"></textarea>
        </div>
</div>
<div class="form-group">
        <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_477") ?></label>
        <div class="col-md-8">
            <select name="categoryid" class="form-control">
            <?php foreach($categories->result() as $r) : ?>
                <option value="<?php echo $r->ID ?>"><?php echo $r->name ?></option>
            <?php endforeach; ?>
            </select>
        </div>
</div>
<input type="submit" class="btn btn-primary form-control" value="<?php echo lang("ctn_572") ?>">
<?php echo form_close() ?>
</div>
</div>

</div>
<script type="text/javascript">
$(document).ready(function() {
CKEDITOR.replace('file-area', { height: '150'});

});
</script>