<div class="white-area-content">
<div class="db-header clearfix">
    <div class="page-header-title"> <span class="glyphicon glyphicon-user"></span> <?php echo lang("ctn_1") ?></div>
    <div class="db-header-extra"> 
</div>
</div>

<ol class="breadcrumb">
  <li><a href="<?php echo site_url() ?>"><?php echo lang("ctn_2") ?></a></li>
  <li><a href="<?php echo site_url("admin") ?>"><?php echo lang("ctn_1") ?></a></li>
  <li><a href="<?php echo site_url("admin/tools") ?>">Tools</a></li>
  <li class="active">Email Debugger</li>
</ol>
<!--
<p>This tool can be used to debug email sending. Your email settings can be found in: application/config/email.php. For more information on what options are available, check out this CodeIgniter User Guide: <a href="https://www.codeigniter.com/user_guide/libraries/email.html">https://www.codeigniter.com/user_guide/libraries/email.html</a></p>

<hr>


<p>This tool will output all email error and debug messages after attempting to send an email. Enter an email you'd like to send a test message to and then click send.</p>
-->
<?php if(!empty($debug)) : ?>
	<hr>
<strong>DEBUG OUTPUT</strong><br />
<pre><?php echo $debug ?></pre>
<hr>
<?php endif; ?>

<div class="panel panel-default">
<div class="panel-body">
<?php echo form_open(site_url("admin/tool_email_debug"), array("class" => "form-horizontal")) ?>
<div class="form-group">
    <label for="dpname-in" class="col-sm-2 control-label">Send Email To</label>
    <div class="col-sm-10">
        <input type="text" name="email" class="form-control" placeholder="example@example.com">
    </div>
</div>
<input type="submit" class="btn btn-primary btn-xs form-control" value="Send Email and Debug">
<?php echo form_close() ?>

</div>
</div>
</div>