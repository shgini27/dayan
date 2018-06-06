<script src="<?php echo base_url();?>scripts/custom/get_usernames.js"></script>
<div id="loading_spinner_mail">
      <span class="glyphicon glyphicon-refresh" id="ajspinner_mail"></span>
</div>

<div class="mail-header">
<?php echo lang("ctn_747") ?>
</div>
<?php echo form_open(site_url("mail/compose_pro/")) ?>
<div class="mail-reply clearfix">
<div class="row">
<div class="col-md-12">
      <input type="text" class="form-control" placeholder="<?php echo lang("ctn_476") ?>" name="title">
</div><!-- /.col-lg-6 -->
<hr>
</div>
<div class="row">
  <div class="col-md-12">
      <input type="text" class="form-control" placeholder="<?php echo lang("ctn_493") ?>" name="username" id="username-search" <?php if(isset($_GET['username'])) : ?>value="<?php echo $this->common->nohtml($_GET['username']) ?>"<?php endif; ?>>
  </div><!-- /.col-lg-6 -->
</div><!-- /.row -->
</div>

<div class="mail-reply-textbox">
<textarea name="reply" rows="5" id="mail-reply-textarea"></textarea>
<p class="mail-reply-button"><input type="submit" name="s" value="<?php echo lang("ctn_748") ?>" class="btn btn-primary form-control"></p>
<?php echo form_close() ?>
</div>