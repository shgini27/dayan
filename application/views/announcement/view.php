<div class="white-area-content">

<div class="db-header clearfix">
    <div class="page-header-title"> <span class="glyphicon glyphicon-bullhorn"></span> <?php echo lang("ctn_447") ?></div>
    <div class="db-header-extra form-inline"> 

    <a href="<?php echo site_url("announcements") ?>" class="btn btn-primary btn-sm"><?php echo lang("ctn_447") ?></a>

</div>
</div>


<div class="panel panel-default">
<div class="panel-heading"><?php echo $announcement->title ?></div>
<div class="panel-body">
<?php echo $announcement->announcement ?>
<hr>
<?php echo $this->common->get_user_display(array("username" => $announcement->username, "avatar" => $announcement->avatar, "online_timestamp" => $announcement->online_timestamp, "first_name" => $announcement->first_name, "last_name" => $announcement->last_name)) ?> <br /><br />
<p class="small-text"><?php echo date($this->settings->info->date_format, $announcement->timestamp) ?></p>
</div>
</div>




</div>