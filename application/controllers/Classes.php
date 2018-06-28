<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require APPPATH . "third_party/vendor/autoload.php";

use PhpOffice\PhpWord\Element\Section;

class Classes extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model("user_model");
        $this->load->model("classes_model");
        $this->load->model("subjects_model");
        $this->load->model("students_model");
        $this->load->model("library_model");

        if (!$this->user->loggedin) {
            redirect(site_url("login"));
        }

        // If the user does not have premium. 
        // -1 means they have unlimited premium
        if ($this->settings->info->global_premium &&
                ($this->user->info->premium_time != -1 &&
                $this->user->info->premium_time < time())) {
            $this->session->set_flashdata("globalmsg", lang("success_29"));
            redirect(site_url("funds/plans"));
        }

        $this->template->loadData("activeLink", array("classes" => array("general" => 1)));

        if (!$this->common->has_permissions(array("admin", "class_manager",
                    "class_viewer"), $this->user)) {
            $this->template->error(lang("error_2"));
        }

        if (!$this->settings->info->classes_section) {
            $this->template->error(lang("error_84"));
        }
    }

    public function index() {

        $this->template->loadData("activeLink", array("classes" => array("general" => 1)));

        $this->template->loadContent("classes/index.php", array(
            "page" => "index"
                )
        );
    }

    public function your() {
        $this->template->loadData("activeLink", array("classes" => array("your" => 1)));
        $this->template->loadContent("classes/index.php", array(
            "page" => "your"
                )
        );
    }

    public function class_page($page) {
        $this->load->library("datatables");

        $this->datatables->set_default_order("classes.ID", "asc");

        // Set page ordering options that can be used
        $this->datatables->ordering(
                array(
                    0 => array(
                        "classes.name" => 0
                    ),
                    1 => array(
                        "subjects.name" => 0
                    ),
                    2 => array(
                        "class_categories.name" => 0
                    ),
                    3 => array(
                        "classes.students" => 0
                    )
                )
        );

        if ($page == "index") {
            $this->datatables->set_total_rows(
                    $this->classes_model
                            ->get_classes_total()
            );
            $classes = $this->classes_model->get_classes($this->datatables);
        } elseif ($page == "your") {
            $this->datatables->set_total_rows(
                    $this->classes_model
                            ->get_classes_total_your($this->user->info->ID)
            );
            $classes = $this->classes_model->get_classes_your($this->user->info->ID, $this->datatables);
        }


        foreach ($classes->result() as $r) {

            $max = $r->students;
            if ($r->max_students > 0) {
                $max = $max . "/" . $r->max_students;
            }

            $options = '<a href="' . site_url("classes/view/" . $r->ID) . '" class="btn btn-primary btn-xs">' . lang("ctn_552") . '</a>';
            $options .= ' <a href="' . site_url("documents/download_attendance/" . $r->ID) . '" class="btn btn-success btn-xs" data-toggle="tooltip" data-placement="bottom" title="' . lang("ctn_494") . '"><span class="glyphicon glyphicon-download"></span></a>';
            if ($this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $options .= ' <a href="' . site_url("classes/edit_class/" . $r->ID) . '" class="btn btn-warning btn-xs" data-toggle="tooltip" data-placement="bottom" title="' . lang("ctn_55") . '"><span class="glyphicon glyphicon-cog"></span></a> <a href="' . site_url("classes/delete_class/" . $r->ID . "/" . $this->security->get_csrf_hash()) . '" class="btn btn-danger btn-xs" onclick="return confirm(\'' . lang("ctn_317") . '\')" data-toggle="tooltip" data-placement="bottom" title="' . lang("ctn_57") . '"><span class="glyphicon glyphicon-trash"></span></a>';
            }

            $today = new DateTime(); // Today
            $contractDateBegin = new DateTime($r->cat_start_date);
            $contractDateEnd = new DateTime($r->cat_end_date);

            if (
                    $today->getTimestamp() > $contractDateBegin->getTimestamp() &&
                    $today->getTimestamp() < $contractDateEnd->getTimestamp()) {

                $style = 'style="color: greenyellow;"';
            } elseif ($today->getTimestamp() < $contractDateBegin->getTimestamp()) {
                $style = 'style="color: yellow;"';
            } else {
                $style = 'style="color: red;"';
            }

            $week_days = "";
            switch (trim($r->class_days)) {
                case 'odd':
                    $week_days = "1,3,5";
                    break;
                case 'even':
                    $week_days = "2,4,6";
                    break;
                case 'everyday':
                    $week_days = "1-6";
                    break;
            }

            $this->datatables->data[] = array(
                '<div class="text-center"><i class="glyphicon glyphicon-stop" ' . $style . '></i></div>',
                $r->name . " / $week_days / " . substr($r->start_hour, 0, 5),
                $r->subject_name,
                $r->hrs,
                $r->cat_name,
                $r->branch_name,
                $r->room_code,
                $max,
                $options
            );
        }

        echo json_encode($this->datatables->process());
    }

    public function delete_class($id, $hash) {
        if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
            $this->template->error(lang("error_2"));
        }
        if ($hash != $this->security->get_csrf_hash()) {
            $this->template->error(lang("error_6"));
        }
        $id = intval($id);
        $class = $this->classes_model->get_class($id);
        if ($class->num_rows() == 0) {
            $this->template->error(lang("error_92"));
        }

        $this->classes_model->delete_class($id);
        $this->classes_model->delete_class_lesson_events($id);
        $this->session->set_flashdata("globalmsg", lang("success_58"));
        redirect(site_url("classes"));
    }

    public function edit_class($id) {
        $id = intval($id);
        $class = $this->classes_model->get_class($id);
        if ($class->num_rows() == 0) {
            $this->template->error(lang("error_92"));
        }

        $class = $class->row();

        if ($this->settings->info->teacher_class_manage) {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $member = $this->classes_model
                        ->get_class_student_user($this->user->info->ID, $id);
                if ($member->num_rows() == 0) {
                    $this->template->error(lang("error_2"));
                }
                $member = $member->row();
                if (!$member->teacher_flag) {
                    $this->template->error(lang("error_2"));
                }
            }
        } else {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $this->template->error(lang("error_2"));
            }
        }

        $categories = $this->classes_model->get_categories();
        $branches = $this->classes_model->get_branches();
        $rooms = $this->classes_model->get_branch_room($class->branch_id);
        $subjects = $this->subjects_model->get_all_subjects();

        $this->template->loadExternal(
                '<link rel="stylesheet" href="' . base_url() . 'scripts/libraries/wickedpicker/dist/wickedpicker.min.css">'
                . '<script src="' . base_url() . 'scripts/libraries/wickedpicker/dist/wickedpicker.min.js"></script>'
        );

        $this->template->loadContent("classes/edit_class.php", array(
            "class" => $class,
            "categories" => $categories,
            "subjects" => $subjects,
            "branches" => $branches,
            "rooms" => $rooms
                )
        );
    }

    public function edit_class_pro($id) {
        $id = intval($id);
        $class = $this->classes_model->get_class($id);
        if ($class->num_rows() == 0) {
            $this->template->error(lang("error_92"));
        }

        $class = $class->row();

        if ($this->settings->info->teacher_class_manage) {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $member = $this->classes_model
                        ->get_class_student_user($this->user->info->ID, $id);
                if ($member->num_rows() == 0) {
                    $this->template->error(lang("error_2"));
                }
                $member = $member->row();
                if (!$member->teacher_flag) {
                    $this->template->error(lang("error_2"));
                }
            }
        } else {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $this->template->error(lang("error_2"));
            }
        }

        $name = $this->common->nohtml($this->input->post("name"));
        $desc = $this->lib_filter->go($this->input->post("description"));
        $content = $this->lib_filter->go($this->input->post("content"));
        $subjectid = intval($this->input->post("subjectid"));
        $categoryid = intval($this->input->post("categoryid"));

        $branch_id = intval($this->input->post("branch_id"));
        $room_id = intval($this->input->post("room_id"));
        $class_days = $this->common->nohtml($this->input->post("class_days"));
        $hrs = $this->common->nohtml($this->input->post("hrs"));
        $start_hour = $this->common->nohtml($this->input->post("start_hour"));
        $end_hour = $this->common->nohtml($this->input->post("end_hour"));

        $allow_signups = intval($this->input->post("allow_signups"));
        $max_students = intval($this->input->post("max_students"));

        $subject = $this->subjects_model->get_subject($subjectid);
        if ($subject->num_rows() == 0) {
            $this->template->error(lang("error_93"));
        }

        $category = $this->classes_model->get_category($categoryid);
        if ($category->num_rows() == 0) {
            $this->template->error(lang("error_94"));
        }

        $branch = $this->classes_model->get_branch($branch_id);
        if ($branch->num_rows() == 0) {
            $this->template->error(lang("error_218"));
        }

        $room = $this->classes_model->get_room($room_id);
        if ($room->num_rows() == 0) {
            $this->template->error(lang("error_223"));
        }

        if (empty($name)) {
            $this->template->error(lang("error_95"));
        }

        if (empty($class_days)) {
            $this->template->error(lang("error_226"));
        }

        if (intval($hrs) === 0) {
            $this->template->error(lang("error_230"));
        }

        if (!empty($start_hour)) {
            $start_hour = DateTime::createFromFormat("H:i:s", str_replace(' ', '', $start_hour) . ":00");
            $start_hour = $start_hour->format("H:i:s");
        } else {
            $this->template->error(lang("error_224"));
        }

        if (!empty($end_hour)) {
            $end_hour = DateTime::createFromFormat("H:i:s", str_replace(' ', '', $end_hour) . ":00");
            $end_hour = $end_hour->format("H:i:s");
        } else {
            $this->template->error(lang("error_225"));
        }

        // Student count
        $count = $this->classes_model->get_student_count($id);

        //Add events
        $cat = $category->row();
        $teachers = $this->classes_model->get_class_teachers2($id);
        $description = 'Teacher(s): ';
        $counter = 1;
        foreach ($teachers->result() as $teacher) {
            $description .= $teacher->username;
            if ($counter !== $teachers->num_rows() && $counter !== 1) {
                $description .= ', ';
            }
            $counter++;
        }

        if (trim($class_days) === 'odd') {
            $days = array(1, 3, 5);
            $color = '1CAAF3';
        } elseif (trim($class_days) === 'even') {
            $days = array(2, 4, 6);
            $color = '26FF2F';
        }

        switch (trim($class_days)) {
            case 'odd':
                $days = array(1, 3, 5);
                $color = '1CAAF3';
                break;
            case 'even':
                $days = array(2, 4, 6);
                $color = '26FF2F';
                break;
            case 'everyday':
                $days = array(1, 2, 3, 4, 5, 6);
                $color = '35FA2F';
                break;
        }

        if (trim($class->class_days) !== trim($class_days) || $class->start_hour !== $start_hour) {

            $room_code = $room->row();
            $this->classes_model->delete_class_lesson_events($id);
            foreach ($days as $day) {
                $dates = $this->getDateForSpecificDayBetweenDates($cat->start_date, $cat->end_date, $day);

                foreach ($dates as $date) {

                    $sd = DateTime::createFromFormat('Y-m-d H:i:s', $date . ' ' . $start_hour);
                    $start_date = $sd->format('Y-m-d H:i:s');

                    $ed = DateTime::createFromFormat('Y-m-d H:i:s', $date . ' ' . $end_hour);
                    $end_date = $ed->format('Y-m-d H:i:s');

                    //check if rooms are available
                    $events = $this->classes_model->get_room_events($start_date, $end_date, $room_code->code, true);

                    if ($events->num_rows() > 0) {
                        $this->template->error(lang("error_200") . " " . lang("ctn_1000") . ": $room_code->code ($start_date --- $end_date)");
                    }

                    $this->classes_model->add_class_event(array(
                        "title" => $name,
                        "description" => $description,
                        "start" => $start_date,
                        "end" => $end_date,
                        "userid" => $this->user->info->ID,
                        "classid" => $id,
                        "room" => $room_code->code,
                        "color" => $color,
                        "lesson_flag" => 1
                            )
                    );
                }
            }
        }

        // update Class
        $this->classes_model->update_class($id, array(
            "name" => $name,
            "description" => $desc,
            "categoryid" => $categoryid,
            "branch_id" => $branch_id,
            "room_id" => $room_id,
            "class_days" => $class_days,
            "hrs" => $hrs,
            "start_hour" => $start_hour,
            "end_hour" => $end_hour,
            "subjectid" => $subjectid,
            "students" => $count,
            "allow_signups" => $allow_signups,
            "max_students" => $max_students,
            "content" => $content
                )
        );

        $this->session->set_flashdata("globalmsg", lang("success_59"));
        redirect(site_url("classes"));
    }

    public function sign_up($id, $hash) {
        if ($hash != $this->security->get_csrf_hash()) {
            $this->template->error(lang("error_6"));
        }

        $id = intval($id);
        $class = $this->classes_model->get_class($id);
        if ($class->num_rows() == 0) {
            $this->template->error(lang("error_92"));
        }

        $class = $class->row();

        if (!$class->allow_signups) {
            $this->template->error(lang("error_96"));
        }

        if ($class->max_students > 0) {
            if ($class->max_students >= $class->students) {
                $this->template->error(lang("error_97"));
            }
        }

        // Check user isn't currently a member
        $member = $this->classes_model
                ->get_class_student_user($this->user->info->ID, $id);
        if ($member->num_rows() > 0) {
            $this->template->error(lang("error_98"));
        }

        // Check user is a student
        if (!$this->common->has_permissions(array("admin", "student"), $this->user)) {
            $this->template->error(lang("error_99"));
        }

        $this->classes_model->add_student(array(
            "classid" => $id,
            "userid" => $this->user->info->ID
                )
        );

        // Recount Class students
        $count = $this->classes_model->get_student_count($id);

        $this->classes_model->update_class($id, array(
            "students" => $count
                )
        );

        $this->session->set_flashdata("globalmsg", lang("success_60"));
        redirect(site_url("classes/view/" . $id));
    }

    public function add() {
        if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
            $this->template->error(lang("error_2"));
        }
        $this->template->loadExternal(
                '<link href="' . base_url() . 'scripts/libraries/chosen/chosen.min.css" rel="stylesheet" type="text/css">
			<script type="text/javascript" src="' . base_url() .
                'scripts/libraries/chosen/chosen.jquery.min.js"></script>'
                . '<link rel="stylesheet" href="' . base_url() . 'scripts/libraries/wickedpicker/dist/wickedpicker.min.css">'
                . '<script src="' . base_url() . 'scripts/libraries/wickedpicker/dist/wickedpicker.min.js"></script>'
        );

        $categories = $this->classes_model->get_categories();
        $branches = $this->classes_model->get_branches();
        $rooms = $this->classes_model->get_rooms();
        $subjects = $this->subjects_model->get_all_subjects();
        $groups = $this->students_model->get_all_groups();

        $students = $this->students_model->get_all_students();
        $teachers = $this->students_model->get_all_teachers();

        $this->template->loadContent("classes/add.php", array(
            "categories" => $categories,
            "branches" => $branches,
            "rooms" => $rooms,
            "subjects" => $subjects,
            "groups" => $groups,
            "students" => $students,
            "teachers" => $teachers
                )
        );
    }

    public function add_class_pro() {
        if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
            $this->template->error(lang("error_2"));
        }
        $name = $this->common->nohtml($this->input->post("name"));
        $desc = $this->lib_filter->go($this->input->post("description"));
        $content = $this->lib_filter->go($this->input->post("content"));
        $subjectid = intval($this->input->post("subjectid"));
        $categoryid = intval($this->input->post("categoryid"));

        $branch_id = intval($this->input->post("branch_id"));
        $room_id = intval($this->input->post("room_id"));
        $class_days = $this->common->nohtml($this->input->post("class_days"));
        $hrs = $this->common->nohtml($this->input->post("hrs"));
        $start_hour = $this->common->nohtml($this->input->post("start_hour"));
        $end_hour = $this->common->nohtml($this->input->post("end_hour"));

        $teachers = $this->input->post("teachers");
        $students = $this->input->post("students");

        $groupid = intval($this->input->post("groupid"));

        $allow_signups = intval($this->input->post("allow_signups"));
        $max_students = intval($this->input->post("max_students"));

        $subject = $this->subjects_model->get_subject($subjectid);
        if ($subject->num_rows() == 0) {
            $this->template->error(lang("error_93"));
        }

        $category = $this->classes_model->get_category($categoryid);
        if ($category->num_rows() == 0) {
            $this->template->error(lang("error_94"));
        }

        $branch = $this->classes_model->get_branch($branch_id);
        if ($branch->num_rows() == 0) {
            $this->template->error(lang("error_218"));
        }

        $room = $this->classes_model->get_room($room_id);
        if ($room->num_rows() == 0) {
            $this->template->error(lang("error_223"));
        }

        if (empty($name)) {
            $this->template->error(lang("error_95"));
        }

        if (empty($class_days)) {
            $this->template->error(lang("error_226"));
        }

        if (intval($hrs) === 0) {
            $this->template->error(lang("error_230"));
        }

        if (!empty($start_hour)) {
            $start_hour = DateTime::createFromFormat("H:i:s", str_replace(' ', '', $start_hour) . ":00");
            $start_hour = $start_hour->format("H:i:s");
        } else {
            $this->template->error(lang("error_224"));
        }

        if (!empty($end_hour)) {
            $end_hour = DateTime::createFromFormat("H:i:s", str_replace(' ', '', $end_hour) . ":00");
            $end_hour = $end_hour->format("H:i:s");
        } else {
            $this->template->error(lang("error_225"));
        }

        $students_toadd = array();
        if ($groupid > 0) {
            $group_students = $this->students_model->get_students_from_group($groupid);
            foreach ($group_students->result() as $r) {
                $students_toadd[] = $r->userid;
            }
        }

        // Check studnets
        if ($students) {
            foreach ($students as $username) {
                $username = $this->common->nohtml($username);
                if (!empty($username)) {
                    $user = $this->user_model->get_user_by_username($username);
                    if ($user->num_rows() == 0) {
                        $this->template->error(lang("error_100") . $username);
                    }
                    $user = $user->row();
                    $students_toadd[] = $user->ID;
                }
            }
        }

        // Check teachers
        $cat = $category->row();

        $teachers_toadd = array();
        $description = '';
        $counter = 1;
        if ($teachers) {
            $description = 'Teacher(s): ';
            foreach ($teachers as $username) {
                $username = $this->common->nohtml($username);
                $flg = false;
                if (!empty($username)) {
                    $user_data = $this->user_model->get_user_by_username($username);
                    if ($user_data->num_rows() == 0) {
                        $this->template->error(lang("error_100") . $username);
                    }

                    $user = $user_data->row();

                    $t = $this->classes_model->check_teachers($user->ID, 0, $cat->end_date, $class_days, $start_hour, $end_hour);

                    if ($t->num_rows() > 0) {
                        $this->template->error($username . ' ' . lang("error_227"));
                    }

                    $teachers_toadd[] = $user->ID;

                    $description .= $user->username;
                    $flg = true;
                }

                if ($counter !== $user_data->num_rows() && $counter !== 1 && $flg) {
                    $description .= ', ';
                }
                $counter++;
            }
        }

        $students_toadd = array_unique($students_toadd);

        //Add events
        switch (trim($class_days)) {
            case 'odd':
                $days = array(1, 3, 5);
                $color = '1CAAF3';
                break;
            case 'even':
                $days = array(2, 4, 6);
                $color = '26FF2F';
                break;
            case 'everyday':
                $days = array(1, 2, 3, 4, 5, 6);
                $color = '35FA2F';
                break;
        }

        $room_code = $room->row();
        foreach ($days as $day) {
            $dates = $this->getDateForSpecificDayBetweenDates($cat->start_date, $cat->end_date, $day);

            foreach ($dates as $date) {

                $sd = DateTime::createFromFormat('Y-m-d H:i:s', $date . ' ' . $start_hour);
                $start_date = $sd->format('Y-m-d H:i:s');

                $ed = DateTime::createFromFormat('Y-m-d H:i:s', $date . ' ' . $end_hour);
                $end_date = $ed->format('Y-m-d H:i:s');

                //check if rooms are available
                $events = $this->classes_model->get_room_events($start_date, $end_date, $room_code->code, true);

                if ($events->num_rows() > 0) {
                    $this->template->error(lang("error_200") . " " . lang("ctn_1000") . ": $room_code->code ($start_date --- $end_date)");
                }
            }
        }

        // Add Class
        $classid = $this->classes_model->add_class(array(
            "name" => $name,
            "description" => $desc,
            "categoryid" => $categoryid,
            "branch_id" => $branch_id,
            "room_id" => $room_id,
            "class_days" => $class_days,
            "hrs" => $hrs,
            "start_hour" => $start_hour,
            "end_hour" => $end_hour,
            "subjectid" => $subjectid,
            "students" => count($students_toadd),
            "allow_signups" => $allow_signups,
            "max_students" => $max_students,
            "content" => $content
                )
        );

        //Add events
        foreach ($days as $day) {
            $dates = $this->getDateForSpecificDayBetweenDates($cat->start_date, $cat->end_date, $day);

            foreach ($dates as $date) {

                $sd = DateTime::createFromFormat('Y-m-d H:i:s', $date . ' ' . $start_hour);
                $start_date = $sd->format('Y-m-d H:i:s');

                $ed = DateTime::createFromFormat('Y-m-d H:i:s', $date . ' ' . $end_hour);
                $end_date = $ed->format('Y-m-d H:i:s');

                $this->classes_model->add_class_event(array(
                    "title" => $name,
                    "description" => $description,
                    "start" => $start_date,
                    "end" => $end_date,
                    "userid" => $this->user->info->ID,
                    "classid" => $classid,
                    "room" => $room_code->code,
                    "color" => $color,
                    "lesson_flag" => 1
                        )
                );
            }
        }

        // Add Students
        foreach ($teachers_toadd as $userid) {
            $this->classes_model->add_student(array(
                "classid" => $classid,
                "userid" => $userid,
                "teacher_flag" => 1
                    )
            );

            $user = $this->user_model->get_user($userid);
            if ($user->num_rows() > 0) {
                $user = $user->row();
                // Send notification
                $this->user_model->increment_field($userid, "noti_count", 1);
                $this->user_model->add_notification(array(
                    "userid" => $userid,
                    "url" => "classes/view/" . $classid,
                    "timestamp" => time(),
                    "message" => lang("ctn_820") . ": <strong>" . $name . "</strong>",
                    "status" => 0,
                    "fromid" => $this->user->info->ID,
                    "email" => $user->email,
                    "username" => $user->username,
                    "email_notification" => $user->email_notification
                        )
                );
            }
        }

        // Add Students
        $brnch = $branch->row();
        $branch_code = $brnch->code;
        foreach ($students_toadd as $userid) {
            $this->classes_model->add_student(array(
                "classid" => $classid,
                "userid" => $userid
                    ), $branch_code
            );

            $user = $this->user_model->get_user($userid);
            if ($user->num_rows() > 0) {
                $user = $user->row();
                // Send notification
                $this->user_model->increment_field($userid, "noti_count", 1);
                $this->user_model->add_notification(array(
                    "userid" => $userid,
                    "url" => "classes/view/" . $classid,
                    "timestamp" => time(),
                    "message" => lang("ctn_820") . ": <strong>" . $name . "</strong>",
                    "status" => 0,
                    "fromid" => $this->user->info->ID,
                    "email" => $user->email,
                    "username" => $user->username,
                    "email_notification" => $user->email_notification
                        )
                );
            }
        }

        $this->session->set_flashdata("globalmsg", lang("success_61"));
        redirect(site_url("classes"));
    }

    /**

     * Method to get dates of specific day between two dates
     * @param type $start_date
     * @param type $end_date
     * @param type $day_number
     * @return array   Array of dates  /
     */
    private function getDateForSpecificDayBetweenDates($start_date, $end_date, $day_number) {
        $date_array = array();
        $end_date = strtotime($end_date);

        $days = array(
            '1' => 'Monday',
            '2' => 'Tuesday',
            '3' => 'Wednesday',
            '4' => 'Thursday',
            '5' => 'Friday',
            '6' => 'Saturday',
            '7' => 'Sunday'
        );
        $i_start = strtotime($days[$day_number], strtotime($start_date));

        for ($i = $i_start; $i <= $end_date; $i = strtotime('+1 week', $i)) {
            $date_array[] = date('Y-m-d', $i);
        }

        return $date_array;
    }

    public function view($id) {
        $id = intval($id);
        $class = $this->classes_model->get_class($id);
        if ($class->num_rows() == 0) {
            $this->template->error(lang("error_92"));
        }

        $class = $class->row();

        $this->template->loadData("activeLink", array("classes" => array("general" => 1)));

        // Check if member
        $member_flag = false;
        $teacher_flag = false;
        $member = $this->classes_model
                ->get_class_student_user($this->user->info->ID, $id);
        if ($member->num_rows() == 0) {
            $member_flag = false;
        } else {
            $member = $member->row();
            if ($member->teacher_flag) {
                $teacher_flag = true;
            }
            $member_flag = true;
        }

        $announcements = $this->classes_model->get_announcements($id);
        $assignments = $this->classes_model->get_assignments($id);
        $books = $this->classes_model->get_reading_books($id);
        $files = $this->classes_model->get_files($id);


        $this->template->loadExternal(
                '<link rel="stylesheet" href="' . base_url() . 'scripts/libraries/bootstrap-datepicker-1.6.4/css/bootstrap-datepicker3.min.css" />
			<script src="' . base_url() . 'scripts/libraries/bootstrap-datepicker-1.6.4/js/bootstrap-datepicker.min.js"></script>'
        );

        $this->template->loadContent("classes/view.php", array(
            "class" => $class,
            "member_flag" => $member_flag,
            "teacher_flag" => $teacher_flag,
            "announcements" => $announcements,
            "assignments" => $assignments,
            "books" => $books,
            "files" => $files
                )
        );
    }

    public function grades($id) {
        $id = intval($id);
        $class = $this->classes_model->get_class($id);
        if ($class->num_rows() == 0) {
            $this->template->error(lang("error_92"));
        }

        $class = $class->row();

        $this->template->loadData("activeLink", array("classes" => array("general" => 1)));

        // Check if member
        $member_flag = false;
        $teacher_flag = false;
        $member = $this->classes_model
                ->get_class_student_user($this->user->info->ID, $id);
        if ($member->num_rows() == 0) {
            $member_flag = false;
        } else {
            $member = $member->row();
            if ($member->teacher_flag) {
                $teacher_flag = true;
            }
            $member_flag = true;
        }

        $this->template->loadContent("classes/grades.php", array(
            "class" => $class,
            "member_flag" => $member_flag,
            "teacher_flag" => $teacher_flag,
                )
        );
    }

    public function grade_page($id) {
        $id = intval($id);
        $class = $this->classes_model->get_class($id);
        if ($class->num_rows() == 0) {
            $this->template->error(lang("error_92"));
        }

        $class = $class->row();

        $this->template->loadData("activeLink", array("classes" => array("general" => 1)));

        // Check if member
        $member_flag = false;
        $teacher_flag = false;
        $member = $this->classes_model
                ->get_class_student_user($this->user->info->ID, $id);
        if ($member->num_rows() == 0) {
            $member_flag = false;
        } else {
            $member = $member->row();
            if ($member->teacher_flag) {
                $teacher_flag = true;
            }
            $member_flag = true;
        }

        $this->load->library("datatables");

        $this->datatables->set_default_order("class_grades.ID", "desc");

        // Set page ordering options that can be used
        $this->datatables->ordering(
                array(
                    0 => array(
                        "class_grades.grade" => 0
                    ),
                    1 => array(
                        "class_grades.min_score" => 0
                    ),
                    2 => array(
                        "class_grades.max_score" => 0
                    ),
                )
        );

        $this->datatables->set_total_rows(
                $this->classes_model
                        ->get_total_class_grades($id)
        );
        $grades = $this->classes_model->get_class_grades($id, $this->datatables);


        $nooptions = false;
        if ($this->settings->info->teacher_class_manage) {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                if (!$teacher_flag) {
                    $nooptions = true;
                }
            }
        } else {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $nooptions = true;
            }
        }

        foreach ($grades->result() as $r) {

            if (!$nooptions) {
                $options = '<a href="' . site_url("classes/edit_grade/" . $r->ID) . '" class="btn btn-warning btn-xs" data-toggle="tooltip" data-placement="bottom" title="' . lang("ctn_55") . '"><span class="glyphicon glyphicon-cog"></span></a> <a href="' . site_url("classes/delete_grade/" . $r->ID . "/" . $this->security->get_csrf_hash()) . '" class="btn btn-danger btn-xs" onclick="return confirm(\'' . lang("ctn_317") . '\')" data-toggle="tooltip" data-placement="bottom" title="' . lang("ctn_57") . '"><span class="glyphicon glyphicon-trash"></span></a>';
            } else {
                $options = "";
            }

            $this->datatables->data[] = array(
                $r->grade,
                $r->min_score,
                $r->max_score,
                $options
            );
        }
        echo json_encode($this->datatables->process());
    }

    public function edit_grade($id) {
        $this->template->loadData("activeLink", array("classes" => array("general" => 1)));
        $id = intval($id);
        $grade = $this->classes_model->get_class_grade($id);
        if ($grade->num_rows() == 0) {
            $this->template->error(lang("error_160"));
        }

        $grade = $grade->row();

        $flags = $this->get_user_flags($grade->classid);

        $teacher_flag = $flags['teacher_flag'];
        $member_flag = $flags['member_flag'];
        if ($this->settings->info->teacher_class_manage) {
            if (!$flags['teacher_flag']) {
                $this->template->error(lang("error_2"));
            }
        } else {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $this->template->error(lang("error_2"));
            }
            $teacher_flag = true;
        }

        $class = $this->classes_model->get_class($grade->classid);
        if ($class->num_rows() == 0) {
            $this->template->error(lang("error_92"));
        }

        $class = $class->row();

        $this->template->loadContent("classes/edit_grade.php", array(
            "grade" => $grade,
            "class" => $class,
            "member_flag" => $member_flag,
            "teacher_flag" => $teacher_flag
                )
        );
    }

    public function edit_grade_pro($id) {
        $id = intval($id);
        $grade = $this->classes_model->get_class_grade($id);
        if ($grade->num_rows() == 0) {
            $this->template->error(lang("error_160"));
        }

        $grade = $grade->row();

        if ($this->settings->info->teacher_class_manage) {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $member = $this->classes_model
                        ->get_class_student_user($this->user->info->ID, $grade->classid);
                if ($member->num_rows() == 0) {
                    $this->template->error(lang("error_2"));
                }
                $member = $member->row();
                if (!$member->teacher_flag) {
                    $this->template->error(lang("error_2"));
                }
            }
        } else {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $this->template->error(lang("error_2"));
            }
        }

        $grade_name = $this->common->nohtml($this->input->post("grade"));
        $min_score = intval($this->input->post("min_score"));
        $max_score = intval($this->input->post("max_score"));

        // Add grade
        $this->classes_model->update_class_grade($id, array(
            "grade" => $grade_name,
            "min_score" => $min_score,
            "max_score" => $max_score
                )
        );

        $this->session->set_flashdata("globalmsg", lang("success_127"));
        redirect(site_url("classes/grades/" . $grade->classid));
    }

    public function delete_grade($id, $hash) {
        if ($hash != $this->security->get_csrf_hash()) {
            $this->template->error(lang("error_6"));
        }
        $id = intval($id);
        $grade = $this->classes_model->get_class_grade($id);
        if ($grade->num_rows() == 0) {
            $this->template->error(lang("error_160"));
        }

        $grade = $grade->row();

        if ($this->settings->info->teacher_class_manage) {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $member = $this->classes_model
                        ->get_class_student_user($this->user->info->ID, $grade->classid);
                if ($member->num_rows() == 0) {
                    $this->template->error(lang("error_2"));
                }
                $member = $member->row();
                if (!$member->teacher_flag) {
                    $this->template->error(lang("error_2"));
                }
            }
        } else {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $this->template->error(lang("error_2"));
            }
        }

        $this->classes_model->delete_class_grade($id);
        $this->session->set_flashdata("globalmsg", lang("success_128"));
        redirect(site_url("classes/grades/" . $grade->classid));
    }

    public function add_grade($id) {
        $id = intval($id);
        $class = $this->classes_model->get_class($id);
        if ($class->num_rows() == 0) {
            $this->template->error(lang("error_92"));
        }

        $class = $class->row();

        $this->template->loadData("activeLink", array("classes" => array("general" => 1)));

        if ($this->settings->info->teacher_class_manage) {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $member = $this->classes_model
                        ->get_class_student_user($this->user->info->ID, $id);
                if ($member->num_rows() == 0) {
                    $this->template->error(lang("error_2"));
                }
                $member = $member->row();
                if (!$member->teacher_flag) {
                    $this->template->error(lang("error_2"));
                }
            }
        } else {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $this->template->error(lang("error_2"));
            }
        }

        $grade = $this->common->nohtml($this->input->post("grade"));
        $min_score = intval($this->input->post("min_score"));
        $max_score = intval($this->input->post("max_score"));

        // Add grade
        $this->classes_model->add_grade(array(
            "classid" => $id,
            "grade" => $grade,
            "min_score" => $min_score,
            "max_score" => $max_score
                )
        );

        $this->session->set_flashdata("globalmsg", lang("success_129"));
        redirect(site_url("classes/grades/" . $id));
    }

    public function delete_book($id, $hash) {
        if ($hash != $this->security->get_csrf_hash()) {
            $this->template->error(lang("error_6"));
        }
        $id = intval($id);
        $book = $this->classes_model->get_reading_book($id);
        if ($book->num_rows() == 0) {
            $this->template->erorr(lang("error_101"));
        }
        $book = $book->row();

        if ($this->settings->info->teacher_class_manage) {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $member = $this->classes_model
                        ->get_class_student_user($this->user->info->ID, $book->classid);
                if ($member->num_rows() == 0) {
                    $this->template->error(lang("error_2"));
                }
                $member = $member->row();
                if (!$member->teacher_flag) {
                    $this->template->error(lang("error_2"));
                }
            }
        } else {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $this->template->error(lang("error_2"));
            }
        }

        $this->classes_model->delete_book($id);
        $this->session->set_flashdata("globalmsg", lang("success_62"));
        redirect(site_url("classes/view/" . $book->classid));
    }

    public function delete_class_file($id, $hash) {
        if ($hash != $this->security->get_csrf_hash()) {
            $this->template->error(lang("error_6"));
        }
        $id = intval($id);
        $file = $this->classes_model->get_class_file($id);
        if ($file->num_rows() == 0) {
            $this->template->erorr(lang("error_102"));
        }
        $file = $file->row();

        if ($this->settings->info->teacher_class_manage) {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $member = $this->classes_model
                        ->get_class_student_user($this->user->info->ID, $file->classid);
                if ($member->num_rows() == 0) {
                    $this->template->error(lang("error_2"));
                }
                $member = $member->row();
                if (!$member->teacher_flag) {
                    $this->template->error(lang("error_2"));
                }
            }
        } else {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $this->template->error(lang("error_2"));
            }
        }

        $this->classes_model->delete_file($id);
        $this->session->set_flashdata("globalmsg", lang("success_63"));
        redirect(site_url("classes/view/" . $file->classid));
    }

    public function view_assignment($id) {
        $id = intval($id);
        $assignment = $this->classes_model->get_assignment($id);
        if ($assignment->num_rows() == 0) {
            $this->template->error(lang("error_103"));
        }
        $assignment = $assignment->row();

        $class = $this->classes_model->get_class($assignment->classid);
        if ($class->num_rows() == 0) {
            $this->template->error(lang("error_92"));
        }

        $class = $class->row();

        // Get user's upload
        $upload = $this->classes_model
                ->get_user_assignment($this->user->info->ID, $id);

        $grades = $this->classes_model->get_class_grades_all($assignment->classid);
        foreach ($grades->result() as $r) {
            $grades_arr[] = array(
                "min_score" => $r->min_score,
                "max_score" => $r->max_score,
                "grade" => $r->grade
            );
        }

        $grade = "N/A";
        if ($upload->num_rows() > 0) {
            $uploadr = $upload->row();
            $grade = $this->calculate_grade($uploadr->mark, $assignment->max_mark, $grades_arr);
        }


        // Check if member
        $member_flag = false;
        $teacher_flag = false;
        $member = $this->classes_model
                ->get_class_student_user($this->user->info->ID, $assignment->classid);
        if ($member->num_rows() == 0) {
            $member_flag = false;
        } else {
            $member = $member->row();
            if ($member->teacher_flag) {
                $teacher_flag = true;
            }
            $member_flag = true;
        }

        $this->template->loadContent("classes/view_assignment.php", array(
            "assignment" => $assignment,
            "upload" => $upload,
            "teacher_flag" => $teacher_flag,
            "member_flag" => $member_flag,
            "class" => $class,
            "grade" => $grade
                )
        );
    }

    public function upload_assignment($id) {
        $id = intval($id);
        $assignment = $this->classes_model->get_assignment($id);
        if ($assignment->num_rows() == 0) {
            $this->template->error(lang("error_103"));
        }
        $assignment = $assignment->row();

        // Check user is a member of this class
        $member = $this->classes_model
                ->get_class_student_user($this->user->info->ID, $assignment->classid);
        if ($member->num_rows() == 0) {
            $this->template->error(lang("error_104"));
        }

        // Get user's upload
        $upload = $this->classes_model
                ->get_user_assignment($this->user->info->ID, $id);

        if (!$assignment->reupload && $upload->num_rows() > 0) {
            $this->template->error(lang("error_105"));
        }

        if ($assignment->deny_upload && $assignment->due_date < time()) {
            $this->template->error(lang("error_106"));
        }

        // Get Details
        $notes = $this->lib_filter->go($this->input->post("notes"));

        $this->load->library("upload");

        if (isset($_FILES['userfile']) && $_FILES['userfile']['size'] > 0) {
            $this->upload->initialize(array(
                "upload_path" => $this->settings->info->upload_path,
                "overwrite" => FALSE,
                "max_filename" => 300,
                "encrypt_name" => TRUE,
                "remove_spaces" => TRUE,
                "allowed_types" => $assignment->file_types,
                "max_size" => $this->settings->info->file_size
            ));

            if (!$this->upload->do_upload()) {
                $this->template->error(lang("error_21")
                        . $this->upload->display_errors());
            }

            $data = $this->upload->data();

            $image = $data['file_name'];
        } else {
            $this->template->error(lang("error_107"));
        }

        // Wipe any previous ones
        if ($upload->num_rows() > 0) {
            $this->classes_model
                    ->delete_assignment_by_user($this->user->info->ID, $id);
        }

        $this->classes_model->add_user_assignment(array(
            "assignmentid" => $id,
            "userid" => $this->user->info->ID,
            "timestamp" => time(),
            "IP" => $_SERVER['REMOTE_ADDR'],
            "file_name" => $data['file_name'],
            "file_size" => $data['file_size'],
            "file_extension" => $data['file_ext'],
            "file_type" => $data['file_type'],
            "notes" => $notes
                )
        );

        $this->session->set_flashdata("globalmsg", lang("success_64"));
        redirect(site_url("classes/view_assignment/" . $id));
    }

    public function add_user_assignment($id) {
        $id = intval($id);
        $assignment = $this->classes_model->get_assignment($id);
        if ($assignment->num_rows() == 0) {
            $this->template->error(lang("error_103"));
        }
        $assignment = $assignment->row();

        if ($this->settings->info->teacher_class_manage) {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $member = $this->classes_model
                        ->get_class_student_user($this->user->info->ID, $assignment->classid);
                if ($member->num_rows() == 0) {
                    $this->template->error(lang("error_2"));
                }
                $member = $member->row();
                if (!$member->teacher_flag) {
                    $this->template->error(lang("error_2"));
                }
            }
        } else {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $this->template->error(lang("error_2"));
            }
        }

        $username = $this->common->nohtml($this->input->post("username"));
        $mark = $this->common->nohtml($this->input->post("mark"));
        $notes = $this->lib_filter->go($this->input->post("notes"));

        $user = $this->user_model->get_user_by_username($username);
        if ($user->num_rows() == 0) {
            $this->template->error(lang("error_87"));
        }
        $user = $user->row();

        $userid = $user->ID;

        $user_role = $this->user_model->get_users_user_role($user->user_role);
        if ($user_role->num_rows() == 0) {
            $this->template->error(lang("error_88"));
            $user_role = $user_role->row();
            if (!$user_role->student) {
                $this->template->error(lang("error_89"));
            }
        }


        $this->load->library("upload");

        if (isset($_FILES['userfile']) && $_FILES['userfile']['size'] > 0) {
            $this->upload->initialize(array(
                "upload_path" => $this->settings->info->upload_path,
                "overwrite" => FALSE,
                "max_filename" => 300,
                "encrypt_name" => TRUE,
                "remove_spaces" => TRUE,
                "allowed_types" => $assignment->file_types,
                "max_size" => $this->settings->info->file_size
            ));

            if (!$this->upload->do_upload()) {
                $this->template->error(lang("error_21")
                        . $this->upload->display_errors());
            }

            $data = $this->upload->data();

            $image = $data['file_name'];
        } else {
            $data = array();
            $data['file_name'] = "";
            $data['file_size'] = 0;
            $data['file_ext'] = "";
            $data['file_type'] = "";
        }



        $this->classes_model->add_user_assignment(array(
            "assignmentid" => $id,
            "userid" => $userid,
            "timestamp" => time(),
            "IP" => $_SERVER['REMOTE_ADDR'],
            "file_name" => $data['file_name'],
            "file_size" => $data['file_size'],
            "file_extension" => $data['file_ext'],
            "file_type" => $data['file_type'],
            "notes" => $notes,
            "mark" => $mark
                )
        );

        $this->session->set_flashdata("globalmsg", lang("success_65"));
        redirect(site_url("classes/view_assignment_submissions/" . $id));
    }

    public function view_assignment_submissions($id) {
        $id = intval($id);
        $assignment = $this->classes_model->get_assignment($id);
        if ($assignment->num_rows() == 0) {
            $this->template->error(lang("error_103"));
        }
        $assignment = $assignment->row();

        $class = $this->classes_model->get_class($assignment->classid);
        if ($class->num_rows() == 0) {
            $this->template->error(lang("error_92"));
        }

        $class = $class->row();

        if ($this->settings->info->teacher_class_manage) {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $member = $this->classes_model
                        ->get_class_student_user($this->user->info->ID, $assignment->classid);
                if ($member->num_rows() == 0) {
                    $this->template->error(lang("error_2"));
                }
                $member = $member->row();
                if (!$member->teacher_flag) {
                    $this->template->error(lang("error_2"));
                }
            }
        } else {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $this->template->error(lang("error_2"));
            }
        }

        $this->template->loadContent("classes/view_user_assignments.php", array(
            "assignment" => $assignment,
            "class" => $class,
            "member_flag" => true,
            "teacher_flag" => true
                )
        );
    }

    public function user_assignment_page($id) {
        $id = intval($id);
        $assignment = $this->classes_model->get_assignment($id);
        if ($assignment->num_rows() == 0) {
            $this->template->error(lang("error_103"));
        }
        $assignment = $assignment->row();

        if ($this->settings->info->teacher_class_manage) {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $member = $this->classes_model
                        ->get_class_student_user($this->user->info->ID, $assignment->classid);
                if ($member->num_rows() == 0) {
                    $this->template->error(lang("error_2"));
                }
                $member = $member->row();
                if (!$member->teacher_flag) {
                    $this->template->error(lang("error_2"));
                }
            }
        } else {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $this->template->error(lang("error_2"));
            }
        }

        $this->load->library("datatables");

        $this->datatables->set_default_order("user_assignments.ID", "desc");

        // Set page ordering options that can be used
        $this->datatables->ordering(
                array(
                    0 => array(
                        "users.username" => 0
                    ),
                    1 => array(
                        "user_assignments.timestamp" => 0
                    ),
                    2 => array(
                        "user_assignments.IP" => 0
                    ),
                    3 => array(
                        "user_assignments.mark" => 0
                    ),
                )
        );

        $grades = $this->classes_model->get_class_grades_all($assignment->classid);
        $grades_arr = array();
        foreach ($grades->result() as $r) {
            $grades_arr[] = array(
                "min_score" => $r->min_score,
                "max_score" => $r->max_score,
                "grade" => $r->grade
            );
        }


        $this->datatables->set_total_rows(
                $this->classes_model
                        ->get_total_user_assignments($id)
        );
        $assign = $this->classes_model->get_user_assignments_dt($id, $this->datatables);

        foreach ($assign->result() as $r) {

            $user_grade = $this->calculate_grade($r->mark, $r->max_mark, $grades_arr);

            $options = '<a href="' . site_url("classes/edit_user_assignment/" . $r->ID) . '" class="btn btn-warning btn-xs" data-toggle="tooltip" data-placement="bottom" title="' . lang("ctn_55") . '"><span class="glyphicon glyphicon-cog"></span></a> <a href="' . site_url("classes/delete_user_assignment/" . $r->ID . "/" . $this->security->get_csrf_hash()) . '" class="btn btn-danger btn-xs" onclick="return confirm(\'' . lang("ctn_317") . '\')" data-toggle="tooltip" data-placement="bottom" title="' . lang("ctn_57") . '"><span class="glyphicon glyphicon-trash"></span></a>';


            $this->datatables->data[] = array(
                $this->common->get_user_display(array("username" => $r->username, "avatar" => $r->avatar, "online_timestamp" => $r->online_timestamp, "first_name" => $r->first_name, "last_name" => $r->last_name)),
                date($this->settings->info->date_format, $r->timestamp),
                $r->IP,
                $r->mark . " / " . $r->max_mark . " (" . $user_grade . ")",
                "<a href='" . base_url() . $this->settings->info->upload_path_relative . "/" . $r->file_name . "' target='_blank'>" . $r->file_name . "</a>",
                $options
            );
        }
        echo json_encode($this->datatables->process());
    }

    private function calculate_grade($mark, $max_mark, $grades) {
        if ($max_mark > 0) {
            $score = intval($mark / $max_mark * 100);
        } else {
            $score = 0;
        }
        foreach ($grades as $grade) {
            if ($score >= $grade['min_score'] && $score <= $grade['max_score']) {
                return $grade['grade'];
            }
        }
        return "N/A";
    }

    public function edit_user_assignment($id) {
        $id = intval($id);
        $assignment = $this->classes_model->get_user_assignment_id($id);
        if ($assignment->num_rows() == 0) {
            $this->template->error(lang("error_103"));
        }

        $assignment = $assignment->row();

        /* foreach($assignment as $key => $v){
          log_message('debug', 'assignment ID: ' . $key . '=>' .$v);
          } */

        $flags = $this->get_user_flags($assignment->ID);

        $teacher_flag = $flags['teacher_flag'];
        $member_flag = $flags['member_flag'];
        if ($assignment->userid != $this->user->info->ID) {
            if ($this->settings->info->teacher_class_manage) {
                if (!$flags['teacher_flag']) {
                    $this->template->error(lang("error_2"));
                }
            } else {
                if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                    $this->template->error(lang("error_2"));
                }
                $teacher_flag = true;
            }
        }

        $class = $this->classes_model->get_class($assignment->classid);
        if ($class->num_rows() == 0) {
            $this->template->error(lang("error_92"));
        }

        $class = $class->row();

        $this->template->loadContent("classes/edit_user_assignment.php", array(
            "assignment" => $assignment,
            "class" => $class,
            "member_flag" => $member_flag,
            "teacher_flag" => $teacher_flag
                )
        );
    }

    public function edit_user_assignment_pro($id) {
        $id = intval($id);
        $assignment = $this->classes_model->get_user_assignment_id($id);
        if ($assignment->num_rows() == 0) {
            $this->template->error(lang("error_103"));
        }

        $assignment = $assignment->row();

        $notes = $this->lib_filter->go($this->input->post("notes"));
        $mark = $this->common->nohtml($this->input->post("mark"));

        $this->load->library("upload");

        if (isset($_FILES['userfile']) && $_FILES['userfile']['size'] > 0) {
            $this->upload->initialize(array(
                "upload_path" => $this->settings->info->upload_path,
                "overwrite" => FALSE,
                "max_filename" => 300,
                "encrypt_name" => TRUE,
                "remove_spaces" => TRUE,
                "allowed_types" => $assignment->file_types,
                "max_size" => $this->settings->info->file_size
            ));

            if (!$this->upload->do_upload()) {
                $this->template->error(lang("error_21")
                        . $this->upload->display_errors());
            }

            $data = $this->upload->data();

            $image = $data['file_name'];
        } else {
            $data = array();
            $data['file_name'] = $assignment->file_name;
            $data['file_size'] = $assignment->file_size;
            $data['file_ext'] = $assignment->file_extension;
            $data['file_type'] = $assignment->file_type;
        }


        $this->classes_model->update_user_assignment($id, array(
            "file_name" => $data['file_name'],
            "file_size" => $data['file_size'],
            "file_extension" => $data['file_ext'],
            "file_type" => $data['file_type'],
            "notes" => $notes,
            "mark" => $mark
                )
        );

        $this->session->set_flashdata("globalmsg", lang("success_66"));
        redirect(site_url("classes/view_assignment_submissions/" . $assignment->assignmentid));
    }

    public function delete_user_assignment($id, $hash) {
        if ($hash != $this->security->get_csrf_hash()) {
            $this->template->error(lang("error_6"));
        }
        $id = intval($id);
        $assignment = $this->classes_model->get_user_assignment_id($id);
        if ($assignment->num_rows() == 0) {
            $this->template->error(lang("error_103"));
        }

        $assignment = $assignment->row();

        $this->classes_model->delete_user_assignment($id);
        $this->session->set_flashdata("globalmsg", lang("success_67"));
        redirect(site_url("classes/view_assignment_submissions/" . $assignment->assignmentid));
    }

    public function view_assignments($id) {
        $id = intval($id);
        $class = $this->classes_model->get_class($id);
        if ($class->num_rows() == 0) {
            $this->template->error(lang("error_92"));
        }

        $class = $class->row();

        $this->template->loadContent("classes/view_assignments.php", array(
            "class" => $class
                )
        );
    }

    public function assignment_page($id) {
        $id = intval($id);
        $class = $this->classes_model->get_class($id);
        if ($class->num_rows() == 0) {
            $this->template->error(lang("error_92"));
        }

        $class = $class->row();

        // Check if member
        $member_flag = false;
        $teacher_flag = false;
        $member = $this->classes_model
                ->get_class_student_user($this->user->info->ID, $id);
        if ($member->num_rows() == 0) {
            $member_flag = false;
        } else {
            $member = $member->row();
            if ($member->teacher_flag) {
                $teacher_flag = true;
            }
            $member_flag = true;
        }


        $this->load->library("datatables");

        $this->datatables->set_default_order("class_assignments.due_date", "asc");

        // Set page ordering options that can be used
        $this->datatables->ordering(
                array(
                    0 => array(
                        "class_assignments.title" => 0
                    ),
                    2 => array(
                        "class_assignments.timestamp" => 0
                    ),
                    3 => array(
                        "class_assignments.type" => 0
                    ),
                    4 => array(
                        "class_assignments.weighting" => 0
                    ),
                    5 => array(
                        "class_assignments.due_date" => 0
                    )
                )
        );

        $this->datatables->set_total_rows(
                $this->classes_model
                        ->get_total_assignments($id)
        );
        $assign = $this->classes_model->get_assignments_dt($id, $this->datatables);

        foreach ($assign->result() as $r) {

            $options = '<a href="' . site_url("classes/view_assignment/" . $r->ID) . '" class="btn btn-primary btn-xs">' . lang("ctn_552") . '</a> ';
            if ($this->common->has_permissions(array("admin", "class_manager"), $this->user) || ($this->settings->info->teacher_class_manage && $teacher_flag)) {
                $options .= '<a href="' . site_url("classes/view_assignment_submissions/" . $r->ID) . '" class="btn btn-info btn-xs">' . lang("ctn_553") . '</a> <a href="' . site_url("classes/edit_assignment/" . $r->ID) . '" class="btn btn-warning btn-xs" data-toggle="tooltip" data-placement="bottom" title="' . lang("ctn_55") . '"><span class="glyphicon glyphicon-cog"></span></a> <a href="' . site_url("classes/delete_assignment/" . $r->ID . "/" . $this->security->get_csrf_hash()) . '" class="btn btn-danger btn-xs" onclick="return confirm(\'' . lang("ctn_317") . '\')" data-toggle="tooltip" data-placement="bottom" title="' . lang("ctn_57") . '"><span class="glyphicon glyphicon-trash"></span></a>';
            }

            if ($r->type == 0) {
                $type = lang("ctn_550");
            } elseif ($r->type == 1) {
                $type = lang("ctn_551");
            }

            $this->datatables->data[] = array(
                $r->title,
                $this->common->get_user_display(array("username" => $r->username, "avatar" => $r->avatar, "online_timestamp" => $r->online_timestamp, "first_name" => $r->first_name, "last_name" => $r->last_name)),
                date($this->settings->info->date_format, $r->timestamp),
                $type,
                $r->weighting . "%",
                date($this->settings->info->date_format, $r->due_date),
                $options
            );
        }
        echo json_encode($this->datatables->process());
    }

    public function edit_assignment($id) {
        $id = intval($id);
        $assignment = $this->classes_model->get_assignment($id);
        if ($assignment->num_rows() == 0) {
            $this->template->error(lang("error_103"));
        }
        $assignment = $assignment->row();

        $flags = $this->get_user_flags($assignment->classid);

        $teacher_flag = $flags['teacher_flag'];
        $member_flag = $flags['member_flag'];
        if ($this->settings->info->teacher_class_manage) {
            if (!$flags['teacher_flag']) {
                $this->template->error(lang("error_2"));
            }
        } else {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $this->template->error(lang("error_2"));
            }
            $teacher_flag = true;
        }

        $class = $this->classes_model->get_class($assignment->classid);
        if ($class->num_rows() == 0) {
            $this->template->error(lang("error_92"));
        }

        $class = $class->row();

        $this->template->loadExternal(
                '<link rel="stylesheet" href="' . base_url() . 'scripts/libraries/bootstrap-datepicker-1.6.4/css/bootstrap-datepicker3.min.css" />
			<script src="' . base_url() . 'scripts/libraries/bootstrap-datepicker-1.6.4/js/bootstrap-datepicker.min.js"></script>'
        );

        $this->template->loadContent("classes/edit_assignment.php", array(
            "assignment" => $assignment,
            "class" => $class,
            "member_flag" => $member_flag,
            "teacher_flag" => $teacher_flag
                )
        );
    }

    public function edit_assignment_pro($id) {
        $id = intval($id);
        $assignment = $this->classes_model->get_assignment($id);
        if ($assignment->num_rows() == 0) {
            $this->template->error(lang("error_103"));
        }
        $assignment = $assignment->row();

        if ($this->settings->info->teacher_class_manage) {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $member = $this->classes_model
                        ->get_class_student_user($this->user->info->ID, $assignment->classid);
                if ($member->num_rows() == 0) {
                    $this->template->error(lang("error_2"));
                }
                $member = $member->row();
                if (!$member->teacher_flag) {
                    $this->template->error(lang("error_2"));
                }
            }
        } else {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $this->template->error(lang("error_2"));
            }
        }

        $title = $this->common->nohtml($this->input->post("title"));
        $body = $this->lib_filter->go($this->input->post("assignment"));
        $due_date = $this->common->nohtml($this->input->post("due_date"));
        $file_types = $this->common->nohtml($this->input->post("file_types"));
        $reupload = intval($this->input->post("reupload"));
        $deny_upload = intval($this->input->post("deny_upload"));
        $weighting = intval($this->input->post("weighting"));
        $max_mark = intval($this->input->post("max_mark"));


        if (empty($title)) {
            $this->template->error(lang("error_85"));
        }

        if (!empty($due_date)) {
            $dd = DateTime::createFromFormat($this->settings->info->date_picker_format, $due_date);
            $dd_timestamp = $dd->getTimestamp();
        } else {
            $dd_timestamp = 0;
        }

        $this->classes_model->update_assignment($id, array(
            "title" => $title,
            "body" => $body,
            "due_date" => $dd_timestamp,
            "file_types" => $file_types,
            "reupload" => $reupload,
            "deny_upload" => $deny_upload,
            "weighting" => $weighting,
            "max_mark" => $max_mark
                )
        );

        $this->session->set_flashdata("globalmsg", lang("success_68"));
        redirect(site_url("classes/view_assignments/" . $assignment->classid));
    }

    public function delete_assignment($id, $hash) {
        if ($hash != $this->security->get_csrf_hash()) {
            $this->template->error(lang("error_6"));
        }
        $id = intval($id);
        $assignment = $this->classes_model->get_assignment($id);
        if ($assignment->num_rows() == 0) {
            $this->template->error(lang("error_103"));
        }
        $assignment = $assignment->row();

        if ($this->settings->info->teacher_class_manage) {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $member = $this->classes_model
                        ->get_class_student_user($this->user->info->ID, $assignment->classid);
                if ($member->num_rows() == 0) {
                    $this->template->error(lang("error_2"));
                }
                $member = $member->row();
                if (!$member->teacher_flag) {
                    $this->template->error(lang("error_2"));
                }
            }
        } else {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $this->template->error(lang("error_2"));
            }
        }

        $this->classes_model->delete_assignment($id);
        $this->session->set_flashdata("globalmsg", lang("success_69"));
        redirect(site_url("classes/view_assignments/" . $assignment->classid));
    }

    public function add_assignment($id) {
        $id = intval($id);
        $class = $this->classes_model->get_class($id);
        if ($class->num_rows() == 0) {
            $this->template->error(lang("error_92"));
        }

        $class = $class->row();

        if ($this->settings->info->teacher_class_manage) {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $member = $this->classes_model
                        ->get_class_student_user($this->user->info->ID, $class->ID);
                if ($member->num_rows() == 0) {
                    $this->template->error(lang("error_2"));
                }
                $member = $member->row();
                if (!$member->teacher_flag) {
                    $this->template->error(lang("error_2"));
                }
            }
        } else {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $this->template->error(lang("error_2"));
            }
        }

        $title = $this->common->nohtml($this->input->post("title"));
        $assignment = $this->lib_filter->go($this->input->post("assignment"));
        $due_date = $this->common->nohtml($this->input->post("due_date"));
        $file_types = $this->common->nohtml($this->input->post("file_types"));
        $reupload = intval($this->input->post("reupload"));
        $deny_upload = intval($this->input->post("deny_upload"));
        $weighting = intval($this->input->post("weighting"));
        $max_mark = intval($this->input->post("max_mark"));

        $type = intval($this->input->post("type"));
        $generate = intval($this->input->post("generate_entries"));

        if ($type == 1) {
            if ($generate) {
                
            }
        }

        if (empty($title)) {
            $this->template->error(lang("error_85"));
        }

        if (!empty($due_date)) {
            $dd = DateTime::createFromFormat($this->settings->info->date_picker_format, $due_date);
            $dd_timestamp = $dd->getTimestamp();
        } else {
            $dd_timestamp = 0;
        }

        $assignmentid = $this->classes_model->add_assignment(array(
            "title" => $title,
            "type" => $type,
            "userid" => $this->user->info->ID,
            "body" => $assignment,
            "timestamp" => time(),
            "due_date" => $dd_timestamp,
            "file_types" => $file_types,
            "classid" => $id,
            "reupload" => $reupload,
            "deny_upload" => $deny_upload,
            "weighting" => $weighting,
            "max_mark" => $max_mark
                )
        );

        if ($type == 1) {
            if ($generate) {
                $students = $this->classes_model->get_students_from_class($id);
                foreach ($students->result() as $r) {
                    if (!$r->teacher_flag) {
                        $this->classes_model->add_user_assignment(array(
                            "userid" => $r->userid,
                            "assignmentid" => $assignmentid,
                            "timestamp" => time()
                                )
                        );
                    }
                }
            }
        }

        $this->session->set_flashdata("globalmsg", lang("success_70"));
        redirect(site_url("classes/view/" . $id));
    }

    public function view_announcements($id) {
        $id = intval($id);
        $class = $this->classes_model->get_class($id);
        if ($class->num_rows() == 0) {
            $this->template->error(lang("error_92"));
        }

        $class = $class->row();

        $flags = $this->get_user_flags($id);

        $teacher_flag = $flags['teacher_flag'];
        $member_flag = $flags['member_flag'];
        if ($this->settings->info->teacher_class_manage) {
            if (!$flags['teacher_flag']) {
                $this->template->error(lang("error_2"));
            }
        } else {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $this->template->error(lang("error_2"));
            }
            $teacher_flag = true;
        }

        $this->template->loadContent("classes/view_announcements.php", array(
            "class" => $class,
            "member_flag" => $member_flag,
            "teacher_flag" => $teacher_flag
                )
        );
    }

    public function view_announcement($id) {
        $this->template->loadData("activeLink", array("students" => array("groups" => 1)));
        $id = intval($id);
        $announcement = $this->classes_model->get_announcement($id);
        if ($announcement->num_rows() == 0) {
            $this->template->error(lang("error_86"));
        }

        $announcement = $announcement->row();


        // Check if user has viewed
        $c = $this->classes_model->get_user_announcement($this->user->info->ID, $id);
        if ($c->num_rows() == 0) {
            $this->classes_model->add_user_announcement(array(
                "announcementid" => $id,
                "userid" => $this->user->info->ID,
                "timestamp" => time()
                    )
            );
        }

        $users = $this->classes_model->get_user_announcements($id);

        $flags = $this->get_user_flags($announcement->classid);

        $teacher_flag = $flags['teacher_flag'];
        $member_flag = $flags['member_flag'];
        if ($this->settings->info->teacher_class_manage) {
            if (!$flags['teacher_flag']) {
                $this->template->error(lang("error_2"));
            }
        } else {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $this->template->error(lang("error_2"));
            }
            $teacher_flag = true;
        }

        $id = intval($id);
        $class = $this->classes_model->get_class($announcement->classid);
        if ($class->num_rows() == 0) {
            $this->template->error(lang("error_92"));
        }

        $class = $class->row();

        $this->template->loadContent("classes/view_announcement.php", array(
            "announcement" => $announcement,
            "users" => $users,
            "class" => $class,
            "member_flag" => $member_flag,
            "teacher_flag" => $teacher_flag
                )
        );
    }

    public function announcement_page($id) {
        $id = intval($id);
        $class = $this->classes_model->get_class($id);
        if ($class->num_rows() == 0) {
            $this->template->error(lang("error_92"));
        }

        $class = $class->row();

        // Check if member
        $member_flag = false;
        $teacher_flag = false;
        $member = $this->classes_model
                ->get_class_student_user($this->user->info->ID, $id);
        if ($member->num_rows() == 0) {
            $member_flag = false;
        } else {
            $member = $member->row();
            if ($member->teacher_flag) {
                $teacher_flag = true;
            }
            $member_flag = true;
        }


        $this->load->library("datatables");

        $this->datatables->set_default_order("class_announcements.timestamp", "desc");

        // Set page ordering options that can be used
        $this->datatables->ordering(
                array(
                    0 => array(
                        "class_announcements.title" => 0
                    ),
                    1 => array(
                        "users.username" => 0
                    ),
                    2 => array(
                        "class_announcements.timestamp" => 0
                    )
                )
        );

        $this->datatables->set_total_rows(
                $this->classes_model
                        ->get_total_announcements($id)
        );
        $news = $this->classes_model->get_announcements_dt($id, $this->datatables);

        foreach ($news->result() as $r) {

            $options = '<a href="' . site_url("classes/view_announcement/" . $r->ID) . '" class="btn btn-primary btn-xs">' . lang("ctn_552") . '</a> ';
            if ($this->common->has_permissions(array("admin", "class_manager"), $this->user) || ($this->settings->info->teacher_class_manage && $teacher_flag)) {
                $options .= '<a href="' . site_url("classes/edit_announcement/" . $r->ID) . '" class="btn btn-warning btn-xs" data-toggle="tooltip" data-placement="bottom" title="' . lang("ctn_55") . '"><span class="glyphicon glyphicon-cog"></span></a> <a href="' . site_url("classes/delete_announcement/" . $r->ID . "/" . $this->security->get_csrf_hash()) . '" class="btn btn-danger btn-xs" onclick="return confirm(\'' . lang("ctn_317") . '\')" data-toggle="tooltip" data-placement="bottom" title="' . lang("ctn_57") . '"><span class="glyphicon glyphicon-trash"></span></a>';
            }

            $this->datatables->data[] = array(
                $r->title,
                $this->common->get_user_display(array("username" => $r->username, "avatar" => $r->avatar, "online_timestamp" => $r->online_timestamp, "first_name" => $r->first_name, "last_name" => $r->last_name)),
                date($this->settings->info->date_format, $r->timestamp),
                $options
            );
        }
        echo json_encode($this->datatables->process());
    }

    public function edit_announcement($id) {
        $id = intval($id);
        $announcement = $this->classes_model->get_announcement($id);
        if ($announcement->num_rows() == 0) {
            $this->template->error(lang("error_86"));
        }

        $announcement = $announcement->row();

        $member_flag = false;
        $teacher_flag = false;
        if ($this->settings->info->teacher_class_manage) {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $member = $this->classes_model
                        ->get_class_student_user($this->user->info->ID, $announcement->classid);
                if ($member->num_rows() == 0) {
                    $this->template->error(lang("error_2"));
                }
                $member = $member->row();
                if (!$member->teacher_flag) {
                    $this->template->error(lang("error_2"));
                }
                $teacher_flag = true;
            }
        } else {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $this->template->error(lang("error_2"));
            }
            $teacher_flag = true;
        }

        $id = intval($id);
        $class = $this->classes_model->get_class($announcement->classid);
        if ($class->num_rows() == 0) {
            $this->template->error(lang("error_92"));
        }

        $class = $class->row();

        $this->template->loadContent("classes/edit_announcement.php", array(
            "announcement" => $announcement,
            "class" => $class,
            "member_flag" => $member_flag,
            "teacher_flag" => $teacher_flag,
                )
        );
    }

    public function edit_announcement_pro($id) {
        $id = intval($id);
        $announcement = $this->classes_model->get_announcement($id);
        if ($announcement->num_rows() == 0) {
            $this->template->error(lang("error_86"));
        }

        $announcement = $announcement->row();


        if ($this->settings->info->teacher_class_manage) {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $member = $this->classes_model
                        ->get_class_student_user($this->user->info->ID, $announcement->classid);
                if ($member->num_rows() == 0) {
                    $this->template->error(lang("error_2"));
                }
                $member = $member->row();
                if (!$member->teacher_flag) {
                    $this->template->error(lang("error_2"));
                }
            }
        } else {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $this->template->error(lang("error_2"));
            }
        }

        $title = $this->common->nohtml($this->input->post("title"));
        $body = $this->lib_filter->go($this->input->post("announcement"));

        if (empty($title)) {
            $this->template->error(lang("error_85"));
        }

        $this->classes_model->update_announcement($id, array(
            "title" => $title,
            "body" => $body,
                )
        );

        // Send notification to all students

        $this->session->set_flashdata("globalmsg", lang("success_50"));
        redirect(site_url("classes/view/" . $announcement->classid));
    }

    public function delete_announcement($id, $hash) {
        if ($hash != $this->security->get_csrf_hash()) {
            $this->template->error(lang("error_6"));
        }
        $id = intval($id);
        $announcement = $this->classes_model->get_announcement($id);
        if ($announcement->num_rows() == 0) {
            $this->template->error(lang("error_86"));
        }

        $announcement = $announcement->row();

        if ($this->settings->info->teacher_class_manage) {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $member = $this->classes_model
                        ->get_class_student_user($this->user->info->ID, $announcement->classid);
                if ($member->num_rows() == 0) {
                    $this->template->error(lang("error_2"));
                }
                $member = $member->row();
                if (!$member->teacher_flag) {
                    $this->template->error(lang("error_2"));
                }
            }
        } else {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $this->template->error(lang("error_2"));
            }
        }

        $this->classes_model->delete_announcement($id);
        $this->session->set_flashdata("globalmsg", lang("success_51"));
        redirect(site_url("classes/view/" . $announcement->classid));
    }

    public function add_class_announcement($id) {
        $id = intval($id);
        $class = $this->classes_model->get_class($id);
        if ($class->num_rows() == 0) {
            $this->template->error(lang("error_92"));
        }

        $class = $class->row();


        if ($this->settings->info->teacher_class_manage) {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $member = $this->classes_model
                        ->get_class_student_user($this->user->info->ID, $id);
                if ($member->num_rows() == 0) {
                    $this->template->error(lang("error_2"));
                }
                $member = $member->row();
                if (!$member->teacher_flag) {
                    $this->template->error(lang("error_2"));
                }
            }
        } else {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $this->template->error(lang("error_2"));
            }
        }

        $title = $this->common->nohtml($this->input->post("title"));
        $body = $this->lib_filter->go($this->input->post("announcement"));

        if (empty($title)) {
            $this->template->error(lang("error_85"));
        }

        $this->classes_model->add_announcement(array(
            "title" => $title,
            "body" => $body,
            "timestamp" => time(),
            "userid" => $this->user->info->ID,
            "classid" => $id
                )
        );

        // Send notification to all students
        $students = $this->classes_model->get_students_from_class($id);
        foreach ($students->result() as $r) {
            // Send notification
            $this->user_model->increment_field($r->userid, "noti_count", 1);
            $this->user_model->add_notification(array(
                "userid" => $r->userid,
                "url" => "classes/view/" . $id,
                "timestamp" => time(),
                "message" => lang("ctn_821") . " <strong>" . $class->name . "</strong>",
                "status" => 0,
                "fromid" => $this->user->info->ID,
                "email" => $r->email,
                "username" => $r->username,
                "email_notification" => $r->email_notification
                    )
            );
        }

        $this->session->set_flashdata("globalmsg", lang("success_49"));
        redirect(site_url("classes/view/" . $id));
    }

    public function teacher_page($id) {
        $id = intval($id);
        $class = $this->classes_model->get_class($id);
        if ($class->num_rows() == 0) {
            $this->template->error(lang("error_92"));
        }

        $class = $class->row();

        // Check if member
        $member_flag = false;
        $teacher_flag = false;
        $member = $this->classes_model
                ->get_class_student_user($this->user->info->ID, $id);
        if ($member->num_rows() == 0) {
            $member_flag = false;
        } else {
            $member = $member->row();
            if ($member->teacher_flag) {
                $teacher_flag = true;
            }
            $member_flag = true;
        }

        $this->load->library("datatables");

        $this->datatables->set_default_order("users.username", "asc");

        // Set page ordering options that can be used
        $this->datatables->ordering(
                array(
                    0 => array(
                        "users.username" => 0
                    ),
                    1 => array(
                        "users.email" => 0
                    )
                )
        );

        $this->datatables->set_total_rows(
                $this->classes_model
                        ->get_teacher_count($id)
        );
        $classes = $this->classes_model->get_class_teachers($id, $this->datatables);


        foreach ($classes->result() as $r) {

            $options = '<a href="<?php echo site_url("mail/index/?username=" . $r->username) ?>" class="btn btn-info btn-xs" data-toggle="tooltip" data-placement="bottom" title="' . lang("ctn_776") . '"><span class="glyphicon glyphicon-envelope"></span></a>';
            if ($this->common->has_permissions(array("admin", "class_manager"), $this->user) || ($this->settings->info->teacher_class_manage && $teacher_flag)) {
                $options .= ' <a href="' . site_url("classes/delete_student/" . $r->ID . "/" . $this->security->get_csrf_hash()) . '" class="btn btn-danger btn-xs" onclick="return confirm(\'' . lang("ctn_317") . '\')" data-toggle="tooltip" data-placement="bottom" title="' . lang("ctn_57") . '"><span class="glyphicon glyphicon-trash"></span></a>';
            }

            $this->datatables->data[] = array(
                $this->common->get_user_display(array("username" => $r->username, "avatar" => $r->avatar, "online_timestamp" => $r->online_timestamp, "first_name" => $r->first_name, "last_name" => $r->last_name)),
                $options
            );
        }

        echo json_encode($this->datatables->process());
    }

    public function attendance($id) {
        $id = intval($id);
        $class = $this->classes_model->get_class($id);
        if ($class->num_rows() == 0) {
            $this->template->error(lang("error_92"));
        }

        $class = $class->row();


        $flags = $this->get_user_flags($id);

        $teacher_flag = $flags['teacher_flag'];
        $member_flag = $flags['member_flag'];
        if ($this->settings->info->teacher_class_manage) {
            if (!$flags['teacher_flag']) {
                $this->template->error(lang("error_2"));
            }
        } else {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $this->template->error(lang("error_2"));
            }
            $teacher_flag = true;
        }

        $this->template->loadContent("classes/attendance.php", array(
            "class" => $class,
            "member_flag" => $member_flag,
            "teacher_flag" => $teacher_flag
                )
        );
    }

    public function attendance_page($id) {
        if ($this->settings->info->teacher_class) {
            if (!$this->common->has_permissions(array("admin", "class_maanger", "teacher"), $this->user)) {
                $this->template->error(lang("error_2"));
            }
        }
        $id = intval($id);

        $this->load->library("datatables");

        $this->datatables->set_default_order("attendance_sheets.attendance_date", "desc");

        // Set page ordering options that can be used
        $this->datatables->ordering(
                array(
                    0 => array(
                        "users.username" => 0
                    ),
                    1 => array(
                        "attendance_sheets.attendance" => 0
                    ),
                    2 => array(
                        "calendar_events.title" => 0
                    ),
                    3 => array(
                        "attendance_sheets.attendance_date" => 0
                    )
                )
        );

        $this->datatables->set_total_rows(
                $this->classes_model
                        ->get_attendance_count($id)
        );
        $attendance = $this->classes_model->get_class_attendance($id, $this->datatables);


        foreach ($attendance->result() as $r) {

            $this->datatables->data[] = array(
                $this->common->get_user_display(array("username" => $r->username, "avatar" => $r->avatar, "online_timestamp" => $r->online_timestamp, "first_name" => $r->first_name, "last_name" => $r->last_name)),
                $r->attendance . "%",
                $r->title . " " . $r->start,
                date($this->settings->info->date_format, $r->attendance_date),
                '<a href="' . site_url("classes/edit_attendance/" . $r->ID) . '" class="btn btn-warning btn-xs" data-toggle="tooltip" data-placement="bottom" title="' . lang("ctn_55") . '"><span class="glyphicon glyphicon-cog"></span></a> <a href="' . site_url("classes/delete_attendance/" . $r->ID . "/" . $this->security->get_csrf_hash()) . '" class="btn btn-danger btn-xs" onclick="return confirm(\'' . lang("ctn_317") . '\')" data-toggle="tooltip" data-placement="bottom" title="' . lang("ctn_57") . '"><span class="glyphicon glyphicon-trash"></span></a>'
            );
        }

        echo json_encode($this->datatables->process());
    }

    public function edit_attendance($id) {
        $id = intval($id);
        $attendance = $this->classes_model->get_class_attendance_sheet($id);
        if ($attendance->num_rows() == 0) {
            $this->template->error(lang("error_108"));
        }
        $attendance = $attendance->row();

        $flags = $this->get_user_flags($attendance->classid);

        $teacher_flag = $flags['teacher_flag'];
        $member_flag = $flags['member_flag'];
        if ($this->settings->info->teacher_class_manage) {
            if (!$flags['teacher_flag']) {
                $this->template->error(lang("error_2"));
            }
        } else {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $this->template->error(lang("error_2"));
            }
            $teacher_flag = true;
        }

        $class = $this->classes_model->get_class($attendance->classid);
        if ($class->num_rows() == 0) {
            $this->template->error(lang("error_92"));
        }

        $class = $class->row();

        // Get entries
        $students = $this->classes_model->get_attendance_sheet_entries($attendance->classid, $id);

        $startdt = new DateTime('now'); // setup a local datetime
        $startdt->sub(DateInterval::createFromDateString('360 days'));
        $format = $startdt->format('Y-m-d H:i:s');

        $startdt = new DateTime('now'); // setup a local datetime
        $startdt->add(DateInterval::createFromDateString('360 days'));
        $format2 = $startdt->format('Y-m-d H:i:s');

        //$attend_ids[] = $attendance->eventid;
        //$events = $this->classes_model->get_class_events($format, $format2, $attendance->classid, $attend_ids);
        $events = $this->classes_model->get_class_event($attendance->eventid);

        $this->template->loadContent("classes/edit_attendance.php", array(
            "students" => $students,
            "attendance" => $attendance,
            "events" => $events,
            "class" => $class,
            "member_flag" => $member_flag,
            "teacher_flag" => $teacher_flag
                )
        );
    }

    public function edit_attendance_pro($id) {
        $id = intval($id);
        $attendance = $this->classes_model->get_class_attendance_sheet($id);
        if ($attendance->num_rows() == 0) {
            $this->template->error(lang("error_108"));
        }
        $attendance = $attendance->row();

        if ($this->settings->info->teacher_class_manage) {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $member = $this->classes_model
                        ->get_class_student_user($this->user->info->ID, $attendance->classid);
                if ($member->num_rows() == 0) {
                    $this->template->error(lang("error_2"));
                }
                $member = $member->row();
                if (!$member->teacher_flag) {
                    $this->template->error(lang("error_2"));
                }
            }
        } else {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $this->template->error(lang("error_2"));
            }
        }

        $eventid = intval($this->input->post("eventid"));
        $event = $this->classes_model->get_class_event($eventid);
        if ($event->num_rows() == 0) {
            $this->template->error(lang("error_116"));
        }
        $event = $event->row();
        if ($event->classid != $attendance->classid) {
            $this->template->error(lang("error_172"));
        }

        $ed = DateTime::createFromFormat('Y-m-d H:i:s', $event->start);
        $end_date = $ed->format('Y-m-d H:i:s');
        $start_date_timestamp = $ed->getTimestamp();



        // Get students
        $students = $this->classes_model->get_students_from_class_only($attendance->classid);
        $students_attendance = array();
        $total = 0;
        foreach ($students->result() as $r) {
            if (isset($_POST['notes_' . $r->userid])) {
                $notes = $this->common->nohtml($this->input->post("notes_" . $r->userid));
                // Attendance
                if (isset($_POST['attendance_' . $r->userid])) {
                    $status = intval($this->input->post("attendance_" . $r->userid));
                } else {
                    $this->template->error(lang("error_110"));
                }
                if ($status == 0) {
                    $total++;
                }
                $students_attendance[] = array(
                    "userid" => $r->userid,
                    "notes" => $notes,
                    "status" => $status
                );
            }
        }

        $attendance_percent = intval(($total / $students->num_rows() ) * 100);

        // Delete old data
        $this->classes_model->delete_attendance_sheet_entries($id);

        $this->classes_model->update_attendance_sheet($id, array(
            "teacherid" => $this->user->info->ID,
            "attendance_date" => $start_date_timestamp,
            "attendance" => $attendance_percent,
            "time_date" => date("Y-m-d", $start_date_timestamp),
            "eventid" => $eventid
                )
        );

        foreach ($students_attendance as $a) {
            if ($a['status'] == 0) {
                $present = 1;
                $absent = 0;
                $late = 0;
                $holiday = 0;
            } elseif ($a['status'] == 1) {
                $present = 0;
                $absent = 1;
                $late = 0;
                $holiday = 0;
            } elseif ($a['status'] == 2) {
                $present = 0;
                $absent = 0;
                $late = 1;
                $holiday = 0;
            } elseif ($a['status'] == 3) {
                $present = 0;
                $absent = 0;
                $late = 0;
                $holiday = 1;
            }
            $this->classes_model->add_attendance_entry(array(
                "attendanceid" => $id,
                "userid" => $a['userid'],
                "notes" => $a['notes'],
                "present" => $present,
                "absent" => $absent,
                "late" => $late,
                "holiday" => $holiday
                    )
            );
        }

        $this->session->set_flashdata("globalmsg", lang("success_71"));
        redirect(site_url("classes/attendance/" . $attendance->classid));
    }

    public function delete_attendance($id, $hash) {
        if ($hash != $this->security->get_csrf_hash()) {
            $this->template->error(lang("error_6"));
        }
        $id = intval($id);
        $attendance = $this->classes_model->get_class_attendance_sheet($id);
        if ($attendance->num_rows() == 0) {
            $this->template->error(lang("error_108"));
        }
        $attendance = $attendance->row();

        if ($this->settings->info->teacher_class_manage) {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $member = $this->classes_model
                        ->get_class_student_user($this->user->info->ID, $attendance->classid);
                if ($member->num_rows() == 0) {
                    $this->template->error(lang("error_2"));
                }
                $member = $member->row();
                if (!$member->teacher_flag) {
                    $this->template->error(lang("error_2"));
                }
            }
        } else {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $this->template->error(lang("error_2"));
            }
        }

        $this->classes_model->delete_attendance_sheet($id);
        $this->session->set_flashdata("globalmsg", lang("success_72"));
        redirect(site_url("classes/attendance/" . $attendance->classid));
    }

    public function add_attendance($id) {
        $id = intval($id);
        $class = $this->classes_model->get_class($id);
        if ($class->num_rows() == 0) {
            $this->template->error(lang("error_92"));
        }

        $class = $class->row();


        $flags = $this->get_user_flags($id);

        $teacher_flag = $flags['teacher_flag'];
        $member_flag = $flags['member_flag'];
        if ($this->settings->info->teacher_class_manage) {
            if (!$flags['teacher_flag']) {
                $this->template->error(lang("error_2"));
            }
        } else {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $this->template->error(lang("error_2"));
            }
            $teacher_flag = true;
        }

        $students = $this->classes_model->get_students_from_class_only($id);

        $startdt = new DateTime('now'); // setup a local datetime
        $startdt->sub(DateInterval::createFromDateString('30 days'));
        $format = $startdt->format('Y-m-d H:i:s');

        $startdt = new DateTime('now'); // setup a local datetime
        $startdt->add(DateInterval::createFromDateString('30 days'));
        $format2 = $startdt->format('Y-m-d H:i:s');

        $attends = $this->classes_model->get_attendance_sheet_by_class($id);
        $attend_ids[] = 0;
        if ($attends->num_rows() > 0) {
            foreach ($attends->result() as $attend) {
                $attend_ids[] = $attend->eventid;
            }
        }

        $events = $this->classes_model->get_class_events($format, $format2, $id, $attend_ids);

        $this->template->loadContent("classes/add_attendance.php", array(
            "class" => $class,
            "students" => $students,
            "events" => $events,
            "member_flag" => $member_flag,
            "teacher_flag" => $teacher_flag
                )
        );
    }

    public function add_attendance_pro($id) {
        $id = intval($id);
        $class = $this->classes_model->get_class($id);
        if ($class->num_rows() == 0) {
            $this->template->error(lang("error_92"));
        }

        $class = $class->row();


        if ($this->settings->info->teacher_class_manage) {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $member = $this->classes_model
                        ->get_class_student_user($this->user->info->ID, $id);
                if ($member->num_rows() == 0) {
                    $this->template->error(lang("error_2"));
                }
                $member = $member->row();
                if (!$member->teacher_flag) {
                    $this->template->error(lang("error_2"));
                }
            }
        } else {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $this->template->error(lang("error_2"));
            }
        }

        //$start_date = $this->common->nohtml($this->input->post("date"));
        $eventid = intval($this->input->post("eventid"));
        $event = $this->classes_model->get_class_event($eventid);
        if ($event->num_rows() == 0) {
            $this->template->error(lang("error_116"));
        }
        $event = $event->row();
        if ($event->classid != $id) {
            $this->template->error(lang("error_172"));
        }

        $ed = DateTime::createFromFormat('Y-m-d H:i:s', $event->start);
        $end_date = $ed->format('Y-m-d H:i:s');
        $start_date_timestamp = $ed->getTimestamp();


        $teacher = $this->common->nohtml($this->input->post("teacher"));

        $user = $this->user_model->get_user_by_username($teacher);
        if ($user->num_rows() == 0) {
            $this->template->error(lang("error_111"));
        }
        $user = $user->row();
        $userid = $user->ID;

        // Check user role
        $user_role = $this->user_model->get_users_user_role($user->user_role);
        if ($user_role->num_rows() == 0) {
            $this->template->error(lang("error_88"));
            $user_role = $user_role->row();
            if (!$user_role->teacher) {
                $this->template->error(lang("error_112"));
            }
        }

        // Get students
        $students = $this->classes_model->get_students_from_class_only($id);
        $students_attendance = array();
        $total = 0;
        foreach ($students->result() as $r) {
            if (isset($_POST['notes_' . $r->userid])) {
                $notes = $this->common->nohtml($this->input->post("notes_" . $r->userid));
                // Attendance
                if (isset($_POST['attendance_' . $r->userid])) {
                    $status = intval($this->input->post("attendance_" . $r->userid));
                } else {
                    $this->template->error(lang("error_110"));
                }
                if ($status == 0) {
                    $total++;
                }
                $students_attendance[] = array(
                    "userid" => $r->userid,
                    "notes" => $notes,
                    "status" => $status
                );
            }
        }

        $attendance = intval(($total / $students->num_rows() ) * 100);

        $attendanceid = $this->classes_model->add_attendance(array(
            "classid" => $id,
            "teacherid" => $userid,
            "attendance_date" => $start_date_timestamp,
            "attendance" => $attendance,
            "time_date" => date("Y-m-d", $start_date_timestamp),
            "eventid" => $eventid
                )
        );

        foreach ($students_attendance as $a) {
            if ($a['status'] == 0) {
                $present = 1;
                $absent = 0;
                $late = 0;
                $holiday = 0;
            } elseif ($a['status'] == 1) {
                $present = 0;
                $absent = 1;
                $late = 0;
                $holiday = 0;
            } elseif ($a['status'] == 2) {
                $present = 0;
                $absent = 0;
                $late = 1;
                $holiday = 0;
            } elseif ($a['status'] == 3) {
                $present = 0;
                $absent = 0;
                $late = 0;
                $holiday = 1;
            }
            $this->classes_model->add_attendance_entry(array(
                "attendanceid" => $attendanceid,
                "userid" => $a['userid'],
                "notes" => $a['notes'],
                "present" => $present,
                "absent" => $absent,
                "late" => $late,
                "holiday" => $holiday
                    )
            );
        }

        $this->session->set_flashdata("globalmsg", lang("success_73"));
        redirect(site_url("classes/attendance/" . $id));
    }

    public function class_students($id) {
        if ($this->settings->info->teacher_class) {
            if (!$this->common->has_permissions(array("admin", "class_maanger", "teacher"), $this->user)) {
                $this->template->error(lang("error_2"));
            }
        }
        $id = intval($id);
        $class = $this->classes_model->get_class($id);
        if ($class->num_rows() == 0) {
            $this->template->error(lang("error_92"));
        }

        $class = $class->row();

        // Check if member
        $member_flag = false;
        $teacher_flag = false;
        $member = $this->classes_model
                ->get_class_student_user($this->user->info->ID, $id);
        if ($member->num_rows() == 0) {
            $member_flag = false;
        } else {
            $member = $member->row();
            if ($member->teacher_flag) {
                $teacher_flag = true;
            }
            $member_flag = true;
        }

        $this->template->loadContent("classes/class_students.php", array(
            "class" => $class,
            "member_flag" => $member_flag,
            "teacher_flag" => $teacher_flag
                )
        );
    }

    public function class_student_page($id) {
        if ($this->settings->info->teacher_class) {
            if (!$this->common->has_permissions(array("admin", "class_maanger", "teacher"), $this->user)) {
                $this->template->error(lang("error_2"));
            }
        }
        $id = intval($id);
        $class = $this->classes_model->get_class($id);
        if ($class->num_rows() == 0) {
            $this->template->error(lang("error_92"));
        }

        $class = $class->row();

        // Check if member
        $member_flag = false;
        $teacher_flag = false;
        $member = $this->classes_model
                ->get_class_student_user($this->user->info->ID, $id);
        if ($member->num_rows() == 0) {
            $member_flag = false;
        } else {
            $member = $member->row();
            if ($member->teacher_flag) {
                $teacher_flag = true;
            }
            $member_flag = true;
        }

        $this->load->library("datatables");

        $this->datatables->set_default_order("users.username", "asc");

        // Set page ordering options that can be used
        $this->datatables->ordering(
                array(
                    0 => array(
                        "users.username" => 0
                    ),
                    1 => array(
                        "users.email" => 0
                    ),
                    2 => array(
                        "class_students.agreement_number" => 0
                    )
                )
        );

        $this->datatables->set_total_rows(
                $this->classes_model
                        ->get_student_count($id)
        );
        $classes = $this->classes_model->get_class_students($id, $this->datatables);


        foreach ($classes->result() as $r) {

            $options = '';
            if ($this->common->has_permissions(array("admin", "class_manager"), $this->user) || ($this->settings->info->teacher_class_manage && $teacher_flag)) {
                $options .= '<a href="' . site_url("classes/student_assignments/" . $r->ID) . '" class="btn btn-info btn-xs">' . lang("ctn_577") . '</a> ';
            }
            $options .= '<a href="' . site_url("documents/download_agreement/" . $r->ID) . '" class="btn btn-primary btn-xs" data-toggle="tooltip" data-placement="bottom" title="' . lang("ctn_1020") . '"><span class="glyphicon glyphicon-download"></span></a>';
            $options .= ' <a href="' . site_url("documents/download_student_attendance/" . $r->ID) . '" class="btn btn-success btn-xs" data-toggle="tooltip" data-placement="bottom" title="' . lang("ctn_494") . '"><span class="glyphicon glyphicon-download"></span></a>';
            $options .= ' <a href="' . site_url("classes/certificate_pdf/" . $r->user_id . "/" . $id) . '" class="btn btn-success btn-xs" data-toggle="tooltip" data-placement="bottom" title="' . lang("ctn_1023") . '"><span class="glyphicon glyphicon-certificate"></span></a>';
            $options .= ' <a href="' . site_url("classes/delete_student/" . $r->ID . "/" . $this->security->get_csrf_hash()) . '" class="btn btn-danger btn-xs" onclick="return confirm(\'' . lang("ctn_317") . '\')" data-toggle="tooltip" data-placement="bottom" title="' . lang("ctn_57") . '"><span class="glyphicon glyphicon-trash"></span></a>';

            $this->datatables->data[] = array(
                $this->common->get_user_display(array("username" => $r->username, "avatar" => $r->avatar, "online_timestamp" => $r->online_timestamp, "first_name" => $r->first_name, "last_name" => $r->last_name)),
                $r->email,
                $r->agreement_number,
                $options
            );
        }

        echo json_encode($this->datatables->process());
    }

    public function certificate_pdf($id, $class_id) {
        $id = intval($id);
        $class_id = intval($class_id);
        if ($this->settings->info->allow_student_profile && $id == $this->user->info->ID) {
            
        } else {
            if (!$this->common->has_permissions(array("admin", "student_manager",
                        "student_viewer", "parent"), $this->user)) {
                $this->template->error(lang("error_2"));
            }

            // Check if user is a parent that the child is theres
            // All roles except parent means they are a parent
            if (!$this->common->has_permissions(array("admin", "student_manager",
                        "student_viewer"), $this->user)) {
                $child = $this->user_model->check_child($this->user->info->ID, $id);
                if ($child->num_rows() == 0) {
                    $this->template->error(lang("error_161"));
                }
            }
        }

        $id = intval($id);
        $student = $this->students_model->get_student($id);
        if ($student->num_rows() == 0) {
            $this->template->error(lang("error_114"));
        }
        $student = $student->row();

        $classes = $this->classes_model->get_class($class_id);
        if ($classes->num_rows() == 0) {
            $this->template->error(lang("error_92"));
        }
        $class = $classes->row();

        $categories = $this->classes_model->get_category($class->categoryid);
        $category = $categories->row();

        $subjects = $this->subjects_model->get_subject($class->subjectid);
        $subject = $subjects->row();

        //format periods in turkmen and english language
        $day1 = date('d', strtotime($category->start_date));
        $day2 = date('d', strtotime($category->end_date));

        $month1 = date('F', strtotime($category->start_date));
        $month2 = date('F', strtotime($category->end_date));

        $month1_num = date('n', strtotime($category->start_date));
        $month2_num = date('n', strtotime($category->end_date));

        $year1 = date('Y', strtotime($category->start_date));
        $year2 = date('Y', strtotime($category->end_date));

        $formated_day1 = $this->addOrdinalNumberSuffix(intval($day1));
        $formated_day2 = $this->addOrdinalNumberSuffix(intval($day2));

        $formated_day1_tm = $this->addOrdinalNumberSuffixTm(intval($day1));
        $formated_day2_tm = $this->addOrdinalNumberSuffixTm(intval($day2));

        $formated_year1_tm = $this->addOrdinalNumberSuffixTm(intval($year1));
        $formated_year2_tm = $this->addOrdinalNumberSuffixTm(intval($year2));

        $formated_month1_tm = $this->getMonthSuffix($month1_num, true);
        $formated_month2_tm = $this->getMonthSuffix($month2_num, false);

        $period_en = $formated_day1 . " of " . $month1 . " " . $year1 . " to " .
                $formated_day2 . " of " . $month2 . " " . $year2;

        $period_tm = $formated_year1_tm . " ýylyň " . $formated_day1_tm . " " .
                $formated_month1_tm . " " . $formated_year2_tm . " ýylyň " .
                $formated_day2_tm . " " . $formated_month2_tm;



        // calculate grade
        $grades = $this->classes_model->get_student_total_grade($student->ID);
        $total_grade = 0;
        foreach ($grades->result() as $grade) {
            $x = ($grade->mark * $grade->weighting) / $grade->max_mark;
            $total_grade += $x;
        }

        $letter_grades = $this->classes_model->get_class_grades_all($class->ID);
        foreach ($letter_grades->result() as $r) {
            $grades_arr[] = array(
                "min_score" => $r->min_score,
                "max_score" => $r->max_score,
                "grade" => $r->grade
            );
        }
        $total_letter_grade = $this->calculate_grade($total_grade, 100, $grades_arr);


        ob_start();
        $this->template->loadAjax("classes/view_certificate_pdf.php", array(
            "student" => $student,
            "class" => $class,
            "subject" => $subject,
            "period_en" => $period_en,
            "period_tm" => $period_tm,
            "total_grade" => $total_grade
                )
        );
        $out = ob_get_contents();
        ob_end_clean();
        require_once APPPATH . 'third_party/mpdf/vendor/autoload.php';

        $mpdf = new \Mpdf\Mpdf(array(
            "mode" => "UTF-8"
                )
        );
        $mpdf->WriteHTML($out);
        $mpdf->Output();
    }

    /**

     * Method to return number with suffix
     * @param type $num
     * @return string number with suffix example: 3rd    /
     */
    function addOrdinalNumberSuffix($num) {
        if (!in_array(($num % 100), array(11, 12, 13))) {
            switch ($num % 10) {
                // Handle 1st, 2nd, 3rd
                case 1: return $num . 'st';
                case 2: return $num . 'nd';
                case 3: return $num . 'rd';
            }
        }
        return $num . 'th';
    }

    /**

     * 
     * @param type $num
     * @return string number with suffix in turkmen language example: 8-nji    /
     */
    function addOrdinalNumberSuffixTm($num) {
        if (in_array(($num % 100), [1, 6, 9, 10, 16, 19, 26, 29, 30, 36, 39, 40, 46, 49, 56, 59, 60, 66, 69, 76, 79, 86, 89, 96, 99])) {
            return $num . "-njy";
        } else {
            return $num . "-nji";
        }
    }

    /**

     * Method to get Month from correct suffix (from and to)
     * @param type $num
     * @param type $status if status is set tot true then it means from, to otherwise
     * @return string month with proper suffix   Example Aprelinden or Apreline /
     */
    function getMonthSuffix($num, $status) {
        $months = [
            1 => 'Ýanwar', 2 => 'Fewral', 3 => 'Mart', 4 => 'Aprel', 5 => 'Maý',
            6 => 'Iýun', 7 => 'Iýul', 8 => 'Awgust', 9 => 'Sentýabyr',
            10 => 'Oktýabyr', 11 => 'Noýabyr', 12 => 'Dekabyr'
        ];

        if ($num > 0 && $num < 13 && array_key_exists($num, $months)) {
            if ($status) {
                $suffix1 = 'yndan';
                $suffix2 = 'inden';
            } else {
                $suffix1 = 'yna';
                $suffix2 = 'ine';
            }

            if ($num === 4) {
                return $months[$num] . $suffix2;
            } else {
                return $months[$num] . $suffix1;
            }
        }
    }

    public function student_assignments($id) {
        $id = intval($id);
        $member = $this->classes_model->get_class_student($id);
        if ($member->num_rows() == 0) {
            $this->template->error(lang("error_114"));
        }
        $memberr = $member->row();

        $class = $this->classes_model->get_class($memberr->classid);
        if ($class->num_rows() == 0) {
            $this->template->error(lang("error_92"));
        }

        $class = $class->row();

        // Check if member
        $member_flag = false;
        $teacher_flag = false;
        $member = $this->classes_model
                ->get_class_student_user($this->user->info->ID, $memberr->classid);
        if ($member->num_rows() == 0) {
            $member_flag = false;
        } else {
            $member = $member->row();
            if ($member->teacher_flag) {
                $teacher_flag = true;
            }
            $member_flag = true;
        }

        if ($this->settings->info->teacher_class_manage) {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $member = $this->classes_model
                        ->get_class_student_user($this->user->info->ID, $member->classid);
                if ($member->num_rows() == 0) {
                    $this->template->error(lang("error_2"));
                }
                $member = $member->row();
                if (!$member->teacher_flag) {
                    $this->template->error(lang("error_2"));
                }
            }
        } else {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $this->template->error(lang("error_2"));
            }
        }

        $grades = $this->classes_model->get_student_total_grade($memberr->userid);
        $total_grade = 0;
        foreach ($grades->result() as $grade) {
            $x = ($grade->mark * $grade->weighting) / $grade->max_mark;
            $total_grade += $x;
        }

        $letter_grades = $this->classes_model->get_class_grades_all($memberr->classid);
        foreach ($letter_grades->result() as $r) {
            $grades_arr[] = array(
                "min_score" => $r->min_score,
                "max_score" => $r->max_score,
                "grade" => $r->grade
            );
        }
        $total_letter_grade = $this->calculate_grade($total_grade, 100, $grades_arr);

        $this->template->loadContent("classes/student_assignments.php", array(
            "class" => $class,
            "total_grade" => $total_grade,
            "total_letter_grade" => $total_letter_grade,
            "member_flag" => $member_flag,
            "teacher_flag" => $teacher_flag,
            "student" => $memberr
                )
        );
    }

    public function student_assignment_page($id) {
        $id = intval($id);
        $member = $this->classes_model->get_class_student($id);
        if ($member->num_rows() == 0) {
            $this->template->error(lang("error_114"));
        }
        $member = $member->row();

        if ($this->settings->info->teacher_class_manage) {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $member = $this->classes_model
                        ->get_class_student_user($this->user->info->ID, $member->classid);
                if ($member->num_rows() == 0) {
                    $this->template->error(lang("error_2"));
                }
                $member = $member->row();
                if (!$member->teacher_flag) {
                    $this->template->error(lang("error_2"));
                }
            }
        } else {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $this->template->error(lang("error_2"));
            }
        }

        $this->load->library("datatables");

        $this->datatables->set_default_order("class_assignments.due_date", "asc");

        // Set page ordering options that can be used
        $this->datatables->ordering(
                array(
                    0 => array(
                        "class_assignments.title" => 0
                    ),
                    1 => array(
                        "class_assignments.due_date" => 0
                    ),
                    2 => array(
                        "classes.name" => 0
                    ),
                    3 => array(
                        "class_assignments.type" => 0
                    ),
                    4 => array(
                        "user_assignments.mark" => 0
                    ),
                    5 => array(
                        "user_assignments.timestamp" => 0
                    )
                )
        );

        $this->datatables->set_total_rows(
                $this->classes_model
                        ->get_your_assignments_total($member->userid)
        );
        $assign = $this->classes_model
                ->get_your_assignments_dt($member->userid, $this->datatables);

        $classes = array();
        foreach ($assign->result() as $r) {
            $classes[] = $r->classid;
        }

        $classes = array_unique($classes);
        $grades = $this->classes_model->get_grades_classes($classes);
        $gradesArr = array();
        foreach ($grades->result() as $grade) {
            if (isset($gradesArr[$grade->classid])) {
                $gradesArr[$grade->classid][] = array(
                    "grade" => $grade->grade,
                    "min_score" => $grade->min_score,
                    "max_score" => $grade->max_score
                );
            } else {
                $gradesArr[$grade->classid] = array();
                $gradesArr[$grade->classid][] = array(
                    "grade" => $grade->grade,
                    "min_score" => $grade->min_score,
                    "max_score" => $grade->max_score
                );
            }
        }


        foreach ($assign->result() as $r) {

            if (isset($gradesArr[$r->classid])) {
                $grades = $gradesArr[$r->classid];
            } else {
                $grades = array();
            }

            if (!isset($r->mark)) {
                $grade = lang("ctn_870");
                $date = "<label class='label label-danger'>" . lang("ctn_823") . "</label>";
            } else {
                $grade = $r->mark . " / " . $r->max_mark . " ( " . $this->calculate_grade($r->mark, $r->max_mark, $grades) . ")";
                $date = date($this->settings->info->date_format, $r->timestamp);
            }

            if ($r->type == 0) {
                $type = lang("ctn_550");
            } elseif ($r->type == 1) {
                $type = lang("ctn_551");
            }

            $this->datatables->data[] = array(
                $r->title,
                date($this->settings->info->date_format, $r->due_date),
                $r->name,
                $type,
                $grade,
                $date,
                '<a href="' . site_url("classes/view_assignment/" . $r->ID) . '" class="btn btn-primary btn-xs">' . lang("ctn_552") . '</a>'
            );
        }

        echo json_encode($this->datatables->process());
    }

    public function add_teacher($id) {
        $id = intval($id);
        $class = $this->classes_model->get_class($id);
        if ($class->num_rows() == 0) {
            $this->template->error(lang("error_92"));
        }

        $class = $class->row();

        if ($this->settings->info->teacher_class_manage) {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $member = $this->classes_model
                        ->get_class_student_user($this->user->info->ID, $id);
                if ($member->num_rows() == 0) {
                    $this->template->error(lang("error_2"));
                }
                $member = $member->row();
                if (!$member->teacher_flag) {
                    $this->template->error(lang("error_2"));
                }
            }
        } else {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $this->template->error(lang("error_2"));
            }
        }


        $username = $this->common->nohtml($this->input->post("username"));

        $user = $this->user_model->get_user_by_username($username);
        if ($user->num_rows() == 0) {
            $this->template->error(lang("error_111"));
        }
        $user = $user->row();
        $userid = $user->ID;

        $category = $this->classes_model->get_category($class->categoryid);
        $cat = $category->row();

        $t = $this->classes_model->check_teachers($user->ID, $id, $cat->end_date, $class->class_days, $class->start_hour, $class->end_hour);

        if ($t->num_rows() > 0) {
            $this->template->error($username . ' ' . lang("error_227"));
        }

        // Check user role
        $user_role = $this->user_model->get_users_user_role($user->user_role);
        if ($user_role->num_rows() == 0) {
            $this->template->error(lang("error_88"));
            $user_role = $user_role->row();
            if (!$user_role->teacher) {
                $this->template->error(lang("error_112"));
            }
        }

        // Check user is not already a member
        $member = $this->classes_model
                ->get_class_student_user($userid, $id);
        if ($member->num_rows() > 0) {
            $this->template->error(lang("error_113"));
        }

        // Add
        $this->classes_model->add_student(array(
            "classid" => $id,
            "userid" => $userid,
            "teacher_flag" => 1
                )
        );

        $user = $this->user_model->get_user($userid);
        if ($user->num_rows() > 0) {
            $user = $user->row();
            // Send notification
            $this->user_model->increment_field($userid, "noti_count", 1);
            $this->user_model->add_notification(array(
                "userid" => $userid,
                "url" => "classes/view/" . $id,
                "timestamp" => time(),
                "message" => lang("ctn_820") . ": <strong>" . $class->name . "</strong>",
                "status" => 0,
                "fromid" => $this->user->info->ID,
                "email" => $user->email,
                "username" => $user->username,
                "email_notification" => $user->email_notification
                    )
            );
        }

        // Recount Class students
        $count = $this->classes_model->get_student_count($id);

        $this->classes_model->update_class($id, array(
            "students" => $count
                )
        );

        $this->session->set_flashdata("globalmsg", lang("success_74"));
        redirect(site_url("classes/view/" . $id));
    }

    public function add_student($id) {
        $id = intval($id);
        $class = $this->classes_model->get_class($id);
        if ($class->num_rows() == 0) {
            $this->template->error(lang("error_92"));
        }

        $class = $class->row();

        if ($this->settings->info->teacher_class_manage) {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $member = $this->classes_model
                        ->get_class_student_user($this->user->info->ID, $id);
                if ($member->num_rows() == 0) {
                    $this->template->error(lang("error_2"));
                }
                $member = $member->row();
                if (!$member->teacher_flag) {
                    $this->template->error(lang("error_2"));
                }
            }
        } else {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $this->template->error(lang("error_2"));
            }
        }

        $username = $this->common->nohtml($this->input->post("username"));

        $user = $this->user_model->get_user_by_username($username);
        if ($user->num_rows() == 0) {
            $this->template->error(lang("error_114"));
        }
        $user = $user->row();
        $userid = $user->ID;

        // Check user role
        $user_role = $this->user_model->get_users_user_role($user->user_role);
        if ($user_role->num_rows() == 0) {
            $this->template->error(lang("error_88"));
            $user_role = $user_role->row();
            if (!$user_role->student) {
                $this->template->error(lang("error_89"));
            }
        }

        // Check user is not already a member
        $member = $this->classes_model
                ->get_class_student_user($userid, $id);
        if ($member->num_rows() > 0) {
            $this->template->error(lang("error_115"));
        }

        // Check student if attend other class(es)
        // SELECT * FROM class_students cs 
        // left join users u on(u.ID = cs.userid) 
        // left join class_categories cc on(cc.ID = cs.classid) 
        // WHERE userid = $userid and classid != $id and cc.start_date >= NOW()
        //check if teacher has other classes
        //SELECT * FROM class_students cs 
        //left join users u on(u.ID = cs.userid) 
        //left join class_categories cc on(cc.ID = cs.classid) 
        //left join classes c on(c.ID = cs.classid) 
        //WHERE userid = 2 and classid != 2 and cc.end_date >= NOW() 
        //and cs.teacher_flag = 1 and c.start_hour != '11:00' and c.end_hour != '13:00'
        // Add
        $branch = $this->classes_model->get_branch($class->branch_id)->row();
        $branch_code = $branch->code;
        $this->classes_model->add_student(array(
            "classid" => $id,
            "userid" => $userid
                ), $branch_code
        );

        $user = $this->user_model->get_user($userid);
        if ($user->num_rows() > 0) {
            $user = $user->row();
            // Send notification
            $this->user_model->increment_field($userid, "noti_count", 1);
            $this->user_model->add_notification(array(
                "userid" => $userid,
                "url" => "classes/view/" . $id,
                "timestamp" => time(),
                "message" => lang("ctn_820") . ": <strong>" . $class->name . "</strong>",
                "status" => 0,
                "fromid" => $this->user->info->ID,
                "email" => $user->email,
                "username" => $user->username,
                "email_notification" => $user->email_notification
                    )
            );
        }

        // Recount Class students
        $count = $this->classes_model->get_student_count($id);

        $this->classes_model->update_class($id, array(
            "students" => $count
                )
        );

        $this->session->set_flashdata("globalmsg", lang("success_75"));
        redirect(site_url("classes/class_students/" . $id));
    }

    public function add_student_to_class() {
        $class_id = intval($this->common->nohtml($this->input->post("class_id")));
        $student_id = intval($this->common->nohtml($this->input->post("student_id")));

        if ($class_id === 0) {
            $this->template->error(lang("error_234"));
        }
        $class = $this->classes_model->get_class($class_id);
        if ($class->num_rows() == 0) {
            $this->template->error(lang("error_92"));
        }

        $class = $class->row();

        if ($this->settings->info->teacher_class_manage) {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $member = $this->classes_model
                        ->get_class_student_user($this->user->info->ID, $class_id);
                if ($member->num_rows() == 0) {
                    $this->template->error(lang("error_2"));
                }
                $member = $member->row();
                if (!$member->teacher_flag) {
                    $this->template->error(lang("error_2"));
                }
            }
        } else {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $this->template->error(lang("error_2"));
            }
        }

        $student = $this->students_model->get_student($student_id);
        if ($student->num_rows() === 0) {
            $this->template->error(lang("error_114"));
        }
        $student = $student->row();
        
        // Check user is not already a member
        $member = $this->classes_model
                ->get_class_student_user($student->ID, $class->ID);
        if ($member->num_rows() > 0) {
            $this->template->error(lang("error_115"));
        }
        
        // Add
        $branch = $this->classes_model->get_branch($class->branch_id)->row();
        $branch_code = $branch->code;
        $this->classes_model->add_student(array(
            "classid" => $class_id,
            "userid" => $student->ID
                ), $branch_code
        );

        $user = $this->user_model->get_user($student->ID);
        if ($user->num_rows() > 0) {
            $user = $user->row();
            // Send notification
            $this->user_model->increment_field($student->ID, "noti_count", 1);
            $this->user_model->add_notification(array(
                "userid" => $student->ID,
                "url" => "classes/view/" . $class_id,
                "timestamp" => time(),
                "message" => lang("ctn_820") . ": <strong>" . $class->name . "</strong>",
                "status" => 0,
                "fromid" => $this->user->info->ID,
                "email" => $user->email,
                "username" => $user->username,
                "email_notification" => $user->email_notification
                    )
            );
        }

        // Recount Class students
        $count = $this->classes_model->get_student_count($class_id);

        $this->classes_model->update_class($class_id, array(
            "students" => $count
                )
        );

        $this->session->set_flashdata("globalmsg", lang("success_75"));
        redirect(site_url("classes"));
    }

    public function delete_student($id, $hash) {
        if ($hash != $this->security->get_csrf_hash()) {
            $this->template->error(lang("error_6"));
        }
        $id = intval($id);
        $student = $this->classes_model->get_class_student($id);
        if ($student->num_rows() == 0) {
            $this->template->error(lang("error_114"));
        }

        $student = $student->row();

        if ($this->settings->info->teacher_class_manage) {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $member = $this->classes_model
                        ->get_class_student_user($this->user->info->ID, $student->classid);
                if ($member->num_rows() == 0) {
                    $this->template->error(lang("error_2"));
                }
                $member = $member->row();
                if (!$member->teacher_flag) {
                    $this->template->error(lang("error_2"));
                }
            }
        } else {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $this->template->error(lang("error_2"));
            }
        }

        // Delete
        $this->classes_model->delete_student($id);

        $data = array(
            "user_id" => $student->userid,
            "class_id" => $student->classid,
            "flag_teacher" => 0,
            "agreement_number" => $student->agreement_number
        );

        $this->classes_model->add_dropped_student($data);

        $user = $this->user_model->get_user($student->userid);
        if ($user->num_rows() > 0) {
            $user = $user->row();
            // Send notification
            $this->user_model->increment_field($student->userid, "noti_count", 1);
            $this->user_model->add_notification(array(
                "userid" => $student->userid,
                "url" => "classes/view/" . $student->classid,
                "timestamp" => time(),
                "message" => lang("ctn_822") . ": <strong>" . $student->name . "</strong>",
                "status" => 0,
                "fromid" => $this->user->info->ID,
                "email" => $user->email,
                "username" => $user->username,
                "email_notification" => $user->email_notification
                    )
            );
        }

        // Recount Class students
        $count = $this->classes_model->get_student_count($id);

        $this->classes_model->update_class($id, array(
            "students" => $count
                )
        );

        $this->session->set_flashdata("globalmsg", lang("success_76"));
        redirect(site_url("classes/class_students/" . $student->classid));
    }

    /*     * *************************BRANCH STAFF STARTS HERE********************** */

    /**

     * Method to get all branches
     * @author shagy
     * @return view list of branches
     */
    public function branches() {
        //check permissions
        if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
            $this->template->error(lang("error_2"));
        }

        $this->template->loadData("activeLink", array("classes" => array("branches" => 1)));

        $this->template->loadContent("classes/branches.php", array(
                )
        );
    }

    /**

     * Method to draw table with branches
     * @author shagy
     * @return branch table data
     */
    public function branch_page() {
        if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
            $this->template->error(lang("error_2"));
        }
        $this->load->library("datatables");

        $this->datatables->set_default_order("branch.branch_id", "asc");

        $this->datatables->set_total_rows(
                $this->classes_model
                        ->get_branches_total()
        );
        $cats = $this->classes_model->get_branches_dt($this->datatables);


        foreach ($cats->result() as $r) {
            $this->datatables->data[] = array(
                $r->branch_id,
                $r->code,
                $r->name,
                $r->room_total,
                '<a href="' . site_url("classes/edit_branch/" . $r->branch_id) . '" class="btn btn-warning btn-xs" data-toggle="tooltip" data-placement="bottom" title="' . lang("ctn_55") . '"><span class="glyphicon glyphicon-cog"></span></a> <a href="' . site_url("classes/delete_branch/" . $r->branch_id . "/" . $this->security->get_csrf_hash()) . '" class="btn btn-danger btn-xs" onclick="return confirm(\'' . lang("ctn_317") . '\')" data-toggle="tooltip" data-placement="bottom" title="' . lang("ctn_57") . '"><span class="glyphicon glyphicon-trash"></span></a>'
            );
        }

        //log_message("debug", json_encode($this->datatables->process()));

        echo json_encode($this->datatables->process());
    }

    /**

     * Method to load branch view to be edited
     * @author Shagy <shagy@ttweb.org>
     * @param int $branch_id     /
     * @return view Returns view of branch to be edited
     */
    public function edit_branch($branch_id) {
        if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
            $this->template->error(lang("error_2"));
        }
        $id = intval($branch_id);
        $branch = $this->classes_model->get_branch($id);
        if ($branch->num_rows() == 0) {
            $this->template->error(lang("error_94"));
        }

        $branch = $branch->row();

        $this->template->loadData("activeLink", array("classes" => array("branches" => 1)));


        $this->template->loadContent("classes/edit_branch.php", array(
            "branch" => $branch
                )
        );
    }

    /**

     * Method to edit branch data
     * @author Shagy <shagy@ttweb.org>
     * @param int $branch_id     /
     */
    public function add_branch_pro() {
        if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
            $this->template->error(lang("error_2"));
        }

        $code = $this->common->nohtml($this->input->post("code"));
        $name = $this->common->nohtml($this->input->post("name"));
        $room_total = intval($this->common->nohtml($this->input->post("room_total")));

        if (empty($code)) {
            $this->template->error(lang("error_215"));
        }

        if (empty($name)) {
            $this->template->error(lang("error_216"));
        }

        if (empty($room_total)) {
            $this->template->error(lang("error_217"));
        }


        $branchid = $this->classes_model->add_branch(array(
            "code" => $code,
            "name" => $name,
            "room_total" => $room_total
                )
        );

        $this->session->set_flashdata("globalmsg", lang("success_158"));
        redirect(site_url("classes/branches"));
    }

    /**

     * Method to edit branch data
     * @author Shagy <shagy@ttweb.org>
     * @param int $branch_id     /
     */
    public function edit_branch_pro($branch_id) {
        if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
            $this->template->error(lang("error_2"));
        }
        $id = intval($branch_id);
        $branch = $this->classes_model->get_branch($id);
        if ($branch->num_rows() == 0) {
            $this->template->error(lang("error_218"));
        }

        //$branch = $branch->row();

        $code = $this->common->nohtml($this->input->post("code"));
        $name = $this->common->nohtml($this->input->post("name"));
        $room_total = intval($this->common->nohtml($this->input->post("room_total")));

        if (empty($code)) {
            $this->template->error(lang("error_215"));
        }

        if (empty($name)) {
            $this->template->error(lang("error_216"));
        }

        if (empty($room_total)) {
            $this->template->error(lang("error_217"));
        }


        $this->classes_model->update_branch($id, array(
            "code" => $code,
            "name" => $name,
            "room_total" => $room_total
                )
        );

        $this->session->set_flashdata("globalmsg", lang("success_156"));
        redirect(site_url("classes/branches"));
    }

    /**

     * Method to delete branch data
     * @author Shagy <shagy@ttweb.org>
     * @param int $branch_id     /
     * @param string $hash
     */
    public function delete_branch($branch_id, $hash) {
        if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
            $this->template->error(lang("error_2"));
        }
        if ($hash != $this->security->get_csrf_hash()) {
            $this->template->error(lang("error_6"));
        }
        $id = intval($branch_id);
        $branch = $this->classes_model->get_branch($id);
        if ($branch->num_rows() == 0) {
            $this->template->error(lang("error_218"));
        }

        $this->classes_model->delete_branch($id);

        $this->session->set_flashdata("globalmsg", lang("success_157"));
        redirect(site_url("classes/branches"));
    }

    /*     * ***************************BRANCH STAFF ENDS HERE********************** */

    /*     * ***************************ROOM STAFF STARTS HERE********************** */

    /**

     * Method to get all branches
     * @author shagy
     * @return view list of branches
     */
    public function rooms() {
        //check permissions
        if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
            $this->template->error(lang("error_2"));
        }

        $branches = $this->classes_model->get_branches()->result();

        $this->template->loadData("activeLink", array("classes" => array("rooms" => 1)));

        $this->template->loadContent("classes/rooms.php", array(
            "branches" => $branches
                )
        );
    }

    /**

     * Method to draw table with branches
     * @author shagy
     * @return branch table data
     */
    public function room_page() {
        if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
            $this->template->error(lang("error_2"));
        }
        $this->load->library("datatables");

        $this->datatables->set_default_order("room.room_id", "asc");

        // Set page ordering options that can be used
        $this->datatables->ordering(
                array(
                    1 => array(
                        "room.numeric_code" => 0
                    ),
                    2 => array(
                        "room.code" => 0
                    ),
                )
        );

        $this->datatables->set_total_rows(
                $this->classes_model
                        ->get_rooms_total()
        );
        $rooms = $this->classes_model->get_rooms_dt($this->datatables);


        foreach ($rooms->result() as $r) {
            $this->datatables->data[] = array(
                $r->numeric_code,
                $r->code,
                $r->branch_name,
                //$r->seat_total,
                '<a href="' . site_url("classes/edit_room/" . $r->room_id) . '" class="btn btn-warning btn-xs" data-toggle="tooltip" data-placement="bottom" title="' . lang("ctn_55") . '"><span class="glyphicon glyphicon-cog"></span></a> <a href="' . site_url("classes/delete_room/" . $r->room_id . "/" . $this->security->get_csrf_hash()) . '" class="btn btn-danger btn-xs" onclick="return confirm(\'' . lang("ctn_317") . '\')" data-toggle="tooltip" data-placement="bottom" title="' . lang("ctn_57") . '"><span class="glyphicon glyphicon-trash"></span></a>'
            );
        }

        echo json_encode($this->datatables->process());
    }

    /**

     * Method to add room data
     * @author Shagy <shagy@ttweb.org>
     * @return view Redirects to rooms page /
     */
    public function add_room_pro() {
        if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
            $this->template->error(lang("error_2"));
        }

        $numeric_code = intval($this->common->nohtml($this->input->post("numeric_code")));
        $code = $this->common->nohtml($this->input->post("code"));
        $branch_id = intval($this->common->nohtml($this->input->post("branch_id")));
        //$seat_total = intval($this->common->nohtml($this->input->post("seat_total")));

        if (empty($numeric_code)) {
            $this->template->error(lang("error_219"));
        }

        if (empty($code)) {
            $this->template->error(lang("error_220"));
        }

        if (empty($branch_id)) {
            $this->template->error(lang("error_221"));
        }

        /* if (empty($seat_total)) {
          $this->template->error(lang("error_222"));
          } */


        $roomid = $this->classes_model->add_room(array(
            "numeric_code" => $numeric_code,
            "code" => $code,
            "branch_id" => $branch_id,
                //"seat_total" => $seat_total
                )
        );

        $this->session->set_flashdata("globalmsg", lang("success_158"));
        redirect(site_url("classes/rooms"));
    }

    /**

     * Method to load room view to be edited
     * @author Shagy <shagy@ttweb.org>
     * @param int $room_id     /
     * @return view Returns view of room to be edited
     */
    public function edit_room($room_id) {
        if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
            $this->template->error(lang("error_2"));
        }
        $id = intval($room_id);
        $room = $this->classes_model->get_room($id);
        if ($room->num_rows() == 0) {
            $this->template->error(lang("error_223"));
        }

        $room = $room->row();

        $branches = $this->classes_model->get_branches()->result();

        $this->template->loadData("activeLink", array("classes" => array("rooms" => 1)));


        $this->template->loadContent("classes/edit_room.php", array(
            "room" => $room,
            "branches" => $branches
                )
        );
    }

    /**

     * Method to edit room data
     * @author Shagy <shagy@ttweb.org>
     * @param int $room_id     /
     */
    public function edit_room_pro($room_id) {
        if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
            $this->template->error(lang("error_2"));
        }
        $id = intval($room_id);
        $room = $this->classes_model->get_room($id);
        if ($room->num_rows() == 0) {
            $this->template->error(lang("error_223"));
        }

        $numeric_code = intval($this->common->nohtml($this->input->post("numeric_code")));
        $code = $this->common->nohtml($this->input->post("code"));
        $branch_id = intval($this->common->nohtml($this->input->post("branch_id")));
        //$seat_total = intval($this->common->nohtml($this->input->post("seat_total")));

        if (empty($numeric_code)) {
            $this->template->error(lang("error_219"));
        }

        if (empty($code)) {
            $this->template->error(lang("error_220"));
        }

        if (empty($branch_id)) {
            $this->template->error(lang("error_221"));
        }

        /* if (empty($seat_total)) {
          $this->template->error(lang("error_222"));
          } */


        $this->classes_model->update_room($id, array(
            "numeric_code" => $numeric_code,
            "code" => $code,
            "branch_id" => $branch_id,
                //"seat_total" => $seat_total
                )
        );

        $this->session->set_flashdata("globalmsg", lang("success_160"));
        redirect(site_url("classes/rooms"));
    }

    /**

     * Method to delete room data
     * @author Shagy <shagy@ttweb.org>
     * @param int $room_id     /
     * @param string $hash
     */
    public function delete_room($room_id, $hash) {
        if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
            $this->template->error(lang("error_2"));
        }
        if ($hash != $this->security->get_csrf_hash()) {
            $this->template->error(lang("error_6"));
        }
        $id = intval($room_id);
        $branch = $this->classes_model->get_room($id);
        if ($branch->num_rows() == 0) {
            $this->template->error(lang("error_223"));
        }

        $this->classes_model->delete_room($id);

        $this->session->set_flashdata("globalmsg", lang("success_161"));
        redirect(site_url("classes/rooms"));
    }

    /**

     * Method to get rooms by branch     /
     * @author Shagy <shagy@ttweb.org>
     * @return json encoded
     */
    public function get_room_by_branch() {
        $json = array();
        $branch_id = intval($this->common->nohtml($this->input->post("branch_id")));

        $rooms = $this->classes_model->get_branch_room($branch_id);
        $html = '<select name="room_id" class="form-control">';
        $html .= '<option value="">' . lang('ctn_1001') . '</option>';
        foreach ($rooms->result() as $r) {
            $html .= '<option value="' . $r->room_id . '">' . $r->code . '</option>';
        }
        $html .= '</select>';
        $json['html'] = $html;


        echo json_encode($json);
    }

    /*     * ***************************ROOM STAFF ENDS HERE**************************** */

    public function categories() {
        if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
            $this->template->error(lang("error_2"));
        }

        /* $this->template->loadExternal(
          '<link rel="stylesheet" href="' . base_url() . 'scripts/libraries/datetimepicker/jquery.datetimepicker.css" />
          <script src="' . base_url() . 'scripts/libraries/datetimepicker/jquery.datetimepicker.full.min.js"></script>'
          ); */
        $this->template->loadExternal(
                '<link rel="stylesheet" href="' . base_url() . 'scripts/libraries/bootstrap-datepicker-1.6.4/css/bootstrap-datepicker3.min.css" />
			<script src="' . base_url() . 'scripts/libraries/bootstrap-datepicker-1.6.4/js/bootstrap-datepicker.min.js"></script>'
        );

        $this->template->loadData("activeLink", array("classes" => array("cats" => 1)));

        $this->template->loadContent("classes/categories.php", array(
                )
        );
    }

    public function add_category_pro() {
        if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
            $this->template->error(lang("error_2"));
        }
        $name = $this->common->nohtml($this->input->post("name"));
        $number = intval($this->common->nohtml($this->input->post("number")));
        $desc = $this->lib_filter->go($this->input->post("description"));
        $start_date = $this->lib_filter->go($this->input->post("start_date"));
        $end_date = intval($this->common->nohtml($this->input->post("end_date")));

        $this->load->library("upload");

        // Image
        if (isset($_FILES['userfile']) && $_FILES['userfile']['size'] > 0) {
            $this->upload->initialize(array(
                "upload_path" => $this->settings->info->upload_path,
                "overwrite" => FALSE,
                "max_filename" => 300,
                "encrypt_name" => TRUE,
                "remove_spaces" => TRUE,
                "allowed_types" => "png|jpeg|jpg|gif",
                "max_size" => $this->settings->info->file_size,
            ));

            if (!$this->upload->do_upload()) {
                $this->template->error(lang("error_21")
                        . $this->upload->display_errors());
            }

            $data = $this->upload->data();

            $image = $data['file_name'];
        } else {
            $image = "default_cat.png";
        }

        if (empty($name)) {
            $this->template->error(lang("error_111"));
        }

        if (empty($number)) {
            $this->template->error(lang("error_213"));
        }


        if (!empty($start_date)) {
            $sd = DateTime::createFromFormat($this->settings->info->date_format, $start_date);
            $start_date = $sd->format('Y-m-d');
        } else {
            $this->template->error(lang("error_211"));
        }

        if (!empty($end_date)) {
            $sd = DateTime::createFromFormat($this->settings->info->date_format, $end_date);
            $end_date = $sd->format('Y-m-d');
        } else {
            $this->template->error(lang("error_211"));
        }

        /* if (!empty($hrs) && !empty($start_date)) {
          $after_week = (floor($hrs / 6) * 7) - 1;
          $day = $hrs % 6;
          $interval = 'P' . ($after_week + $day) . 'D';
          $date = new DateTime($start_date);
          $date->add(new DateInterval($interval));
          $end_date = $date->format('Y-m-d');
          } else {
          $this->template->error(lang("error_214"));
          } */

        //log_message("debug", "start: " . $start_date . " End :" . $end_date);

        $catid = $this->classes_model->add_category(array(
            "name" => $name,
            "number" => $number,
            "description" => $desc,
            "start_date" => $start_date,
            "end_date" => $end_date,
            "image" => $image
                )
        );

        $this->session->set_flashdata("globalmsg", lang("success_77"));
        redirect(site_url("classes/categories"));
    }

    public function delete_cat($id, $hash) {
        if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
            $this->template->error(lang("error_2"));
        }
        if ($hash != $this->security->get_csrf_hash()) {
            $this->template->error(lang("error_6"));
        }
        $id = intval($id);
        $category = $this->classes_model->get_category($id);
        if ($category->num_rows() == 0) {
            $this->template->error(lang("error_94"));
        }

        $this->classes_model->delete_category($id);
        $this->session->set_flashdata("globalmsg", lang("success_78"));
        redirect(site_url("classes/categories"));
    }

    public function edit_cat($id) {
        if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
            $this->template->error(lang("error_2"));
        }
        $id = intval($id);
        $category = $this->classes_model->get_category($id);
        if ($category->num_rows() == 0) {
            $this->template->error(lang("error_94"));
        }

        /* $this->template->loadExternal(
          '<link rel="stylesheet" href="' . base_url() . 'scripts/libraries/datetimepicker/jquery.datetimepicker.css" />
          <script src="' . base_url() . 'scripts/libraries/datetimepicker/jquery.datetimepicker.full.min.js"></script>'
          ); */
        $this->template->loadExternal(
                '<link rel="stylesheet" href="' . base_url() . 'scripts/libraries/bootstrap-datepicker-1.6.4/css/bootstrap-datepicker3.min.css" />
			<script src="' . base_url() . 'scripts/libraries/bootstrap-datepicker-1.6.4/js/bootstrap-datepicker.min.js"></script>'
        );

        $category = $category->row();

        $this->template->loadData("activeLink", array("classes" => array("cats" => 1)));


        $this->template->loadContent("classes/edit_cat.php", array(
            "category" => $category
                )
        );
    }

    public function edit_category_pro($id) {
        if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
            $this->template->error(lang("error_2"));
        }
        $id = intval($id);
        $category = $this->classes_model->get_category($id);
        if ($category->num_rows() == 0) {
            $this->template->error(lang("error_94"));
        }

        $category = $category->row();

        $name = $this->common->nohtml($this->input->post("name"));
        $number = intval($this->common->nohtml($this->input->post("number")));
        $desc = $this->lib_filter->go($this->input->post("description"));
        $start_date = $this->common->nohtml($this->input->post("start_date"));
        $end_date = $this->common->nohtml($this->input->post("end_date"));


        $this->load->library("upload");

        // Image
        if (isset($_FILES['userfile']) && $_FILES['userfile']['size'] > 0) {
            $this->upload->initialize(array(
                "upload_path" => $this->settings->info->upload_path,
                "overwrite" => FALSE,
                "max_filename" => 300,
                "encrypt_name" => TRUE,
                "remove_spaces" => TRUE,
                "allowed_types" => "png|jpeg|jpg|gif",
                "max_size" => $this->settings->info->file_size,
            ));

            if (!$this->upload->do_upload()) {
                $this->template->error(lang("error_21")
                        . $this->upload->display_errors());
            }

            $data = $this->upload->data();

            $image = $data['file_name'];
        } else {
            $image = $category->image;
        }

        if (empty($name)) {
            $this->template->error(lang("error_81"));
        }

        if (empty($number)) {
            $this->template->error(lang("error_213"));
        }

        if (!empty($start_date)) {
            $sd = DateTime::createFromFormat($this->settings->info->date_format, $start_date);
            $start_date = $sd->format('Y-m-d');
        } else {
            $this->template->error(lang("error_211"));
        }

        if (!empty($end_date)) {
            $sd = DateTime::createFromFormat($this->settings->info->date_format, $end_date);
            $end_date = $sd->format('Y-m-d');
        } else {
            $this->template->error(lang("error_211"));
        }

        /* if (!empty($hrs) && !empty($start_date)) {
          $after_week = (floor($hrs / 6) * 7) - 1;
          $day = $hrs % 6;
          $interval = 'P' . ($after_week + $day) . 'D';
          $date = new DateTime($start_date);
          $date->add(new DateInterval($interval));
          $end_date = $date->format('Y-m-d');
          //$ed = DateTime::createFromFormat($this->settings->info->date_format, $end_date);
          //$end_date = $ed->format('Y-m-d');
          } else {
          $this->template->error(lang("error_214"));
          } */

        $this->classes_model->update_category($id, array(
            "name" => $name,
            "number" => $number,
            "description" => $desc,
            "start_date" => $start_date,
            "end_date" => $end_date,
            "image" => $image
                )
        );


        $this->session->set_flashdata("globalmsg", lang("success_79"));
        redirect(site_url("classes/categories"));
    }

    public function cat_page() {
        if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
            $this->template->error(lang("error_2"));
        }
        $this->load->library("datatables");

        $this->datatables->set_default_order("class_categories.number", "desc");

        // Set page ordering options that can be used
        $this->datatables->ordering(
                array(
                    1 => array(
                        "class_categories.number" => 0
                    ),
                    2 => array(
                        "class_categories.hrs" => 0
                    ),
                    3 => array(
                        "class_categories.start_date" => 0
                    ),
                    4 => array(
                        "class_categories.end_date" => 0
                    )
                )
        );

        $this->datatables->set_total_rows(
                $this->classes_model
                        ->get_categories_total()
        );
        $cats = $this->classes_model->get_categories_dt($this->datatables);


        foreach ($cats->result() as $r) {
            $today = new DateTime(); // Today
            $contractDateBegin = new DateTime($r->start_date);
            $contractDateEnd = new DateTime($r->end_date);

            if (
                    $today->getTimestamp() > $contractDateBegin->getTimestamp() &&
                    $today->getTimestamp() < $contractDateEnd->getTimestamp()) {

                $style = 'style="color: greenyellow;"';
                $val = 1;
            } elseif ($today->getTimestamp() < $contractDateBegin->getTimestamp()) {
                $style = 'style="color: yellow;"';
                $val = 2;
            } else {
                $style = 'style="color: red;"';
                $val = 3;
            }

            $this->datatables->data[] = array(
                //'<img src="' . base_url() . $this->settings->info->upload_path_relative . '/' . $r->image . '" class="cat-icon">',
                '</div><div class="text-center"><i class="glyphicon glyphicon-stop" ' . $style . '></i></div>',
                $r->number,
                $r->name,
                $r->start_date,
                $r->end_date,
                '<a href="' . site_url("classes/edit_cat/" . $r->ID) . '" class="btn btn-warning btn-xs" data-toggle="tooltip" data-placement="bottom" title="' . lang("ctn_55") . '"><span class="glyphicon glyphicon-cog"></span></a> <a href="' . site_url("classes/delete_cat/" . $r->ID . "/" . $this->security->get_csrf_hash()) . '" class="btn btn-danger btn-xs" onclick="return confirm(\'' . lang("ctn_317") . '\')" data-toggle="tooltip" data-placement="bottom" title="' . lang("ctn_57") . '"><span class="glyphicon glyphicon-trash"></span></a> <a href="' . site_url("documents/download_order/" . $r->ID . "/general") . '" class="btn btn-primary btn-xs" data-toggle="tooltip" data-placement="bottom" title="' . lang("ctn_1015") . '"><span class="glyphicon glyphicon-download"></span></a> <a href="' . site_url("documents/download_order/" . $r->ID . "/certificate") . '" class="btn btn-primary btn-xs" data-toggle="tooltip" data-placement="bottom" title="' . lang("ctn_1017") . '"><span class="glyphicon glyphicon-download"></span></a> <a href="' . site_url("documents/close_category/" . $r->ID) . '" class="btn btn-success btn-xs" data-toggle="tooltip" data-placement="bottom" title="' . lang("ctn_1016") . '"><span class="glyphicon glyphicon-download"></span></a>'
            );
        }

        //log_message("debug", json_encode($this->datatables->process()));

        echo json_encode($this->datatables->process());
    }

    public function your_assignments() {
        $this->template->loadData("activeLink", array("classes" => array("your_assignments" => 1)));

        $this->template->loadContent("classes/your_assignments.php", array(
                )
        );
    }

    public function your_assignment_page() {
        $this->load->library("datatables");

        $this->datatables->set_default_order("class_assignments.due_date", "asc");

        // Set page ordering options that can be used
        $this->datatables->ordering(
                array(
                    0 => array(
                        "class_assignments.title" => 0
                    ),
                    1 => array(
                        "class_assignments.due_date" => 0
                    ),
                    2 => array(
                        "classes.name" => 0
                    ),
                    3 => array(
                        "class_assignments.type" => 0
                    ),
                    4 => array(
                        "user_assignments.mark" => 0
                    ),
                    5 => array(
                        "user_assignments.timestamp" => 0
                    )
                )
        );

        $this->datatables->set_total_rows(
                $this->classes_model
                        ->get_your_assignments_total($this->user->info->ID)
        );
        $assign = $this->classes_model
                ->get_your_assignments_dt($this->user->info->ID, $this->datatables);

        $classes = array();
        foreach ($assign->result() as $r) {
            $classes[] = $r->classid;
        }

        $classes = array_unique($classes);
        $grades = $this->classes_model->get_grades_classes($classes);
        $gradesArr = array();
        foreach ($grades->result() as $grade) {
            if (isset($gradesArr[$grade->classid])) {
                $gradesArr[$grade->classid][] = array(
                    "grade" => $grade->grade,
                    "min_score" => $grade->min_score,
                    "max_score" => $grade->max_score
                );
            } else {
                $gradesArr[$grade->classid] = array();
                $gradesArr[$grade->classid][] = array(
                    "grade" => $grade->grade,
                    "min_score" => $grade->min_score,
                    "max_score" => $grade->max_score
                );
            }
        }


        foreach ($assign->result() as $r) {

            if (isset($gradesArr[$r->classid])) {
                $grades = $gradesArr[$r->classid];
            } else {
                $grades = array();
            }

            if (!isset($r->mark)) {
                $grade = "N/A";
                $date = "<label class='label label-danger'>" . lang("ctn_823") . "</label>";
            } else {
                $grade = $r->mark . " / " . $r->max_mark . " ( " . $this->calculate_grade($r->mark, $r->max_mark, $grades) . ")";
                $date = date($this->settings->info->date_format, $r->timestamp);
            }

            if ($r->type == 0) {
                $type = lang("ctn_550");
            } elseif ($r->type == 1) {
                $type = lang("ctn_551");
            }

            $this->datatables->data[] = array(
                $r->title,
                date($this->settings->info->date_format, $r->due_date),
                $r->name,
                $type,
                $grade,
                $date,
                '<a href="' . site_url("classes/view_assignment/" . $r->ID) . '" class="btn btn-primary btn-xs">' . lang("ctn_552") . '</a>'
            );
        }

        echo json_encode($this->datatables->process());
    }

    public function timetable($id) {
        $id = intval($id);
        $class = $this->classes_model->get_class($id);
        if ($class->num_rows() == 0) {
            $this->template->error(lang("error_92"));
        }

        $class = $class->row();

        $rooms = $this->classes_model->get_rooms();

        $this->template->loadExternal(
                '<link rel="stylesheet" href="' . base_url() . 'scripts/libraries/datetimepicker/jquery.datetimepicker.css" />
			<script src="' . base_url() . 'scripts/libraries/datetimepicker/jquery.datetimepicker.full.min.js"></script>
			<link rel="stylesheet" href="' . base_url() . 'scripts/libraries/fullcalendar/fullcalendar.min.css" />
			<script src="' . base_url() . 'scripts/libraries/fullcalendar/lib/moment.min.js"></script>
			<script src="' . base_url() . 'scripts/libraries/fullcalendar/fullcalendar.min.js"></script>
			<script src="' . base_url() . 'scripts/libraries/fullcalendar/gcal.js"></script>
			<script src="' . base_url() . 'scripts/libraries/jscolor.min.js"></script>
			<link rel="stylesheet" href="' . base_url() . 'styles/calendar.css" />'
        );


        // Check if member
        $member_flag = false;
        $teacher_flag = false;
        $member = $this->classes_model
                ->get_class_student_user($this->user->info->ID, $id);
        if ($member->num_rows() == 0) {
            $member_flag = false;
        } else {
            $member = $member->row();
            if ($member->teacher_flag) {
                $teacher_flag = true;
            }
            $member_flag = true;
        }


        $this->template->loadContent("classes/timetable.php", array(
            "class" => $class,
            "rooms" => $rooms,
            "member_flag" => $member_flag,
            "teacher_flag" => $teacher_flag
                )
        );
    }

    public function get_class_events() {
        $start = $this->common->nohtml($this->input->get("start"));
        $end = $this->common->nohtml($this->input->get("end"));
        $classid = intval($this->input->get("classid"));

        $startdt = new DateTime('now'); // setup a local datetime
        $startdt->setTimestamp($start); // Set the date based on timestamp
        $format = $startdt->format('Y-m-d H:i:s');

        $enddt = new DateTime('now'); // setup a local datetime
        $enddt->setTimestamp($end); // Set the date based on timestamp
        $format2 = $enddt->format('Y-m-d H:i:s');


        $class = $this->classes_model->get_class($classid);
        if ($class->num_rows() == 0) {
            $this->template->error(lang("error_92"));
        }

        $class = $class->row();

        // Global calendar
        $class_name = $class->name;

        $event_ids[] = 0;

        $events = $this->classes_model->get_class_events($format, $format2, $classid, $event_ids);
        $data_events = array();
        foreach ($events->result() as $r) {
            $data_events[] = array(
                "id" => $r->ID,
                "title" => $r->title . " ($r->room)",
                "description" => $r->description,
                "end" => $r->end,
                "start" => $r->start,
                "color" => "#" . $r->color,
                "classid" => $classid,
                "class_name" => $class_name,
                "room" => $r->room,
                "lesson_flag" => $r->lesson_flag
            );
        }

        echo json_encode(array("events" => $data_events));
        exit();
    }

    /**

     * Method to handle overall timetable action event   /
     */
    public function overall_events() {
        $classes = $this->classes_model->get_all_classes()->result();

        $this->template->loadExternal(
                '<link rel="stylesheet" href="' . base_url() . 'scripts/libraries/full/fullcalendar.min.css" />
                <link rel="stylesheet" href="' . base_url() . 'scripts/libraries/full/fullcalendar.print.min.css" media="print" type="text/css" />
		<script src="' . base_url() . 'scripts/libraries/full/lib/moment.min.js"></script>
		<script src="' . base_url() . 'scripts/libraries/full/fullcalendar.min.js"></script>
                <script src="' . base_url() . 'scripts/libraries/full/gcal.min.js"></script>
                <script src="' . base_url() . 'scripts/libraries/full/locale-all.js"></script>
                <script src="' . base_url() . 'scripts/libraries/jscolor.min.js"></script>'
        );

        $this->template->loadContent("classes/all_events.php", array(
            "classes" => $classes
                )
        );
    }

    public function add_class_event($id) {
        $id = intval($id);
        $class = $this->classes_model->get_class($id);
        if ($class->num_rows() == 0) {
            $this->template->error(lang("error_92"));
        }

        $class = $class->row();

        if ($this->settings->info->teacher_class_manage) {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $member = $this->classes_model
                        ->get_class_student_user($this->user->info->ID, $class->ID);
                if ($member->num_rows() == 0) {
                    $this->template->error(lang("error_2"));
                }
                $member = $member->row();
                if (!$member->teacher_flag) {
                    $this->template->error(lang("error_2"));
                }
            }
        } else {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $this->template->error(lang("error_2"));
            }
        }

        $name = $this->common->nohtml($this->input->post("name"));
        $desc = $this->common->nohtml($this->input->post("description"));
        $start_date = $this->common->nohtml($this->input->post("start_date"));
        $end_date = $this->common->nohtml($this->input->post("end_date"));
        $room = $this->common->nohtml($this->input->post("room"));
        $color = $this->common->nohtml($this->input->post("color"));

        if (empty($name)) {
            $this->template->error(lang("error_81"));
        }

        if (!empty($start_date)) {
            $sd = DateTime::createFromFormat($this->settings->info->calendar_picker_format, $start_date);
            $start_date = $sd->format('Y-m-d H:i:s');
            $start_date_timestamp = $sd->getTimestamp();
        } else {
            $start_date = date("Y-m-d H:i:s", time());
            $start_date_timestamp = time();
        }

        if (!empty($end_date)) {
            $ed = DateTime::createFromFormat($this->settings->info->calendar_picker_format, $end_date);
            $end_date = $ed->format('Y-m-d H:i:s');
            $end_date_timestamp = $ed->getTimestamp();
        } else {
            $end_date = date("Y-m-d H:i:s", time());
            $end_date_timestamp = time();
        }

        $check = $this->classes_model->get_room_events($start_date, $end_date, $room);
        //log_message('debug', $check->num_rows());

        if ($check->num_rows() > 0) {
            $this->template->error(lang("error_200"));
        }

        $this->classes_model->add_class_event(array(
            "title" => $name,
            "description" => $desc,
            "start" => $start_date,
            "end" => $end_date,
            "userid" => $this->user->info->ID,
            "classid" => $id,
            "room" => $room,
            "color" => $color
                )
        );

        $this->session->set_flashdata("globalmsg", lang("success_80"));
        redirect(site_url("classes/timetable/" . $id));
    }

    public function update_class_event($classid) {
        $eventid = intval($this->input->post("eventid"));
        $event = $this->classes_model->get_class_event($eventid);
        if ($event->num_rows() == 0) {
            $this->template->error(lang("error_116"));
        }

        $event->row();

        if ($this->settings->info->teacher_class_manage) {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $member = $this->classes_model
                        ->get_class_student_user($this->user->info->ID, $event->classid);
                if ($member->num_rows() == 0) {
                    $this->template->error(lang("error_2"));
                }
                $member = $member->row();
                if (!$member->teacher_flag) {
                    $this->template->error(lang("error_2"));
                }
            }
        } else {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $this->template->error(lang("error_2"));
            }
        }

        /* Our calendar data */
        $name = $this->common->nohtml($this->input->post("name"));
        $desc = $this->common->nohtml($this->input->post("description"));
        $start_date = $this->common->nohtml($this->input->post("start_date"));
        $end_date = $this->common->nohtml($this->input->post("end_date"));
        $room = $this->common->nohtml($this->input->post("room"));
        $delete = intval($this->input->post("delete"));
        $color = $this->common->nohtml($this->input->post("color"));

        if (!$delete) {
            if (empty($name)) {
                $this->template->error(lang("error_81"));
            }

            if (!empty($start_date)) {
                $sd = DateTime::createFromFormat($this->settings->info->calendar_picker_format, $start_date);
                $start_date = $sd->format('Y-m-d H:i:s');
                $start_date_timestamp = $sd->getTimestamp();
            } else {
                $start_date = date("Y-m-d\TH:i:s", time());
                $start_date_timestamp = time();
            }

            if (!empty($end_date)) {
                $ed = DateTime::createFromFormat($this->settings->info->calendar_picker_format, $end_date);
                $end_date = $ed->format('Y-m-d H:i:s');
                $end_date_timestamp = $ed->getTimestamp();
            } else {
                $this->template->error(lang("error_78"));
            }

            $this->classes_model->update_class_event($eventid, array(
                "title" => $name,
                "description" => $desc,
                "start" => $start_date,
                "end" => $end_date,
                "room" => $room,
                "color" => $color
                    )
            );
            $this->session->set_flashdata("globalmsg", lang("success_81"));
        } else {
            $this->classes_model->delete_class_event($eventid);
            $this->session->set_flashdata("globalmsg", lang("success_82"));
        }
        redirect(site_url("classes/timetable/" . $classid));
    }

    public function your_timetable() {
        $this->template->loadExternal(
                '<link rel="stylesheet" href="' . base_url() . 'scripts/libraries/datetimepicker/jquery.datetimepicker.css" />
			<script src="' . base_url() . 'scripts/libraries/datetimepicker/jquery.datetimepicker.full.min.js"></script>
			<link rel="stylesheet" href="' . base_url() . 'scripts/libraries/fullcalendar/fullcalendar.min.css" />
			<script src="' . base_url() . 'scripts/libraries/fullcalendar/lib/moment.min.js"></script>
			<script src="' . base_url() . 'scripts/libraries/fullcalendar/fullcalendar.min.js"></script>
			<script src="' . base_url() . 'scripts/libraries/fullcalendar/gcal.js"></script>
			<script src="' . base_url() . 'scripts/libraries/jscolor.min.js"></script>
			<link rel="stylesheet" href="' . base_url() . 'styles/calendar.css" />'
        );

        $this->template->loadData("activeLink", array("classes" => array("your_timetable" => 1)));

        // Get user's classes
        $classes = $this->classes_model->get_user_classes($this->user->info->ID);
        $classes_events = array();
        foreach ($classes->result() as $r) {
            $classes_events[] = array("classid" => $r->classid);
        }

        $this->template->loadContent("classes/your_timetable.php", array(
            "classes" => $classes_events
                )
        );
    }

    public function add_class_books($id) {
        $id = intval($id);
        $class = $this->classes_model->get_class($id);
        if ($class->num_rows() == 0) {
            $this->template->error(lang("error_92"));
        }

        $class = $class->row();

        if ($this->settings->info->teacher_class_manage) {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $member = $this->classes_model
                        ->get_class_student_user($this->user->info->ID, $class->ID);
                if ($member->num_rows() == 0) {
                    $this->template->error(lang("error_2"));
                }
                $member = $member->row();
                if (!$member->teacher_flag) {
                    $this->template->error(lang("error_2"));
                }
            }
        } else {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $this->template->error(lang("error_2"));
            }
        }

        $title = $this->common->nohtml($this->input->post("title"));

        // Check
        $book = $this->library_model->get_book_by_name($title);
        if ($book->num_rows() == 0) {
            $this->template->error(lang("error_117"));
        }

        $book = $book->row();

        $this->classes_model->add_reading_book(array(
            "classid" => $id,
            "bookid" => $book->ID
                )
        );

        $this->session->set_flashdata("globalmsg", lang("success_83"));
        redirect(site_url("classes/view/" . $id));
    }

    public function add_class_file($id) {
        $id = intval($id);
        $class = $this->classes_model->get_class($id);
        if ($class->num_rows() == 0) {
            $this->template->error(lang("error_92"));
        }

        $class = $class->row();

        if ($this->settings->info->teacher_class_manage) {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $member = $this->classes_model
                        ->get_class_student_user($this->user->info->ID, $class->ID);
                if ($member->num_rows() == 0) {
                    $this->template->error(lang("error_2"));
                }
                $member = $member->row();
                if (!$member->teacher_flag) {
                    $this->template->error(lang("error_2"));
                }
            }
        } else {
            if (!$this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $this->template->error(lang("error_2"));
            }
        }

        $name = $this->common->nohtml($this->input->post("name"));

        $this->load->model("files_model");

        // Check
        $file = $this->files_model->get_file_by_name($name);
        if ($file->num_rows() == 0) {
            $this->template->error(lang("error_118"));
        }

        $file = $file->row();

        $this->classes_model->add_class_file(array(
            "classid" => $id,
            "fileid" => $file->ID
                )
        );

        $this->session->set_flashdata("globalmsg", lang("success_84"));
        redirect(site_url("classes/view/" . $id));
    }

    private function get_user_flags($classid) {
        $member_flag = false;
        $teacher_flag = false;

        $member = $this->classes_model
                ->get_class_student_user($this->user->info->ID, $classid);
        if ($member->num_rows() > 0) {
            $member = $member->row();
            if ($member->teacher_flag) {
                $teacher_flag = true;
            }
            $member_flag = true;
        }

        if ($this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
            $member_flag = true;
            $teacher_flag = true;
        }

        return array("member_flag" => $member_flag, "teacher_flag" => $teacher_flag);
    }

}

?>