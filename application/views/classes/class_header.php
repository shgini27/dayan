<nav class="navbar navbar-default navbar-darktext">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand navbar-darktext" href="<?php echo site_url("classes/view/" . $class->ID) ?>">
        <?php echo $class->name ?>
      </a>
    </div>

    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
    	<?php if( (isset($class) && isset($member_flag) ) && $class->allow_signups && !$member_flag && $this->common->has_permissions(array("admin", "student"), $this->user)) : ?>
		<a href="<?php echo site_url("classes/sign_up/" . $class->ID . "/" . $this->security->get_csrf_hash()) ?>" class="btn btn-info btn-sm navbar-btn" onclick="return confirm('<?php echo lang("ctn_543") ?>')"><?php echo lang("ctn_544") ?></a>
		<?php endif; ?>
		<?php if($this->settings->info->teacher_class) : ?>
		  <?php if($this->common->has_permissions(array("admin", "class_manager", "teacher"), $this->user)) :?>
		  <a href="<?php echo site_url("classes/class_students/" . $class->ID) ?>" class="btn btn-success btn-sm navbar-btn"><?php echo lang("ctn_481") ?></a>
		  <?php endif; ?>
		<?php else : ?>
		<a href="<?php echo site_url("classes/class_students/" . $class->ID) ?>" class="btn btn-success btn-sm navbar-btn"><?php echo lang("ctn_481") ?></a>
		<?php endif; ?>
    	<a href="<?php echo site_url("classes/grades/" . $class->ID) ?>" class="btn btn-warning btn-sm navbar-btn"><?php echo lang("ctn_859") ?></a>
    	<a href="<?php echo site_url("classes/view_assignments/" . $class->ID) ?>" class="btn btn-primary btn-sm navbar-btn"><?php echo lang("ctn_546") ?></a>
    	<a href="<?php echo site_url("classes/timetable/" . $class->ID) ?>" class="btn btn-info btn-sm navbar-btn"><?php echo lang("ctn_547") ?></a>

		<?php if($this->common->has_permissions(array("admin", "class_manager"), $this->user) || (isset($teacher_flag) && $this->settings->info->teacher_class_manage && $teacher_flag)) : ?>
		  <a href="<?php echo site_url("classes/attendance/" . $class->ID) ?>" class="btn btn-success btn-sm navbar-btn"><?php echo lang("ctn_494") ?></a>
		<?php endif; ?>
    </div>
  </div>
</nav>
