<?php include("class_header.php") ?>
<div class="white-area-content">

<div class="db-header clearfix">
    <div class="page-header-title"> <span class="glyphicon glyphicon-bell"></span> <?php echo lang("ctn_507") ?></div>
    <div class="db-header-extra form-inline"> 


</div>
</div>

</div>


<div class="white-area-content content-separator">

<div class="panel panel-default">
<div class="panel-heading"><?php echo $assignment->title ?> <?php if($this->common->has_permissions(array("admin", "class_manager"), $this->user) || ($this->settings->info->teacher_class_manage && $teacher_flag)) : ?>
<span class="pull-right">
<a href="<?php echo site_url("classes/view_assignment_submissions/" . $assignment->ID) ?>" class="btn btn-info btn-xs"><?php echo lang("ctn_553") ?></a> <a href="<?php echo site_url("classes/edit_assignment/" . $assignment->ID) ?>" class="btn btn-warning btn-xs" data-toggle="tooltip" data-placement="bottom" title="<?php echo lang("ctn_55") ?>"><span class="glyphicon glyphicon-cog"></span></a> <a href="<?php echo site_url("classes/delete_assignment/" . $assignment->ID . "/" . $this->security->get_csrf_hash()) ?>" class="btn btn-danger btn-xs" onclick="return confirm(' <?php echo lang("ctn_317") ?>')" data-toggle="tooltip" data-placement="bottom" title="<?php echo lang("ctn_57") ?>"><span class="glyphicon glyphicon-trash"></span></a>
</span>
<?php endif; ?></div>
<div class="panel-body">
<?php echo $assignment->body ?>
<hr>
<?php echo $this->common->get_user_display(array("username" => $assignment->username, "avatar" => $assignment->avatar, "online_timestamp" => $assignment->online_timestamp, "first_name" => $assignment->first_name, "last_name" => $assignment->last_name)) ?> <br /><br />
<p class="small-text"><?php echo date($this->settings->info->date_format, $assignment->timestamp) ?> - <a href="<?php echo site_url("classes/view/" . $assignment->classid) ?>"><?php echo $assignment->name ?></a>
</p>
<hr>
<?php if($assignment->weighting > 0) : ?>
<p><?php echo lang("ctn_848") ?> <strong><?php echo $assignment->weighting ?>%</strong> <?php echo lang("ctn_849") ?>.</p>
<?php endif; ?>
<?php if($upload->num_rows() > 0) : ?>
	<?php $uploadr = $upload->row(); ?>
<?php if(!empty($uploadr->mark)) : ?>
<p><?php echo lang("ctn_580") ?> <strong><?php echo $uploadr->mark ?></strong> / <?php  echo $assignment->max_mark ?> ( <?php echo $grade ?> )</p>
<?php endif; ?>
<?php endif; ?>
<?php if($assignment->type == 0) : ?>
<p><?php echo lang("ctn_578") ?> <strong><?php echo date($this->settings->info->date_format, $assignment->due_date) ?></strong></p>
<hr>
<h4><?php echo lang("ctn_579") ?></h4>
<?php if($assignment->deny_upload && $assignment->due_date < time()) : ?>
<p><?php echo lang("ctn_581") ?></p>
<?php else : ?>
<?php if(!$assignment->reupload && $upload->num_rows() > 0) : ?>
<p><?php echo lang("ctn_582") ?></p>
<?php else : ?>
<?php if($upload->num_rows() > 0) : ?>
<p><strong><?php echo lang("ctn_583") ?></strong> <?php echo lang("ctn_584") ?></p>
<?php endif; ?>
<?php echo form_open_multipart(site_url("classes/upload_assignment/" . $assignment->ID), array("class" => "form-horizontal")) ?>
            <div class="form-group">
			    <label for="inputEmail3" class="col-sm-2 control-label"><?php echo lang("ctn_585") ?></label>
			    <div class="col-sm-10">
			      <input type="file" class="form-control" name="userfile" value="<?php echo lang("ctn_586") ?>">
			    </div>
			</div>
			<div class="form-group">
			    <label for="inputEmail3" class="col-sm-2 control-label"><?php echo lang("ctn_495") ?></label>
			    <div class="col-sm-10">
			      <textarea name="notes" id="notes-area"></textarea>
			    </div>
			</div>
			 <input type="submit" name="s" value="<?php echo lang("ctn_587") ?>" class="btn btn-primary form-control" />
    <?php echo form_close() ?>
<?php endif; ?>
<?php endif; ?>
<?php else : ?>
<?php if($upload->num_rows() > 0) : ?>
	<?php $uploadr = $upload->row(); ?>
<?php if(!empty($uploadr->grade)) : ?>
<p><?php echo lang("ctn_580") ?> <strong><?php echo $uploadr->grade ?></strong></p>
<?php endif; ?>
<?php endif; ?>
<?php endif; ?>
</div>
</div>



</div>
<script type="text/javascript">
$(document).ready(function() {

  CKEDITOR.replace('notes-area', { height: '150'});

  });
</script>