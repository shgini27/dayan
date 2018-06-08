<div class="white-area-content">

    <div class="db-header clearfix">
        <div class="page-header-title"> <span class="glyphicon glyphicon-bell"></span> <?php echo lang("ctn_501") ?></div>
        <div class="db-header-extra"> 
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-body">

            <?php echo form_open_multipart(site_url("classes/edit_category_pro/" . $category->ID), array("class" => "form-horizontal")) ?>
            <div class="form-group">
                <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_980") ?></label>
                <div class="col-md-8 ui-front">
                    <input type="number" class="form-control" name="number" min="1" value="<?php echo $category->number ?>">
                </div>
            </div>        
            <div class="form-group">
                <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_81") ?></label>
                <div class="col-md-8 ui-front">
                    <input type="text" class="form-control" name="name" value="<?php echo $category->name ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_271") ?></label>
                <div class="col-md-8">
                    <textarea name="description" id="cat-description"><?php echo $category->description ?></textarea>
                </div>
            </div>
            <div class="form-group">
                <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_535") ?></label>
                <div class="col-md-8">
                    <input type="text" class="form-control datetimepicker" name="start_date" 
                           id="start_date" value="<?php $sd = DateTime::createFromFormat("Y-m-d", $category->start_date);echo $sd->format('d/m/Y'); ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_981") ?></label>
                <div class="col-md-8">
                    <input type="number" class="form-control" name="hrs" min="1" max="300" id="hrs" value="<?php echo $category->hrs ?>">
                </div>
            </div>
            <!-- <div class="form-group">
                <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_536") ?></label>
                <div class="col-md-8">
                    <input type="text" class="form-control datetimepicker" name="end_date" id="end_date" value="<?php echo $category->end_date ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_347") ?></label>
                <div class="col-md-8">
                    <p><img src="<?php echo base_url() ?><?php echo $this->settings->info->upload_path_relative ?>/<?php echo $category->image ?>"><br /></p>
                    <input type="file" name="userfile" />
                    <span class="help-block"><?php echo lang("ctn_503") ?></span>
                </div>
            </div> -->

            <input type="submit" class="btn btn-primary form-control" value="<?php echo lang("ctn_523") ?>">
            <?php echo form_close() ?>

        </div>
    </div>


</div>

<script type="text/javascript">
    $(document).ready(function () {
        CKEDITOR.replace('cat-description', {height: '100'});
        $('.datetimepicker').datetimepicker({
            format: '<?php echo $this->settings->info->date_format ?>'
        });
    });
</script>