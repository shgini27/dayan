<div class="white-area-content">

    <div class="db-header clearfix">
        <div class="page-header-title"> <span class="glyphicon glyphicon-bell"></span> <?php echo lang("ctn_990") ?></div>
        <div class="db-header-extra"> 
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-body">

            <?php echo form_open_multipart(site_url("classes/edit_branch_pro/" . $branch->branch_id), array("class" => "form-horizontal")) ?>
            <div class="form-group">
                <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_987") ?></label>
                <div class="col-md-8 ui-front">
                    <input type="text" class="form-control" name="code" value="<?php echo $branch->code ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_81") ?></label>
                <div class="col-md-8 ui-front">
                    <input type="text" class="form-control" name="name" value="<?php echo $branch->name ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_988") ?></label>
                <div class="col-md-8 ui-front">
                    <input type="number" class="form-control" name="room_total" min="1" value="<?php echo $branch->room_total ?>">
                </div>
            </div>
            <input type="submit" class="btn btn-primary form-control" value="<?php echo lang("ctn_991") ?>">
            <?php echo form_close() ?>

        </div>
    </div>


</div>