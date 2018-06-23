<div class="panel panel-default">
    <div class="panel-body">
        <?php echo form_open_multipart(site_url("home/process_user"), array("class" => "form-horizontal")) ?>
        <input type="hidden" name="userid" value="<?php if (isset($member)) echo $member->ID ?>">
        <input type="hidden" name="redirect" value="<?php echo $redirect ?>">
        <input type="hidden" name="hook" value="<?php echo $hook ?>">
        <div class="form-group">
            <label for="email-in" class="col-md-3 label-heading"><?php echo lang("ctn_24") ?><span class="required"> *</span></label>
            <div class="col-md-9">
                <input type="email" class="form-control" id="email-in" name="email" value="<?php if (isset($member->email)) echo $member->email ?>">
            </div>
        </div>
        <div class="form-group">

            <label for="username-in" class="col-md-3 label-heading"><?php echo lang("ctn_25") ?><span class="required"> *</span></label>
            <div class="col-md-9">
                <input type="text" class="form-control" id="username" name="username" value="<?php if (isset($member->username)) echo $member->username ?>">
                <div id="username_check"></div>
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-3 label-heading"><?php echo lang("ctn_26") ?></label>
            <div class="col-sm-9">
                <?php if (isset($member->avatar)) : ?>
                    <img src="<?php echo base_url() ?>/<?php echo $this->settings->info->upload_path_relative ?>/<?php echo $member->avatar ?>" />
                <?php endif; ?>
                <input type="file" name="userfile" /> 
            </div>
        </div>
        <?php if (!isset($flags['new_user'])) : ?>
            <div class="form-group">

                <label for="password-in" class="col-md-3 label-heading"><?php echo lang("ctn_27") ?><span class="required"> *</span></label>
                <div class="col-md-9">
                    <input type="password" class="form-control" id="password-in" name="password" value="">
                    <span class="help-text"><?php echo lang("ctn_28") ?></span>
                </div>
            </div>
        <?php else : ?>
            <div class="form-group">
                <label for="password-in" class="col-md-3 label-heading"><?php echo lang("ctn_87") ?><span class="required"> *</span></label>
                <div class="col-md-9">
                    <input type="password" class="form-control" id="password-in" name="password" value="">
                </div>
            </div>
            <div class="form-group">
                <label for="password2-in" class="col-md-3 label-heading"><?php echo lang("ctn_88") ?><span class="required"> *</span></label>
                <div class="col-md-9">
                    <input type="password" class="form-control" id="password2-in" name="password2" value="">
                </div>
            </div>
        <?php endif; ?>

        <div class="form-group">

            <label for="first-name-in" class="col-md-3 label-heading"><?php echo lang("ctn_29") ?><span class="required"> *</span></label>
            <div class="col-md-9">
                <input type="text" class="form-control" id="first-name-in" name="first_name" value="<?php if (isset($member->first_name)) echo $member->first_name ?>">
            </div>
        </div>
        <div class="form-group">

            <label for="last-name-in" class="col-md-3 label-heading"><?php echo lang("ctn_30") ?><span class="required"> *</span></label>
            <div class="col-md-9">
                <input type="text" class="form-control" id="last-name-in" name="last_name" value="<?php if (isset($member->last_name)) echo $member->last_name ?>">
            </div>
        </div>
        <div class="form-group">

            <label for="fathers-name-in" class="col-md-3 label-heading"><?php echo lang("ctn_977") ?><span class="required"> *</span></label>
            <div class="col-md-9">
                <input type="text" class="form-control" id="fathers-name-in" name="fathers_name" value="<?php if (isset($member->fathers_name)) echo $member->fathers_name ?>">
            </div>
        </div>
        <div class="form-group ui-front">
            <label for="p-in" class="col-md-3 label-heading"><?php echo lang("ctn_1018") ?><span class="required"> *</span></label>
            <div class="col-md-9">
                <input type="text" name="birth_date" class="form-control datepicker" 
                       value="<?php if (isset($member->birth_date)) echo date('d/m/Y',strtotime($member->birth_date)); ?>">
            </div>
        </div>
        <?php if (isset($flags['credits']) && $flags['credits']) : ?>
            <div class="form-group">
                <label for="name-in" class="col-md-3 label-heading"><?php echo lang("ctn_350") ?></label>
                <div class="col-md-9">
                    <input type="text" class="form-control" id="name-in" name="credits" value="<?php if (isset($member->points)) echo $member->points ?>">
                </div>
            </div>
        <?php endif; ?>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-3 label-heading"><?php echo lang("ctn_31") ?></label>
            <div class="col-sm-9">
                <textarea class="form-control" name="aboutme" rows="8"><?php if (isset($member->aboutme)) echo nl2br($member->aboutme) ?></textarea>
            </div>
        </div>
        <?php if (isset($member)) : ?>
            <div class="form-group">

                <label for="ip-in" class="col-md-3 label-heading"><?php echo lang("ctn_36") ?></label>
                <div class="col-md-9">
                    <?php echo lang("ctn_37") ?> : <?php echo $member->IP ?> <br />
                    <?php echo lang("ctn_38") ?> : <?php echo date($this->settings->info->date_format, $member->joined) ?><br />
                    <?php echo lang("ctn_39") ?> : <?php echo date($this->settings->info->date_format, $member->online_timestamp) ?>
                </div>
            </div>
        <?php endif; ?>
        <?php if (isset($flags['activate_user']) && $flags['activate_user']) : ?>
            <div class="form-group">

                <label for="active-in" class="col-md-3 label-heading"><?php echo lang("ctn_331") ?></label>
                <div class="col-md-9">
                    <select name="active" class="form-control">
                        <option value="0"><?php echo lang("ctn_332") ?></option>
                        <option value="1" <?php if (isset($member->active) && $member->active) {
            echo "selected";
        } else {
            echo"selected";
        } ?>><?php echo lang("ctn_333") ?></option>
                    </select>
                </div>
            </div>
