<div class="white-area-content">
<div class="db-header clearfix">
    <div class="page-header-title"> <span class="glyphicon glyphicon-cog"></span> <?php echo lang("ctn_1329") ?></div>
    <div class="db-header-extra"> <a href="<?php echo site_url("user_settings/change_password") ?>" class="btn btn-primary btn-sm"><?php echo lang("ctn_225") ?></a> <a href="<?php echo site_url("user_settings/paying_account") ?>" class="btn btn-info btn-sm"><?php echo lang("ctn_649") ?></a>
</div>
</div>

<ol class="breadcrumb">
  <li><a href="<?php echo site_url() ?>"><?php echo lang("ctn_2") ?></a></li>
  <li><a href="<?php echo site_url("user_settings") ?>"><?php echo lang("ctn_156") ?></a></li>
  <li class="active">Paying Account</li>
</ol>

<p>This is your personal Paying Account used for Invoices.</p>

<hr>

<div class="panel panel-default">
  <div class="panel-body">
  <?php echo form_open(site_url("user_settings/paying_account_pro"), array("class" => "form-horizontal")) ?>
       <div class="form-group ui-front">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_663") ?></label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="name" value="<?php echo $account->name ?>">
                    </div>
            </div>
            <div class="form-group ui-front">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_29") ?></label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="first_name" value="<?php echo $account->first_name ?>">
                    </div>
            </div>
            <div class="form-group ui-front">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_30") ?></label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="last_name" value="<?php echo $account->last_name ?>">
                    </div>
            </div>
            <div class="form-group ui-front">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_24") ?></label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="email" value="<?php echo $account->email ?>">
                    </div>
            </div>
            <h4><?php echo lang("ctn_1333") ?></h4>
            <div class="form-group ui-front">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_665") ?></label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="paypal_email" value="<?php echo $account->paypal_email ?>">
                    </div>
            </div>
            <div class="form-group ui-front">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_376") ?></label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="stripe_secret_key" value="<?php echo $account->stripe_secret_key ?>">
                    </div>
            </div>
            <div class="form-group ui-front">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_377") ?></label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="stripe_publishable_key" value="<?php echo $account->stripe_publishable_key ?>">
                    </div>
            </div>
            <div class="form-group ui-front">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_666") ?></label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="checkout2_account_number" value="<?php echo $account->checkout2_account_number ?>">
                    </div>
            </div>
            <div class="form-group ui-front">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_667") ?></label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="checkout2_secret_key" value="<?php echo $account->checkout2_secret_key ?>">
                    </div>
            </div>
            <h4><?php echo lang("ctn_1116") ?></h4>
            <div class="form-group ui-front">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_420") ?></label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="address_line_1" value="<?php echo $account->address_line_1 ?>">
                    </div>
            </div>
            <div class="form-group ui-front">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_421") ?></label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="address_line_2" value="<?php echo $account->address_line_2 ?>">
                    </div>
            </div>
            <div class="form-group ui-front">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_422") ?></label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="city" value="<?php echo $account->city ?>">
                    </div>
            </div>
            <div class="form-group ui-front">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_423") ?></label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="state" value="<?php echo $account->state ?>">
                    </div>
            </div>
            <div class="form-group ui-front">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_424") ?></label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="zip" value="<?php echo $account->zip ?>">
                    </div>
            </div>
            <div class="form-group ui-front">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_425") ?></label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="country" value="<?php echo $account->country ?>">
                    </div>
            </div>
     <input type="submit" name="s" value="<?php echo lang("ctn_686") ?>" class="btn btn-primary form-control" />
  <?php echo form_close() ?>
  </div>
  </div>

</div>