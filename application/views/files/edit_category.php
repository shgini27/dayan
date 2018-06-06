<div class="white-area-content">

<div class="db-header clearfix">
    <div class="page-header-title"> <span class="glyphicon glyphicon-file"></span> <?php echo lang("ctn_596") ?></div>
    <div class="db-header-extra"> 
</div>
</div>

<div class="panel panel-default">
<div class="panel-body">
<?php echo form_open(site_url("files/edit_category_pro/" . $category->ID), array("class" => "form-horizontal")) ?>
<div class="form-group">
        <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_81") ?></label>
        <div class="col-md-8 ui-front">
            <input type="text" class="form-control" name="name" value="<?php echo $category->name ?>">
        </div>
</div>
<div class="form-group">
        <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_271") ?></label>
        <div class="col-md-8">
            <textarea name="description" class="form-control"><?php echo $category->description ?></textarea>
        </div>
</div>
<input type="submit" class="btn btn-primary form-control" value="<?php echo lang("ctn_523") ?>">
<?php echo form_close() ?>
</div>
</div>

</div>