<?php endif; ?>
        <div class="form-group">
            <label for="name-in" class="col-md-3 label-heading"><?php echo lang("ctn_322") ?><span class="required"> *</span></label>
            <div class="col-md-9">
                <select name="user_role" class="form-control">
                    <option value="0" selected><?php echo lang("ctn_46") ?></option>
<?php foreach ($user_roles->result() as $r) : ?>
                        <option value="<?php echo $r->ID ?>" <?php if (isset($member->user_role) && $r->ID == $member->user_role) echo "selected" ?>><?php echo $r->name ?></option>
<?php endforeach; ?>
                </select>
            </div>
        </div>
        <h4><?php echo lang("ctn_419") ?></h4>
        <div class="form-group">
            <label for="inputEmail3" class="col-md-3 label-heading"><?php echo lang("ctn_420") ?><span class="required"> *</span></label>
            <div class="col-md-9">
                <input type="text" name="address_1" class="form-control" value="<?php if (isset($member->address_line_1)) echo $member->address_line_1 ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-md-3 label-heading"><?php echo lang("ctn_1021") ?><span class="required"> *</span></label>
            <div class="col-md-9">
                <input type="text" name="mobile_phone" class="form-control" value="<?php if (isset($member->mobile_phone)) echo $member->mobile_phone ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-md-3 label-heading"><?php echo lang("ctn_422") ?><span class="required"> *</span></label>
            <div class="col-md-9">
                <input type="text" name="city" class="form-control" value="<?php if (isset($member->city)) echo $member->city ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-md-3 label-heading"><?php echo lang("ctn_423") ?><span class="required"> *</span></label>
            <div class="col-md-9">
                <input type="text" name="state" class="form-control" value="<?php if (isset($member->state)) echo $member->state ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-md-3 label-heading"><?php echo lang("ctn_1022") ?><span class="required"> *</span></label>
            <div class="col-md-9">
                <input type="text" name="phone" class="form-control" value="<?php if (isset($member->phone)) echo $member->phone ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-md-3 label-heading"><?php echo lang("ctn_425") ?><span class="required"> *</span></label>
            <div class="col-md-9">
                <input type="text" name="country" class="form-control" value="<?php if (isset($member->country)) echo $member->country ?>">
            </div>
        </div>
        <h4><?php echo lang("ctn_346") ?></h4>
                <?php foreach ($fields->result() as $r) : ?>
            <div class="form-group">

                <label for="name-in-<?php echo $r->ID ?>" class="col-md-3 label-heading"><?php echo $r->name ?> <?php if ($r->required) : ?><span class="required">*</span><?php endif; ?></label>
                <div class="col-md-9">
                    <?php if ($r->type == 0) : ?>
                        <input type="text" class="form-control" id="name-in-<?php echo $r->ID ?>" name="cf_<?php echo $r->ID ?>" value="<?php if (isset($r->value)) echo $r->value ?>">
                    <?php elseif ($r->type == 1) : ?>
                        <textarea name="cf_<?php echo $r->ID ?>" rows="8" class="form-control"><?php if (isset($r->value)) echo $r->value ?></textarea>
                    <?php elseif ($r->type == 2) : ?>
                        <?php $options = explode(",", $r->options); ?>
                        <?php if (isset($r->value)) : ?>
                            <?php $values = array_map('trim', (explode(",", $r->value))); ?>
                        <?php else : ?>
                            <?php $values = array() ?>
                        <?php endif; ?>
                        <?php if (count($options) > 0) : ?>
                            <?php foreach ($options as $k => $v) : ?>
                                <div class="form-group"><input type="checkbox" name="cf_cb_<?php echo $r->ID ?>_<?php echo $k ?>" value="1" <?php if (in_array($v, $values)) echo "checked" ?>> <?php echo $v ?></div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php elseif ($r->type == 3) : ?>
                        <?php $options = explode(",", $r->options); ?>

                        <?php if (count($options) > 0) : ?>
                            <?php foreach ($options as $k => $v) : ?>
                                <div class="form-group"><input type="radio" name="cf_radio_<?php echo $r->ID ?>" value="<?php echo $k ?>" <?php if (isset($r->value) && $r->value == $v) echo "checked" ?>> <?php echo $v ?></div>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        <?php elseif ($r->type == 4) : ?>
                            <?php $options = explode(",", $r->options); ?>
                            <?php if (count($options) > 0) : ?>
                            <select name="cf_<?php echo $r->ID ?>" class="form-control">
                            <?php foreach ($options as $k => $v) : ?>
                                    <option value="<?php echo $k ?>" <?php if (isset($r->value) && $r->value == $v) echo "selected" ?>><?php echo $v ?></option>
                            <?php endforeach; ?>
                            </select>
        <?php endif; ?>
    <?php elseif ($r->type == 5) : ?>
                        <input type="text" class="form-control datepicker" name="cf_<?php echo $r->ID ?>" value="<?php if (isset($r->value)) echo date($this->settings->info->date_picker_format, $r->value) ?>">
            <?php endif; ?>
                    <span class="help-text"><?php echo $r->help_text ?></span>
                </div>
            </div>
<?php endforeach; ?>
        <p><span class="required">*</span><?php echo lang("ctn_351") ?></p>
        <input type="submit" class="btn btn-primary form-control" value="<?php if (isset($flags['new_user'])) : ?>Add<?php else : ?><?php echo lang("ctn_13") ?><?php endif; ?>" />
<?php echo form_close() ?>
    </div>
</div>
</div>