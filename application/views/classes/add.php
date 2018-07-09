<div class="white-area-content">

    <div class="db-header clearfix">
        <div class="page-header-title"> <span class="glyphicon glyphicon-bell"></span> <?php echo lang("ctn_471") ?></div>
        <div class="db-header-extra form-inline"> 

            <?php if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) : ?>
                <a href="<?php echo site_url("classes/add") ?>" class="btn btn-primary btn-sm"><?php echo lang("ctn_472") ?></a>
            <?php endif; ?>

        </div>
    </div>


    <div class="panel panel-default">
        <div class="panel-body">
            <?php echo form_open(site_url("classes/add_class_pro"), array("class" => "form-horizontal", "id" => "class_form")) ?>
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
                        <option value=""><?php echo lang('ctn_1001'); ?></option>
                        <?php foreach ($subjects->result() as $r) : ?>
                            <option value="<?php echo $r->ID ?>"><?php echo $r->name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label"><?php echo lang("ctn_477") ?></label>
                <div class="col-sm-10">
                    <select name="categoryid" class="form-control">
                        <option value=""><?php echo lang('ctn_1001'); ?></option>
                        <?php foreach ($categories->result() as $r) : ?>
                            <option value="<?php echo $r->ID ?>"><?php echo $r->name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label"><?php echo lang("ctn_995") ?></label>
                <div class="col-sm-10">
                    <select id="branch" name="branch_id" class="form-control">
                        <option value=""><?php echo lang('ctn_1001'); ?></option>
                        <?php foreach ($branches->result() as $r) : ?>
                            <option value="<?php echo $r->branch_id ?>"><?php echo $r->name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div id="rooms" class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label"><?php echo lang("ctn_1000") ?></label>
                <div class="col-sm-10">
                    <select name="room_id" class="form-control">
                        <option value=""><?php echo lang('ctn_1001'); ?></option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label"><?php echo lang("ctn_1002") ?></label>
                <div class="col-sm-10">
                    <select id="branch" name="class_days" class="form-control">
                        <option value=""><?php echo lang('ctn_1001'); ?></option>
                        <option value="odd"><?php echo lang('ctn_1005'); ?></option>
                        <option value="even"><?php echo lang('ctn_1006'); ?></option>
                        <option value="everyday"><?php echo lang('ctn_1024'); ?></option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="p-in" class="col-sm-2 control-label"><?php echo lang("ctn_981") ?></label>
                <div class="col-sm-10">
                    <input type="number" class="form-control" name="hrs" min="1" max="300" id="hrs">
                </div>
            </div>
            <div class="form-group">
                <label for="weeks" class="col-sm-2 control-label"><?php echo lang("ctn_1033") ?></label>
                <div class="col-sm-10">
                    <input type="number" class="form-control" name="weeks" min="1" max="70" id="weeks">
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label"><?php echo lang("ctn_1003") ?></label>
                <div class="col-sm-10">
                    <input type="text" class="form-control timepicker" name="start_hour" />
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label"><?php echo lang("ctn_1004") ?></label>
                <div class="col-sm-10">
                    <input type="text" class="form-control timepicker" name="end_hour" />
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label"><?php echo lang("ctn_478") ?></label>
                <div class="col-sm-10">
                    <select name="teachers[]" data-placeholder="<?php echo lang("ctn_479") ?>" multiple class="form-control chosen-select-no-single">
                        <?php foreach ($teachers->result() as $r) : ?>
                            <option value="<?php echo $r->username ?>"><?php echo $r->username ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <!--
            <h4><?php echo lang("ctn_481") ?></h4>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label"><?php echo lang("ctn_480") ?></label>
                <div class="col-sm-10">
                    <select name="groupid" class="form-control">
                        <option value="0"><?php echo lang("ctn_46") ?></option>
                        <?php foreach ($groups->result() as $r) : ?>
                            <option value="<?php echo $r->ID ?>"><?php echo $r->name ?></option>
                        <?php endforeach; ?>
                    </select>
                    <span class="help-block"><?php echo lang("ctn_482") ?></span>
                </div>
            </div>
            -->
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label"><?php echo lang("ctn_483") ?></label>
                <div class="col-sm-10">
                    <select name="students[]" data-placeholder="<?php echo lang("ctn_484") ?>" multiple class="form-control chosen-select-no-single">
                        <?php foreach ($students->result() as $r) : ?>
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
    $(document).ready(function () {
        CKEDITOR.replace('desc-area', {height: '150'});
        CKEDITOR.replace('content-area', {height: '150'});

        var options = {
            now: '09:00',
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
        $('.timepicker').wickedpicker(options);
        $(".chosen-select-no-single").chosen({
        });
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