<div class="white-area-content">

    <div class="db-header clearfix">
        <div class="page-header-title"> <span class="glyphicon glyphicon-bell"></span> <?php echo lang("ctn_999") ?></div>
        <div class="db-header-extra"> 
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-body">

            <?php echo form_open_multipart(site_url("classes/edit_room_pro/" . $room->room_id), array("class" => "form-horizontal")) ?>
            <div class="form-group">
                <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_993") ?></label>
                <div class="col-md-8 ui-front">
                    <input type="number" class="form-control" name="numeric_code" min="0" value="<?php echo $room->numeric_code ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="code-in" class="col-md-4 label-heading"><?php echo lang("ctn_994") ?></label>
                <div class="col-md-8 ui-front">
                    <input type="text" class="form-control" name="code" value="<?php echo $room->code ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="branch-in" class="col-md-4 label-heading"><?php echo lang("ctn_995") ?></label>
                <div class="col-md-8 ui-front">
                    <select name='branch_id' class="form-control">
                        <?php if (isset($branches)) : ?>
                            <?php foreach ($branches as $branch): ?>
                                <?php if(intval($branch->branch_id) === intval($room->branch_id)) :?>
                                <option value='<?php echo $branch->branch_id ?>' selected><?php echo $branch->name ?></option>
                                <?php else: ?>
                                <option value='<?php echo $branch->branch_id ?>'><?php echo $branch->name ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
            <!-- <div class="form-group">
                <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_996") ?></label>
                <div class="col-md-8 ui-front">
                    <input type="number" class="form-control" name="seat_total" min="1" value="<?php echo $room->seat_total ?>">
                </div>
            </div> -->
            <input type="submit" class="btn btn-primary form-control" value="<?php echo lang("ctn_998") ?>">
            <?php echo form_close() ?>

        </div>
    </div>


</div>