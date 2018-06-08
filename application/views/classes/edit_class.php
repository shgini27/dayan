<div class="white-area-content">

    <div class="db-header clearfix">
        <div class="page-header-title"> <span class="glyphicon glyphicon-bell"></span> <?php echo lang("ctn_471") ?></div>
        <div class="db-header-extra"> 
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-body">

            <?php echo form_open(site_url("classes/edit_class_pro/" . $class->ID), array("class" => "form-horizontal", "id" => "class_form")) ?>
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
                        <?php foreach ($subjects->result() as $r) : ?>
                            <option value="<?php echo $r->ID ?>" <?php if ($r->ID == $class->subjectid) echo "selected" ?>><?php echo $r->name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label"><?php echo lang("ctn_477") ?></label>
                <div class="col-sm-10">
                    <select name="categoryid" class="form-control">
                        <?php foreach ($categories->result() as $r) : ?>
                            <option value="<?php echo $r->ID ?>" <?php if ($r->ID == $class->categoryid) echo "selected" ?>><?php echo $r->name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label"><?php echo lang("ctn_995") ?></label>
                <div class="col-sm-10">
                    <select id="branch" name="branch_id" class="form-control">
                        <?php foreach ($branches->result() as $r) : ?>
                            <option value="<?php echo $r->branch_id ?>" <?php if ($r->branch_id == $class->branch_id) echo "selected" ?>><?php echo $r->name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div id="rooms" class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label"><?php echo lang("ctn_1000") ?></label>
                <div class="col-sm-10">
                    <select name="room_id" class="form-control">
                        <?php foreach ($rooms->result() as $r) : ?>
                            <option value="<?php echo $r->room_id ?>" <?php if ($r->room_id == $class->room_id) echo "selected" ?>><?php echo $r->code ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label"><?php echo lang("ctn_1002") ?></label>
                <div class="col-sm-10">
                    <select id="branch" name="class_days" class="form-control">
                        <?php if($class->class_days === 'odd') :?>
                        <option value="odd" selected><?php echo lang('ctn_1005'); ?></option>
                        <option value="even"><?php echo lang('ctn_1006'); ?></option>
                        <?php else: ?>
                        <option value="even" selected><?php echo lang('ctn_1006'); ?></option>
                        <option value="odd"><?php echo lang('ctn_1005'); ?></option>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label"><?php echo lang("ctn_1003") ?></label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="start_timepicker" name="start_hour" />
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label"><?php echo lang("ctn_1004") ?></label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="end_timepicker" name="end_hour" />
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label"><?php echo lang("ctn_485") ?></label>
                <div class="col-sm-10">
                    <input type="checkbox" name="allow_signups" value="1" <?php if ($class->allow_signups) echo "checked" ?>>
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
    $(document).ready(function () {
        CKEDITOR.replace('desc-area', {height: '150'});
        CKEDITOR.replace('content-area', {height: '150'});
        var start_options = {
            now: '<?php echo $class->start_hour; ?>',
            twentyFour: true,
            upArrow: 'wickedpicker__controls__control-up',
            downArrow: 'wickedpicker__controls__control-down',
            close: 'wickedpicker__close',
            hoverState: 'hover-state',
            title: 'Timepicker',
            showSeconds: false,
            minutesInterval: 5,
            beforeShow: null,
            show: null,
            clearable: false
        };
        var end_options = {
            now: '<?php echo $class->end_hour; ?>',
            twentyFour: true,
            upArrow: 'wickedpicker__controls__control-up',
            downArrow: 'wickedpicker__controls__control-down',
            close: 'wickedpicker__close',
            hoverState: 'hover-state',
            title: 'Timepicker',
            showSeconds: false,
            minutesInterval: 5,
            beforeShow: null,
            show: null,
            clearable: false
        };
        $('#start_timepicker').wickedpicker(start_options);
        $('#end_timepicker').wickedpicker(end_options);
    });
</script>
<script type="text/javascript">
    $("#branch").change(function () {
        $.ajax({
            url: '<?php echo site_url("classes/get_room_by_branch") ?>',
            type: 'post',
            data: $('#class_form input[type=\'hidden\'], #class_form select[name=\'branch_id\']'),
            dataType: 'json',
            success: function (json) {

                if (json['html']) {
                    $('#rooms select').remove();
                    $('#rooms div').html(json['html']);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    });
</script>