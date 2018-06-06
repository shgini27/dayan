<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?php echo $this->settings->info->site_name ?></title>         
        <meta charset="UTF-8" />
        <link href="<?php echo base_url();?>styles/report.css" rel="stylesheet" type="text/css">
               

        <!-- CODE INCLUDES -->
    </head>
    <body>
    <div class="document">
    <div class="wrapper">

    <div class="part-1">
        <div class="pull-left">
        <img src="<?php echo base_url() ?><?php echo $this->settings->info->upload_path_relative ?>/<?php echo $student->avatar ?>">
        
        </div>
    </div>
    <div class="part-2-right">
        <h2><?php echo $student->first_name ?> <?php echo $student->last_name ?></h2>
        <p class="small-text">@<?php echo $student->username ?> | <?php echo $this->settings->info->site_name ?></p>
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

    <hr>

    <h3>Grades</h3>

    <table class="table table-bordered small-text">
<tr class="table-header"><td><?php echo lang("ctn_532") ?></td><td><?php echo lang("ctn_467") ?></td><td><?php echo lang("ctn_494") ?></td><td><?php echo lang("ctn_531") ?></td></tr>
<?php foreach($classes->result() as $r) : ?>
<tr><td><?php echo $r->name ?></td>
<td>
<?php $teachers = $this->classes_model->get_class_teachers_all($r->ID) ?>
<?php foreach($teachers->result() as $rr) : ?>
<?php echo $rr->first_name ?> <?php echo $rr->last_name ?>
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
        N/A
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

<hr>

<h3><?php echo lang("ctn_845") ?></h3>
<?php if($report->num_rows() > 0) : ?>
    <?php $report = $report->row(); ?>
<?php echo $report->notes ?>

<p class="small-text"><?php echo lang("ctn_871") ?> <?php echo date($this->settings->info->date_format, $report->timestamp) ?></p>
<?php endif; ?>

    </div>
    </div>
</body>
</html>