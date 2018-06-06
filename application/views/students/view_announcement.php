<div class="white-area-content">

<div class="db-header clearfix">
    <div class="page-header-title"> <span class="glyphicon glyphicon-education"></span> <?php echo lang("ctn_758") ?></div>
    <div class="db-header-extra form-inline"> 

 

</div>
</div>

<div class="panel panel-default">
<div class="panel-heading"><?php echo $announcement->title ?></div>
<div class="panel-body">
<?php echo $announcement->body ?>
<hr>
<?php echo $this->common->get_user_display(array("username" => $announcement->username, "avatar" => $announcement->avatar, "online_timestamp" => $announcement->online_timestamp, "first_name" => $announcement->first_name, "last_name" => $announcement->last_name)) ?> <br /><br />
<p class="small-text"><?php echo date($this->settings->info->date_format, $announcement->timestamp) ?> - <a href="<?php echo site_url("students/view_group/" . $announcement->groupid) ?>"><?php echo $announcement->name ?></a></p>
</div>
</div>

<div class="panel panel-default">
<div class="panel-body">
<h4><?php echo lang("ctn_575") ?></h4>
<?php foreach($users->result() as $r) : ?>
<?php echo $this->common->get_user_display(array("username" => $r->username, "avatar" => $r->avatar, "online_timestamp" => $r->online_timestamp, "first_name" => $r->first_name, "last_name" => $r->last_name)) ?> 
<?php endforeach; ?>
</div>
</div>

</div>