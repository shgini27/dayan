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
                <label for="password-in" class="col-md-3 label-heading"><?php echo lang("ctn_88") ?><span class="required"> *</span></label>
                <div class="col-md-9">
                    <input type="password" class="form-control" id="password-in" name="password2" value="">
                </div>
            </div>
        <?php endif; ?>

        <div class="form-group">

            <label for="name-in" class="col-md-3 label-heading"><?php echo lang("ctn_29") ?><span class="required"> *</span></label>
            <div class="col-md-9">
                <input type="text" class="form-control" id="name-in" name="first_name" value="<?php if (isset($member->first_name)) echo $member->first_name ?>">
            </div>
        </div>
        <div class="form-group">

            <label for="name-in" class="col-md-3 label-heading"><?php echo lang("ctn_30") ?><span class="required"> *</span></label>
            <div class="col-md-9">
                <input type="text" class="form-control" id="name-in" name="last_name" value="<?php if (isset($member->last_name)) echo $member->last_name ?>">
            </div>
        </div>
    
        <?php if (isset($member)) : ?>
            <div class="form-group">

                <label for="name-in" class="col-md-3 label-heading"><?php echo lang("ctn_36") ?></label>
                <div class="col-md-9">
                    <?php echo lang("ctn_37") ?> : <?php echo $member->IP ?> <br />
                    <?php echo lang("ctn_38") ?> : <?php echo date($this->settings->info->date_format, $member->joined) ?><br />
                    <?php echo lang("ctn_39") ?> : <?php echo date($this->settings->info->date_format, $member->online_timestamp) ?>
                </div>
            </div>
        <?php endif; ?>
        <?php if (isset($flags['activate_user']) && $flags['activate_user']) : ?>
            <div class="form-group">

                <label for="name-in" class="col-md-3 label-heading"><?php echo lang("ctn_331") ?></label>
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
        <p><span class="required">*</span><?php echo lang("ctn_351") ?></p>
        <input type="submit" class="btn btn-primary form-control" value="<?php if (isset($flags['new_user'])) : ?>Add<?php else : ?><?php echo lang("ctn_13") ?><?php endif; ?>" />
<?php echo form_close() ?>
    </div>
</div>
</div>