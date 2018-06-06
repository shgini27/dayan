<div class="white-area-content">

<div class="db-header clearfix">
    <div class="page-header-title"> <span class="glyphicon glyphicon-file"></span> <?php echo lang("ctn_596") ?></div>
    <div class="db-header-extra"> 
</div>
</div>

<div class="panel panel-default">
<div class="panel-body">

<?php
	$url = base_url().$this->settings->info->upload_path_relative.'/'.$file->upload_file_name;
				if(!empty($file->file_url)) {
					$url = $file->file_url;
				}
?>

<table class="table table-bordered table-striped table-hover">
<tr class="table-header"><td><?php echo lang("ctn_603") ?></td><td><?php echo lang("ctn_604") ?></td></tr>
<tr><td><?php echo lang("ctn_597") ?></td><td><?php echo $file->file_name ?></td></tr>
<tr><td><?php echo lang("ctn_599") ?></td><td><a href="<?php echo $url ?>"><?php echo $url ?></a></td></tr>
<tr><td><?php echo lang("ctn_528") ?></td><td><?php echo $file->file_type ?></td></tr>
<tr><td><?php echo lang("ctn_107") ?></td><td><?php echo $file->file_size ?> kb</td></tr>
<tr><td><?php echo lang("ctn_529") ?></td><td><?php echo date($this->settings->info->date_format, $file->timestamp) ?></td></tr>
<tr><td><?php echo lang("ctn_605") ?></td><td><?php echo $this->common->get_user_display(array("username" => $file->username, "avatar" => $file->avatar, "online_timestamp" => $file->online_timestamp, "first_name" => $file->first_name, "last_name" => $file->last_name)) ?></td></tr>
</table>

<hr>
<h4><?php echo lang("ctn_601") ?></h4>
<?php echo $file->notes ?>

</div>
</div>
</div>