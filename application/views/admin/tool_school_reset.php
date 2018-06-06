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
  <li class="active">School Reset</li>
</ol>

<p>This function will remove data from the school system.</p>


<hr>


<?php if(!empty($debug)) : ?>
	<hr>
<strong>Debug Output</strong><br />
<pre><?php echo $debug ?></pre>
<hr>
<?php endif; ?>

<div class="panel panel-default">
<div class="panel-body">
<?php echo form_open(site_url("admin/school_reset"), array("class" => "form-horizontal")) ?>
<ul>
	<li>Clear All Assignments <input type="checkbox" name="remove_assignments" value="1" checked></li>
	<li>Clear All Attendance Records <input type="checkbox" name="remove_attendance" value="1" checked></li>
	<li>Remove all students from all classes <input type="checkbox" name="remove_students" value="1" checked></li>
	<li>Clear All Timetable Entries <input type="checkbox" name="remove_timetable" value="1" checked></li>
	<li>Clear All Class Announcements <input type="checkbox" name="remove_class_announcements" value="1" checked></li>
	<li>Remove Classes <input type="checkbox" name="remove_class" value="1"></li>
	<li>Remove Subjects <input type="checkbox" name="remove_subjects" value="1"></li>
</ul>
<input type="submit" name="s" class="btn btn-primary btn-xs form-control" value="Reset School">
<?php echo form_close() ?>

</div>
</div>
</div>