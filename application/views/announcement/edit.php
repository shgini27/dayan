<div class="white-area-content">

    <div class="db-header clearfix">
        <div class="page-header-title"> <span class="glyphicon glyphicon-bullhorn"></span> <?php echo lang("ctn_447") ?></div>
        <div class="db-header-extra form-inline"> 

            <a href="<?php echo site_url("announcements") ?>" class="btn btn-primary btn-sm"><?php echo lang("ctn_447") ?></a>

        </div>
    </div>


    <div class="panel panel-default">
        <div class="panel-body">
            <?php echo form_open(site_url("announcements/edit_announcement_pro/" . $announcement->ID), array("class" => "form-horizontal")) ?>
            <div class="form-group">
                <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_448") ?></label>
                <div class="col-md-8 ui-front">
                    <input type="text" class="form-control" name="title" value="<?php echo $announcement->title ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_449") ?></label>
                <div class="col-md-8 ui-front">
                    <textarea name="announcement" id="ann-area"><?php echo $announcement->announcement ?></textarea>
                </div>
            </div>
            <hr />
            <?php foreach ($user_roles->result() as $user_role): ?>
                <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo $user_role->name ?></label>
                    <div class="col-md-8">
                        <input type="checkbox" name="roles[]" value="<?php echo $user_role->ID ?>" <?php $rls = json_decode($announcement->roles);if($rls){if(in_array($user_role->ID, $rls)){ ?> checked <?php }} ?>>
                    </div>
                </div>
            <?php endforeach; ?><hr />
            <input type="submit" name="s" value="<?php echo lang("ctn_450") ?>" class="btn btn-primary form-control" />
            <?php echo form_close() ?>
        </div>
    </div>




</div>
<script type="text/javascript">
    $(document).ready(function () {
        CKEDITOR.replace('ann-area', {height: '150'});

    });
</script>