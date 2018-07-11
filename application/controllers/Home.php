<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Home extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (defined('REQUEST') && REQUEST == "external") {
            return;
        }
        $this->template->loadData("activeLink", array("home" => array("general" => 1)));
        $this->load->model("user_model");
        $this->load->model("home_model");
        $this->load->model("finance_model");
        $this->load->model("classes_model");
        if (!$this->user->loggedin) {
            redirect(site_url("login"));
        }
    }

    public function index() {
        $stats = $this->home_model->get_home_stats();
        if ($stats->num_rows() == 0) {
            $this->template->error(lang("error_24"));
        } else {
            $stats = $stats->row();
            if ($stats->timestamp < time() - $this->settings->info->cache_time) {
                $stats = $this->get_fresh_results($stats);
                // Update Row
                $this->home_model->update_home_stats(array(
                    "students" => $stats->students,
                    "teachers" => $stats->teachers,
                    "classes" => $stats->classes
                        )
                );
            }
        }

        $months = array();
        $total_revenue = 0;
        $current_month = 12;
        $current_year = date("Y");
        // First month
        for ($i = 11; $i >= 0; $i--) {
            // Get month in the past
            $new_month = $current_month - $i;


            // Get month name using mktime
            $timestamp = mktime(0, 0, 0, $new_month, 1, $current_year);
            $count = $this->finance_model->get_sum_for_month(
                    $new_month, $current_year, 1);
            $total_revenue += $count;
            $months[] = array(
                "date" => date("F", $timestamp),
                "count" => $count
            );
        }
        $income = $months;


        $months = array();
        $current_month = 12;
        $current_year = date("Y");
        $total_expense = 0;
        // First month
        for ($i = 11; $i >= 0; $i--) {
            // Get month in the past
            $new_month = $current_month - $i;


            // Get month name using mktime
            $timestamp = mktime(0, 0, 0, $new_month, 1, $current_year);
            $count = $this->finance_model->get_sum_for_month(
                    $new_month, $current_year, 0);
            $total_expense += $count;
            $count *= -1;
            $months[] = array(
                "date" => date("F", $timestamp),
                "count" => $count
            );
        }
        $expense = $months;

        $profit = $total_revenue + $total_expense;

        /* $this->template->loadExternal(
          '<script type="text/javascript" src="'
          . base_url() . 'scripts/libraries/Chart.min.js" /></script>
          <script type="text/javascript" src="'
          . base_url() . 'scripts/libraries/jquery.animateNumber.min.js" />
          </script>
          <link rel="stylesheet" href="' . base_url() . 'scripts/libraries/datetimepicker/jquery.datetimepicker.css" />
          <script src="' . base_url() . 'scripts/libraries/datetimepicker/jquery.datetimepicker.full.min.js"></script>
          <link rel="stylesheet" href="' . base_url() . 'scripts/libraries/fullcalendar/fullcalendar.min.css" />
          <script src="' . base_url() . 'scripts/libraries/fullcalendar/lib/moment.min.js"></script>
          <script src="' . base_url() . 'scripts/libraries/fullcalendar/fullcalendar.min.js"></script>
          <script src="' . base_url() . 'scripts/libraries/fullcalendar/gcal.js"></script>
          <script src="' . base_url() . 'scripts/libraries/jscolor.min.js"></script>
          <link rel="stylesheet" href="' . base_url() . 'styles/calendar.css" />'
          ); */

        $this->template->loadExternal(
                '<link rel="stylesheet" href="' . base_url() . 'scripts/libraries/datetimepicker/jquery.datetimepicker.css" />
		<script src="' . base_url() . 'scripts/libraries/datetimepicker/jquery.datetimepicker.full.min.js"></script>
                <link rel="stylesheet" href="' . base_url() . 'scripts/libraries/full/fullcalendar.min.css" />
                <link rel="stylesheet" href="' . base_url() . 'scripts/libraries/full/fullcalendar.print.min.css" media="print" type="text/css" />
		<script src="' . base_url() . 'scripts/libraries/full/lib/moment.min.js"></script>
		<script src="' . base_url() . 'scripts/libraries/full/fullcalendar.min.js"></script>
                <script src="' . base_url() . 'scripts/libraries/full/gcal.min.js"></script>
                <script src="' . base_url() . 'scripts/libraries/full/locale-all.js"></script>
                <script src="' . base_url() . 'scripts/libraries/jscolor.min.js"></script>'
        );

        $online_count = $this->user_model->get_online_count();

        $news = $this->home_model->get_news(8);
        $new_news = [];
        foreach($news->result() as $r){
            $rls = json_decode($r->roles);
            if($rls){
                if(in_array($this->user->info->user_role, $rls)){
                    $new_news[] = [
                        "username" => $r->username,
                        "avatar" => $r->avatar,
                        "online_timestamp" => $r->online_timestamp,
                        "ID" => $r->ID,
                        "title" => $r->title
                    ];
                }
            }
        }
        $assignments = $this->home_model->get_user_assignments($this->user->info->ID, 4);

        $classes = $this->classes_model->get_user_classes($this->user->info->ID);

        $classes_events = array();
        foreach ($classes->result() as $r) {
            $classes_events[] = array("classid" => $r->classid);
        }

        if ($this->user->info->admin || $this->user->info->reception_manager) {
            $all_classes = $this->classes_model->get_all_classes();
            foreach ($all_classes->result() as $r) {
                $classes_events[] = array("classid" => $r->ID);
            }
        }
        
        $todays_student_count = $this->classes_model->get_total_students_count();
        $todays_classes = $this->classes_model->get_todays_classes();
        $todays_classes_ids = array();
        foreach($todays_classes->result() as $class){
            $todays_classes_ids[] = $class->ID;
        }
        if(empty($todays_classes_ids)){
            $todays_classes_ids[] = 0;
        }
        $todays_missing_student_count = $this->classes_model->get_total_missing_students_count($todays_classes_ids);

        $birthday_data = $this->user_model->get_users_birthday();
        $birthdays = $birthday_data->result();

        $invoices = $this->home_model->get_user_invoices($this->user->info->ID, 4);

        $children = null;

        if ($this->common->has_permissions(array("parent"), $this->user)) {
            $children = $this->user_model->get_parent_children($this->user->info->ID);
        }

        $this->template->loadContent("home/index.php", array(
            "stats" => $stats,
            "online_count" => $online_count,
            "todays_student_count" => $todays_student_count,
            "todays_missing_student_count" => $todays_missing_student_count,
            "income" => $income,
            "expense" => $expense,
            "total_revenue" => $total_revenue,
            "total_expense" => $total_expense,
            "profit" => $profit,
            //"news" => $news,
            "news" => $new_news,
            "assignments" => $assignments,
            "classes_events" => $classes_events,
            "invoices" => $invoices,
            "classes" => $classes,
            "birthdays" => $birthdays,
            "children" => $children
                )
        );
    }

    private function get_fresh_results($stats) {
        $data = new STDclass;

        $data->teachers = $this->user_model->get_user_role_count("teacher");
        $data->students = $this->user_model->get_user_role_count("student");
        $data->classes = $this->home_model->get_class_count();

        return $data;
    }

    public function change_language() {
        $languages = $this->config->item("available_languages");
        if (!isset($_COOKIE['language'])) {
            $lang = "";
        } else {
            $lang = $_COOKIE["language"];
        }
        $this->template->loadContent("home/change_language.php", array(
            "languages" => $languages,
            "user_lang" => $lang
                )
        );
    }

    public function change_language_pro() {
        $lang = $this->common->nohtml($this->input->post("language"));
        $languages = $this->config->item("available_languages");

        if (!array_key_exists($lang, $languages)) {
            $this->template->error(lang("error_25"));
        }

        setcookie("language", $lang, time() + 3600 * 7, "/");
        $this->session->set_flashdata("globalmsg", lang("success_14"));
        redirect(site_url());
    }

    public function load_emails() {
        $this->load->model("mail_model");
        $mail = $this->mail_model
                ->get_user_mail_recent($this->user->info->ID, 5);
        $this->template->loadAjax("home/ajax_emails.php", array(
            "mail" => $mail
                ), 0
        );
    }

    public function get_usernames() {
        $query = $this->common->nohtml($this->input->get("query"));

        if (!empty($query)) {
            $usernames = $this->user_model->get_usernames($query);
            if ($usernames->num_rows() == 0) {
                echo json_encode(array());
            } else {
                $array = array();
                foreach ($usernames->result() as $r) {
                    $array[] = $r->username;
                }
                echo json_encode($array);
                exit();
            }
        } else {
            echo json_encode(array());
            exit();
        }
    }

    public function load_notifications() {
        $notifications = $this->user_model
                ->get_notifications($this->user->info->ID);
        $this->template->loadAjax("home/ajax_notifications.php", array(
            "notifications" => $notifications
                ), 0
        );
    }

    public function load_notifications_unread() {
        $notifications = $this->user_model
                ->get_notifications_unread($this->user->info->ID);
        $this->template->loadAjax("home/ajax_notifications.php", array(
            "notifications" => $notifications
                ), 0
        );
    }

    public function load_notification($id) {
        $notification = $this->user_model
                ->get_notification($id, $this->user->info->ID);
        if ($notification->num_rows() == 0) {
            $this->template->error(lang("error_123"));
        }
        $noti = $notification->row();
        if (!$noti->status) {
            $this->user_model->update_notification($id, array(
                "status" => 1
                    )
            );
            $this->user_model->update_user($this->user->info->ID, array(
                "noti_count" => $this->user->info->noti_count - 1
                    )
            );
        }

        // redirect
        redirect(site_url($noti->url));
    }

    public function notification_read($id) {
        $notification = $this->user_model
                ->get_notification($id, $this->user->info->ID);
        if ($notification->num_rows() == 0) {
            $this->template->error(lang("error_123"));
        }
        $noti = $notification->row();
        if (!$noti->status) {
            $this->user_model->update_notification($id, array(
                "status" => 1
                    )
            );
            $this->user_model->update_user($this->user->info->ID, array(
                "noti_count" => $this->user->info->noti_count - 1
                    )
            );
        }
        redirect(site_url("home/notifications"));
    }

    public function notification_unread($id) {
        $notification = $this->user_model
                ->get_notification($id, $this->user->info->ID);
        if ($notification->num_rows() == 0) {
            $this->template->error(lang("error_123"));
        }
        $noti = $notification->row();
        if ($noti->status) {
            $this->user_model->update_notification($id, array(
                "status" => 0
                    )
            );
            $this->user_model->update_user($this->user->info->ID, array(
                "noti_count" => $this->user->info->noti_count + 1
                    )
            );
        }
        redirect(site_url("home/notifications"));
    }

    public function notifications() {
        $this->template->loadContent("home/notifications.php", array(
                )
        );
    }

    public function notifications_page() {
        $this->load->library("datatables");

        $this->datatables->set_default_order("user_notifications.timestamp", "desc");

        // Set page ordering options that can be used
        $this->datatables->ordering(
                array(
                    2 => array(
                        "user_notifications.timestamp" => 0
                    )
                )
        );
        $this->datatables->set_total_rows(
                $this->user_model
                        ->get_notifications_all_total($this->user->info->ID)
        );
        $notifications = $this->user_model
                ->get_notifications_all($this->user->info->ID, $this->datatables);



        foreach ($notifications->result() as $r) {
            $msg = '<a href="' . site_url("profile/" . $r->username) . '">' . $r->username . '</a> ' . $r->message;
            $options = '<a href="' . site_url("home/notification_unread/" . $r->ID) . '" class="btn btn-default btn-xs">' . lang("ctn_825") . '</a>';
            if ($r->status != 1) {
                $msg .= ' <label class="label label-danger">' . lang("ctn_827") . '</label>';
                $options = '<a href="' . site_url("home/notification_read/" . $r->ID) . '" class="btn btn-info btn-xs">' . lang("ctn_826") . '</a>';
            }

            $this->datatables->data[] = array(
                $this->common->get_user_display(array("username" => $r->username, "avatar" => $r->avatar, "online_timestamp" => $r->online_timestamp)),
                $msg,
                date($this->settings->info->date_format, $r->timestamp),
                $options . ' <a href="' . site_url("home/load_notification/" . $r->ID) . '" class="btn btn-primary btn-xs">' . lang("ctn_552") . '</a>'
            );
        }
        echo json_encode($this->datatables->process());
    }

    public function process_user() {
        $redirect = $this->common->nohtml($this->input->post("redirect"));
        $userid = intval($this->input->post("userid"));
        $hook = $this->common->nohtml($this->input->post("hook"));

        // user data
        $points = abs($this->input->post("credits"));
        $active = intval($this->input->post("active"));
        
        //check if student
        $student_flag = false;

        // Update user
        $user = $this->user_model->get_user($userid);
        if ($user->num_rows() == 0) {
            $user = null;
        } else {
            $user = $user->row();
        }

        // Hook check
        if ($hook == "edit_parent") {
            // Check for parent permissions
            if (!$this->common->has_permissions(array("admin", "parent_manager"
                            ), $this->user)) {
                $this->template->error(lang("error_2"));
            }

            if (!$user) {
                $this->template->error(lang("error_52"));
            }

            $points = $user->points;
            $active = $user->active;

            // Check user is a parent user role
            $role = $this->user_model->get_users_user_role($user->user_role);
            if ($role->num_rows() == 0) {
                $this->template->error(lang("error_162"));
            }
            $role = $role->row();
            if ($role->parent != 1) {
                $this->template->error(lang("error_162"));
            }

            // Check user role they are updating is a parent user role
            $user_role = intval($this->input->post("user_role"));
            $role = $this->user_model->get_users_user_role($user_role);
            if ($role->num_rows() == 0) {
                $this->template->error(lang("error_163"));
            }
            $role = $role->row();
            if ($role->parent != 1) {
                $this->template->error(lang("error_163"));
            }
        }
        if ($hook == "edit_student") {
            
            $student_flag = true;
            
            // Check for parent permissions
            if (!$this->common->has_permissions(array("admin", "student_manager"
                            ), $this->user)) {
                $this->template->error(lang("error_2"));
            }

            if (!$user) {
                $this->template->error(lang("error_52"));
            }

            $points = $user->points;
            $active = $user->active;

            // Check user is a parent user role
            $role = $this->user_model->get_users_user_role($user->user_role);
            if ($role->num_rows() == 0) {
                $this->template->error(lang("error_114"));
            }
            $role = $role->row();
            if ($role->student != 1) {
                $this->template->error(lang("error_114"));
            }

            // Check user role they are updating is a parent user role
            $user_role = intval($this->input->post("user_role"));
            $role = $this->user_model->get_users_user_role($user_role);
            if ($role->num_rows() == 0) {
                $this->template->error(lang("error_164"));
            }
            $role = $role->row();
            if ($role->student != 1) {
                $this->template->error(lang("error_164"));
            }
        }
        if ($hook == "add_parent") {
            $points = 0;
            $active = 1;
            // Check for parent permissions
            if (!$this->common->has_permissions(array("admin", "parent_manager"
                            ), $this->user)) {
                $this->template->error(lang("error_2"));
            }

            // Check user role they are updating is a parent user role
            $user_role = intval($this->input->post("user_role"));
            $role = $this->user_model->get_users_user_role($user_role);
            if ($role->num_rows() == 0) {
                $this->template->error(lang("error_163"));
            }
            $role = $role->row();
            if ($role->parent != 1) {
                $this->template->error(lang("error_163"));
            }
        }
        if ($hook == "add_student") {
            $student_flag = true;
            $points = 0;
            $active = 1;
            // Check for parent permissions
            if (!$this->common->has_permissions(array("admin", "student_manager"
                            ), $this->user)) {
                $this->template->error(lang("error_2"));
            }

            // Check user role they are updating is a parent user role
            $user_role = intval($this->input->post("user_role"));
            $role = $this->user_model->get_users_user_role($user_role);
            if ($role->num_rows() == 0) {
                $this->template->error(lang("error_164"));
            }
            $role = $role->row();
            if ($role->student != 1) {
                $this->template->error(lang("error_164"));
            }
        }

        if (!$user) {
            // New user add
            $fields = $this->user_model->get_custom_fields(array(
            ));

            $this->load->model("register_model");
            $email = $this->input->post("email", true);
            $first_name = $this->common->nohtml(
                    $this->input->post("first_name", true));
            $last_name = $this->common->nohtml(
                    $this->input->post("last_name", true));
            $fathers_name = $this->common->nohtml(
                    $this->input->post("fathers_name", true));
            $first_name_en = $this->common->nohtml(
                    $this->input->post("first_name_en", true));
            $last_name_en = $this->common->nohtml(
                    $this->input->post("last_name_en", true));
            $fathers_name_en = $this->common->nohtml(
                    $this->input->post("fathers_name_en", true));
            $birth_date = $this->common->nohtml(
                    $this->input->post("birth_date"));
            $pass = $this->common->nohtml(
                    $this->input->post("password", true));
            $pass2 = $this->common->nohtml(
                    $this->input->post("password2", true));
            $username = $this->common->nohtml(
                    $this->input->post("username", true));
            $aboutme = $this->common->nohtml($this->input->post("aboutme"));

            $user_role = intval($this->input->post("user_role"));

            $address_1 = $this->common->nohtml($this->input->post("address_1"));
            $mobile_phone = $this->common->nohtml($this->input->post("mobile_phone"));
            $city = $this->common->nohtml($this->input->post("city"));
            $state = $this->common->nohtml($this->input->post("state"));
            $phone = $this->common->nohtml($this->input->post("phone"));
            $country = $this->common->nohtml($this->input->post("country"));

            //check if fileds are set
            if ($first_name === "") {
                $this->template->error(lang("error_201"));
            }
            if ($last_name === "") {
                $this->template->error(lang("error_202"));
            }
            if ($student_flag && $fathers_name === "") {
                $this->template->error(lang("error_203"));
            }
            if ($student_flag && $first_name_en === "") {
                $this->template->error(lang("error_231"));
            }
            if ($student_flag && $last_name_en === "") {
                $this->template->error(lang("error_232"));
            }
            if ($student_flag && $fathers_name_en === "") {
                $this->template->error(lang("error_233"));
            }
            if ($student_flag && empty($birth_date)) {
                $this->template->error(lang("error_209"));
            } else {
                $time = strtotime(str_replace("/", ".", $birth_date));
                $birth_date = date('Y-m-d', $time);
            }
            if ($student_flag && $address_1 === "" && $student_flag) {
                $this->template->error(lang("error_204"));
            }
            if ($student_flag && $mobile_phone === "") {
                $this->template->error(lang("error_228"));
            }
            if ($student_flag && $phone === "") {
                $this->template->error(lang("error_229"));
            }
            if ($student_flag && $city === "") {
                $this->template->error(lang("error_206"));
            }
            if ($student_flag && $state === "") {
                $this->template->error(lang("error_207"));
            }
            if ($student_flag && $country === "") {
                $this->template->error(lang("error_208"));
            }

            if (strlen($username) < 3)
                $this->template->error(lang("error_14"));

            if (!preg_match("/^[a-z0-9_]+$/i", $username)) {
                $this->template->error(lang("error_15"));
            }

            if (!$this->register_model->check_username_is_free($username)) {
                $this->template->error(lang("error_16"));
            }
            if ($pass != $pass2) {
                $this->template->error(lang("error_44"));
            }
            if (strlen($pass) <= 5) {
                $this->template->error(lang("error_17"));
            }
            $pass = $this->common->encrypt($pass);


            $this->load->helper('email');
            $this->load->library('upload');

            if (empty($email)) {
                //$this->template->error(lang("error_18"));
                $email = $username . '@dayan.ru';
            }

            if (!valid_email($email)) {
                $this->template->error(lang("error_19"));
            }

            if (!$this->register_model->checkEmailIsFree($email)) {
                $this->template->error(lang("error_20"));
            }


            if ($_FILES['userfile']['size'] > 0) {
                $this->upload->initialize(array(
                    "upload_path" => $this->settings->info->upload_path,
                    "overwrite" => FALSE,
                    "max_filename" => 300,
                    "encrypt_name" => TRUE,
                    "remove_spaces" => TRUE,
                    "allowed_types" => "gif|jpg|png|jpeg|",
                    "max_size" => 1000,
                ));

                if (!$this->upload->do_upload()) {
                    $this->template->error(lang("error_21")
                            . $this->upload->display_errors());
                }

                $data = $this->upload->data();

                $image = $data['file_name'];
            } else {
                $image = "default.png";
            }

            // Custom Fields
            // Process fields
            $answers = array();
            foreach ($fields->result() as $r) {
                $answer = "";
                if ($r->type == 0) {
                    // Look for simple text entry
                    $answer = $this->common->nohtml($this->input->post("cf_" . $r->ID));

                    if ($r->required && empty($answer)) {
                        $this->template->error(lang("error_78") . $r->name);
                    }
                    // Add
                    $answers[] = array(
                        "fieldid" => $r->ID,
                        "answer" => $answer
                    );
                } elseif ($r->type == 1) {
                    // HTML
                    $answer = $this->common->nohtml($this->input->post("cf_" . $r->ID));

                    if ($r->required && empty($answer)) {
                        $this->template->error(lang("error_78") . $r->name);
                    }
                    // Add
                    $answers[] = array(
                        "fieldid" => $r->ID,
                        "answer" => $answer
                    );
                } elseif ($r->type == 2) {
                    // Checkbox
                    $options = explode(",", $r->options);
                    foreach ($options as $k => $v) {
                        // Look for checked checkbox and add it to the answer if it's value is 1
                        $ans = $this->common->nohtml($this->input->post("cf_cb_" . $r->ID . "_" . $k));
                        if ($ans) {
                            if (empty($answer)) {
                                $answer .= $v;
                            } else {
                                $answer .= ", " . $v;
                            }
                        }
                    }

                    if ($r->required && empty($answer)) {
                        $this->template->error(lang("error_78") . $r->name);
                    }
                    $answers[] = array(
                        "fieldid" => $r->ID,
                        "answer" => $answer
                    );
                } elseif ($r->type == 3) {
                    // radio
                    $options = explode(",", $r->options);
                    if (isset($_POST['cf_radio_' . $r->ID])) {
                        $answer = intval($this->common->nohtml($this->input->post("cf_radio_" . $r->ID)));

                        $flag = false;
                        foreach ($options as $k => $v) {
                            if ($k == $answer) {
                                $flag = true;
                                $answer = $v;
                            }
                        }
                        if ($r->required && !$flag) {
                            $this->template->error(lang("error_78") . $r->name);
                        }
                        if ($flag) {
                            $answers[] = array(
                                "fieldid" => $r->ID,
                                "answer" => $answer
                            );
                        }
                    }
                } elseif ($r->type == 4) {
                    // Dropdown menu
                    $options = explode(",", $r->options);
                    $answer = intval($this->common->nohtml($this->input->post("cf_" . $r->ID)));
                    $flag = false;
                    foreach ($options as $k => $v) {
                        if ($k == $answer) {
                            $flag = true;
                            $answer = $v;
                        }
                    }
                    if ($r->required && !$flag) {
                        $this->template->error(lang("error_78") . $r->name);
                    }
                    if ($flag) {
                        $answers[] = array(
                            "fieldid" => $r->ID,
                            "answer" => $answer
                        );
                    }
                } elseif ($r->type == 5) {
                    $answer = $this->common->nohtml($this->input->post("cf_" . $r->ID));

                    if ($r->required && empty($answer)) {
                        $this->template->error(lang("error_78") . $r->name);
                    }
                    if (!empty($answer)) {
                        $sd = DateTime::createFromFormat($this->settings->info->date_picker_format, $answer);
                        $start_date = $sd->format('Y-m-d H:i:s');
                        $start_date_timestamp = $sd->getTimestamp();
                    } else {
                        $start_date_timestamp = 0;
                    }

                    // Add
                    $answers[] = array(
                        "fieldid" => $r->ID,
                        "answer" => $start_date_timestamp
                    );
                }
            }


            $userid = $this->register_model->add_user(array(
                "username" => $username,
                "email" => $email,
                "first_name" => $first_name,
                "last_name" => $last_name,
                "fathers_name" => $fathers_name,
                "first_name_en" => $first_name_en,
                "last_name_en" => $last_name_en,
                "fathers_name_en" => $fathers_name_en,
                "birth_date" => $birth_date,
                "password" => $pass,
                "avatar" => $image,
                "aboutme" => $aboutme,
                "points" => $points,
                "active" => $active,
                "address_line_1" => $address_1,
                "mobile_phone" => $mobile_phone,
                "city" => $city,
                "state" => $state,
                "phone" => $phone,
                "country" => $country,
                "user_role" => $user_role,
                "IP" => $_SERVER['REMOTE_ADDR'],
                "joined" => time(),
                "joined_date" => date("n-Y")
                    )
            );

            // Update CF
            // Add Custom Fields data
            foreach ($answers as $answer) {
                $this->user_model->add_custom_field(array(
                    "userid" => $userid,
                    "fieldid" => $answer['fieldid'],
                    "value" => $answer['answer']
                        )
                );
            }


            $this->session->set_flashdata("globalmsg", lang("success_131"));
            redirect(site_url($redirect));
        } else {
            // Update user
            $fields = $this->user_model->get_custom_fields_answers(array(
                    ), $userid);

            $this->load->model("register_model");
            $email = $this->input->post("email", true);
            $first_name = $this->common->nohtml(
                    $this->input->post("first_name", true));
            $last_name = $this->common->nohtml(
                    $this->input->post("last_name", true));
            $fathers_name = $this->common->nohtml(
                    $this->input->post("fathers_name", true));
            $first_name_en = $this->common->nohtml(
                    $this->input->post("first_name_en", true));
            $last_name_en = $this->common->nohtml(
                    $this->input->post("last_name_en", true));
            $fathers_name_en = $this->common->nohtml(
                    $this->input->post("fathers_name_en", true));
            $birth_date = $this->common->nohtml(
                    $this->input->post("birth_date", true));
            $pass = $this->common->nohtml(
                    $this->input->post("password", true));
            $username = $this->common->nohtml(
                    $this->input->post("username", true));
            $aboutme = $this->common->nohtml($this->input->post("aboutme"));
            $points = abs($this->input->post("credits"));

            $user_role = intval($this->input->post("user_role"));

            $address_1 = $this->common->nohtml($this->input->post("address_1"));
            $mobile_phone = $this->common->nohtml($this->input->post("mobile_phone"));
            $city = $this->common->nohtml($this->input->post("city"));
            $state = $this->common->nohtml($this->input->post("state"));
            $phone = $this->common->nohtml($this->input->post("phone"));
            $country = $this->common->nohtml($this->input->post("country"));

            //check if fileds are set
            if ($first_name === "") {
                $this->template->error(lang("error_201"));
            }
            if ($last_name === "") {
                $this->template->error(lang("error_202"));
            }
            if ($student_flag && $fathers_name === "") {
                $this->template->error(lang("error_203"));
            }
            if ($student_flag && $first_name_en === "") {
                $this->template->error(lang("error_231"));
            }
            if ($student_flag && $last_name_en === "") {
                $this->template->error(lang("error_232"));
            }
            if ($student_flag && $fathers_name_en === "") {
                $this->template->error(lang("error_233"));
            }
            if ($student_flag && empty($birth_date)) {
                $this->template->error(lang("error_209"));
            } else {
                $time = strtotime(str_replace("/", ".", $birth_date));
                $birth_date = date('Y-m-d', $time);
            }
            if ($student_flag && $address_1 === "") {
                $this->template->error(lang("error_204"));
            }
            if ($student_flag && $city === "") {
                $this->template->error(lang("error_206"));
            }
            if ($student_flag && $state === "") {
                $this->template->error(lang("error_207"));
            }
            if ($student_flag && $country === "") {
                $this->template->error(lang("error_208"));
            }

            if (strlen($username) < 3)
                $this->template->error(lang("error_14"));

            if (!preg_match("/^[a-z0-9_]+$/i", $username)) {
                $this->template->error(lang("error_15"));
            }

            if ($username != $user->username) {
                if (!$this->register_model->check_username_is_free($username)) {
                    $this->template->error(lang("error_16"));
                }
            }

            if (!empty($pass)) {
                if (strlen($pass) <= 5) {
                    $this->template->error(lang("error_17"));
                }
                $pass = $this->common->encrypt($pass);
            } else {
                $pass = $user->password;
            }

            $this->load->helper('email');
            $this->load->library('upload');

            if (empty($email)) {
                $this->template->error(lang("error_18"));
            }

            if (!valid_email($email)) {
                $this->template->error(lang("error_19"));
            }

            if ($email != $user->email) {
                if (!$this->register_model->checkEmailIsFree($email)) {
                    $this->template->error(lang("error_20"));
                }
            }

            if ($_FILES['userfile']['size'] > 0) {
                $this->upload->initialize(array(
                    "upload_path" => $this->settings->info->upload_path,
                    "overwrite" => FALSE,
                    "max_filename" => 300,
                    "encrypt_name" => TRUE,
                    "remove_spaces" => TRUE,
                    "allowed_types" => "gif|jpg|png|jpeg|",
                    "max_size" => 1000,
                ));

                if (!$this->upload->do_upload()) {
                    $this->template->error(lang("error_21")
                            . $this->upload->display_errors());
                }

                $data = $this->upload->data();

                $image = $data['file_name'];
            } else {
                $image = $user->avatar;
            }

            // Custom Fields
            // Process fields
            $answers = array();
            foreach ($fields->result() as $r) {
                $answer = "";
                if ($r->type == 0) {
                    // Look for simple text entry
                    $answer = $this->common->nohtml($this->input->post("cf_" . $r->ID));

                    if ($r->required && empty($answer)) {
                        $this->template->error(lang("error_78") . $r->name);
                    }
                    // Add
                    $answers[] = array(
                        "fieldid" => $r->ID,
                        "answer" => $answer
                    );
                } elseif ($r->type == 1) {
                    // HTML
                    $answer = $this->common->nohtml($this->input->post("cf_" . $r->ID));

                    if ($r->required && empty($answer)) {
                        $this->template->error(lang("error_78") . $r->name);
                    }
                    // Add
                    $answers[] = array(
                        "fieldid" => $r->ID,
                        "answer" => $answer
                    );
                } elseif ($r->type == 2) {
                    // Checkbox
                    $options = explode(",", $r->options);
                    foreach ($options as $k => $v) {
                        // Look for checked checkbox and add it to the answer if it's value is 1
                        $ans = $this->common->nohtml($this->input->post("cf_cb_" . $r->ID . "_" . $k));
                        if ($ans) {
                            if (empty($answer)) {
                                $answer .= $v;
                            } else {
                                $answer .= ", " . $v;
                            }
                        }
                    }

                    if ($r->required && empty($answer)) {
                        $this->template->error(lang("error_78") . $r->name);
                    }
                    $answers[] = array(
                        "fieldid" => $r->ID,
                        "answer" => $answer
                    );
                } elseif ($r->type == 3) {
                    // radio
                    $options = explode(",", $r->options);
                    if (isset($_POST['cf_radio_' . $r->ID])) {
                        $answer = intval($this->common->nohtml($this->input->post("cf_radio_" . $r->ID)));

                        $flag = false;
                        foreach ($options as $k => $v) {
                            if ($k == $answer) {
                                $flag = true;
                                $answer = $v;
                            }
                        }
                        if ($r->required && !$flag) {
                            $this->template->error(lang("error_78") . $r->name);
                        }
                        if ($flag) {
                            $answers[] = array(
                                "fieldid" => $r->ID,
                                "answer" => $answer
                            );
                        }
                    }
                } elseif ($r->type == 4) {
                    // Dropdown menu
                    $options = explode(",", $r->options);
                    $answer = intval($this->common->nohtml($this->input->post("cf_" . $r->ID)));
                    $flag = false;
                    foreach ($options as $k => $v) {
                        if ($k == $answer) {
                            $flag = true;
                            $answer = $v;
                        }
                    }
                    if ($r->required && !$flag) {
                        $this->template->error(lang("error_78") . $r->name);
                    }
                    if ($flag) {
                        $answers[] = array(
                            "fieldid" => $r->ID,
                            "answer" => $answer
                        );
                    }
                } elseif ($r->type == 5) {
                    $answer = $this->common->nohtml($this->input->post("cf_" . $r->ID));

                    if ($r->required && empty($answer)) {
                        $this->template->error(lang("error_78") . $r->name);
                    }
                    if (!empty($answer)) {
                        $sd = DateTime::createFromFormat($this->settings->info->date_picker_format, $answer);
                        $start_date = $sd->format('Y-m-d H:i:s');
                        $start_date_timestamp = $sd->getTimestamp();
                    } else {
                        $start_date_timestamp = 0;
                    }

                    // Add
                    $answers[] = array(
                        "fieldid" => $r->ID,
                        "answer" => $start_date_timestamp
                    );
                }
            }

            $this->user_model->update_user($userid, array(
                "username" => $username,
                "email" => $email,
                "first_name" => $first_name,
                "last_name" => $last_name,
                "fathers_name" => $fathers_name,
                "first_name_en" => $first_name_en,
                "last_name_en" => $last_name_en,
                "fathers_name_en" => $fathers_name_en,
                "birth_date" => $birth_date,
                "password" => $pass,
                "avatar" => $image,
                "aboutme" => $aboutme,
                "points" => $points,
                "active" => $active,
                "address_line_1" => $address_1,
                "mobile_phone" => $mobile_phone,
                "city" => $city,
                "state" => $state,
                "phone" => $phone,
                "country" => $country,
                "user_role" => $user_role
                    )
            );

            // Update CF
            // Add Custom Fields data
            foreach ($answers as $answer) {
                // Check if field exists
                $field = $this->user_model->get_user_cf($answer['fieldid'], $userid);
                if ($field->num_rows() == 0) {
                    $this->user_model->add_custom_field(array(
                        "userid" => $userid,
                        "fieldid" => $answer['fieldid'],
                        "value" => $answer['answer']
                            )
                    );
                } else {
                    $this->user_model->update_custom_field($answer['fieldid'], $userid, $answer['answer']);
                }
            }


            $this->session->set_flashdata("globalmsg", lang("success_132"));
            redirect(site_url($redirect));
        }
    }

}

?>