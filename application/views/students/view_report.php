<div class="white-area-content">

<div class="db-header clearfix">
    <div class="page-header-title"> <span class="glyphicon glyphicon-education"></span> <?php echo lang("ctn_481") ?></div>
    <div class="db-header-extra form-inline"> 

<a href="<?php echo site_url("students/view/" . $student->ID) ?>" class="btn btn-primary btn-sm"><?php echo lang("ctn_456") ?></a> <a href="<?php echo site_url("students/view_classes/" . $student->ID) ?>" class="btn btn-info btn-sm"><?php echo lang("ctn_471") ?></a> <a href="<?php echo site_url("students/view_attendance/" . $student->ID) ?>" class="btn btn-success btn-sm"><?php echo lang("ctn_494") ?></a> <a href="<?php echo site_url("students/view_behaviour/" . $student->ID) ?>" class="btn btn-danger btn-sm"><?php echo lang("ctn_455") ?></a> <a href="<?php echo site_url("students/view_report/" . $student->ID) ?>" class="btn btn-info btn-sm"><?php echo lang("ctn_845") ?></a>

</div>
</div>


</div>

<div class="row">
<div class="col-md-3">

<div class="white-area-content content-separator align-center">
<p><img src="<?php echo base_url() ?><?php echo $this->settings->info->upload_path_relative ?>/<?php echo $student->avatar ?>"></p>
<p><?php echo $student->first_name ?> <?php echo $student->last_name ?></p>
<p class="small-text">@<?php echo $student->username ?></p>
<table class="table table-bordered small-text">
		<?php foreach($fields->result() as $r) : ?>
			<?php if($r->type == 1) : ?>
				<tr><td><?php echo $r->name ?><br /><strong><?php echo $r->value ?></strong></td></tr>
			<?php elseif($r->type == 5) : ?>
				<tr><td><?php echo $r->name ?><br /><strong><?php echo date($this->settings->info->date_picker_format, $r->value) ?></strong></td></tr>
			<?php else : ?>
				<tr><td><?php echo $r->name ?> <span class="profile-info-content"><?php echo $r->value ?></span></td></tr>
			<?php endif; ?>
		<?php endforeach; ?>
		<tr><td><?php echo lang("ctn_869") ?><br /><strong><?php echo date($this->settings->info->date_format) ?></strong></td></tr>
		</table>
</div>

</div>
<div class="col-md-9">

<div class="white-area-content content-separator">

<div class="db-header clearfix">
    <div class="page-header-title"> <?php echo $student->first_name ?> <?php echo $student->last_name ?>'s <?php echo lang("ctn_845") ?></div>
    <div class="db-header-extra form-inline"> <a href="<?php echo site_url("students/report_pdf/" . $student->ID) ?>" data-toggle="tooltip" data-placement="bottom" title="<?php echo lang("ctn_872") ?>" class="btn btn-success btn-sm"><span class="glyphicon glyphicon-save"></span></a>

</div>
</div>

<table class="table table-bordered small-text">
<tr class="table-header"><td><?php echo lang("ctn_532") ?></td><td><?php echo lang("ctn_467") ?></td><td><?php echo lang("ctn_494") ?></td><td><?php echo lang("ctn_531") ?></td></tr>
<?php foreach($classes->result() as $r) : ?>
<tr><td><a href="<?php echo site_url("classes/view/" . $r->ID) ?>"><?php echo $r->name ?></a></td>
<td>
<?php $teachers = $this->classes_model->get_class_teachers_all($r->ID) ?>
<?php foreach($teachers->result() as $rr) : ?>
<a href="<?php echo site_url("profile/" . $rr->username) ?>"><?php echo $rr->first_name ?> <?php echo $rr->last_name ?></a>
<?php endforeach; ?>
</td>
<td><?php $att = $this->students_model->get_student_attendance_for_class($student->ID, $r->ID); ?>
<?php if($att->num_rows() > 0) : ?>
<?php
$att = $att->row();
$total = $att->present + $att->absent + $att->late + $att->holiday;

			$present_p = @intval(($att->present/$total) * 100);
			$absent_p = @intval(($att->absent/$total) * 100);
			$late_p = @intval(($att->late/$total) * 100);
			$holiday_p = @intval(($att->holiday/$total) * 100);

			echo $present_p . "%";
?>

	<?php else : ?>
		<?php echo lang("ctn_870") ?>
	<?php endif; ?>
</td><td>
<?php

// Get all assignments
$assignments = $this->classes_model->get_user_assignments_class_all($student->ID, $r->ID);

$grades = $this->classes_model->get_class_grades_all($r->ID);
$grades_arr = array();
foreach($grades->result() as $r) {
	$grades_arr[] = array(
		"min_score" => $r->min_score,
		"max_score" => $r->max_score,
		"grade" => $r->grade
	);
}
$total_score = 0;

foreach($assignments->result() as $ass) {
	// Means they haven't taken this exam yet.
	if(!isset($ass->mark)) {
		$ass->mark = $ass->max_mark;
	}


	if($ass->max_mark > 0) {
		$score = intval($ass->mark / $ass->max_mark * $ass->weighting);
	} else {
		$score = 0;
	}

	$total_score += $score;
}
$user_grade = lang("ctn_870");
// Get grade
foreach($grades_arr as $grade) {
	if($total_score >= $grade['min_score'] && $total_score <= $grade['max_score']) {
		$user_grade = $grade['grade'];
	}
}

echo $user_grade;

?>
</td></tr>
<?php endforeach; ?>
</table>


</div>

<div class="white-area-content content-separator">

<div class="db-header clearfix">
    <div class="page-header-title"><?php echo lang("ctn_845") ?></div>
    <div class="db-header-extra form-inline"> 

<?php if($this->common->has_permissions(array("admin", "student_manager"), $this->user)) : ?>
<input type="button" class="btn btn-warning btn-sm" value="<?php echo lang("ctn_55") ?>" data-toggle="modal" data-target="#reportModal">
<?php endif; ?>

</div>
</div>

<?php if($report->num_rows() > 0) : ?>
	<?php $report = $report->row(); ?>
<?php echo $report->notes ?>

<p class="small-text"><?php echo lang("ctn_871") ?> <?php echo date($this->settings->info->date_format, $report->timestamp) ?></p>
<?php endif; ?>
</div>

</div>
</div>

<div class="modal fade" id="reportModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-send"></span> <?php echo lang("ctn_845") ?></h4>
      </div>
      <div class="modal-body">
         <?php echo form_open(site_url("students/edit_report/" . $student->ID), array("class" => "form-horizontal")) ?>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_845") ?></label>
                    <div class="col-md-8">
                        <textarea name="report" id="report-area"><?php if(isset($report->notes)) echo $report->notes ?></textarea>
                    </div>
            </div>
            
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo lang("ctn_60") ?></button>
        <input type="submit" class="btn btn-primary" value="<?php echo lang("ctn_13") ?>">
        <?php echo form_close() ?>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">

  CKEDITOR.replace('report-area', { height: '150'});

  </script>