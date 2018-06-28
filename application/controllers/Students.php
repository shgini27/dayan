<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Students extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model("user_model");
        $this->load->model("students_model");
        $this->load->model("classes_model");
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

        $this->template->loadData("activeLink", array("students" => array("general" => 1)));

        if (!$this->settings->info->students_section) {
            $this->template->error(lang("error_84"));
        }
    }

    public function index() {
        if (!$this->common->has_permissions(array("admin", "student_manager",
                    "student_viewer"), $this->user)) {
            $this->template->error(lang("error_2"));
        }
        $this->template->loadData("activeLink", array("students" => array("general" => 1)));

        $this->template->loadContent("students/index.php", array(
                )
        );
    }

    public function student_page() {
        if (!$this->common->has_permissions(array("admin", "student_manager",
                    "student_viewer"), $this->user)) {
            $this->template->error(lang("error_2"));
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
                )
        );

        $this->datatables->set_total_rows(
                $this->students_model
                        ->get_total_students()
        );
        $users = $this->students_model->get_students($this->datatables);


        foreach ($users->result() as $r) {

            $options = '<a href="' . site_url("students/view/" . $r->ID) . '" class="btn btn-primary btn-xs">' . lang("ctn_552") . '</a>';
            if ($this->common->has_permissions(array("admin", "student_manager"), $this->user)) {
                $options .= ' <a href="' . site_url("students/edit_student/" . $r->ID) . '" class="btn btn-warning btn-xs" data-toggle="tooltip" data-placement="bottom" title="' . lang("ctn_55") . '"><span class="glyphicon glyphicon-cog"></span></a> <a href="' . site_url("students/delete_student/" . $r->ID . "/" . $this->security->get_csrf_hash()) . '" class="btn btn-danger btn-xs" onclick="return confirm(\'' . lang("ctn_317") . '\')" data-toggle="tooltip" data-placement="bottom" title="' . lang("ctn_57") . '"><span class="glyphicon glyphicon-trash"></span></a>';
            }


            $this->datatables->data[] = array(
                $this->common->get_user_display(array("username" => $r->username, "avatar" => $r->avatar, "online_timestamp" => $r->online_timestamp, "first_name" => $r->first_name, "last_name" => $r->last_name)),
                $r->email,
                $options
            );
        }
        echo json_encode($this->datatables->process());
    }

    public function add_student() {
        if (!$this->common->has_permissions(array("admin", "student_manager"), $this->user)) {
            $this->template->error(lang("error_2"));
        }
        
        $this->template->loadExternal(
                '<link rel="stylesheet" href="' . base_url() . 'scripts/libraries/bootstrap-datepicker-1.6.4/css/bootstrap-datepicker3.min.css" />
			<script src="' . base_url() . 'scripts/libraries/bootstrap-datepicker-1.6.4/js/bootstrap-datepicker.min.js"></script>'
        );
        
        $this->template->loadData("activeLink", array("students" => array("general" => 1)));

        $user_roles = $this->user_model->get_student_roles();
        $fields = $this->user_model->get_custom_fields(array());

        $this->template->loadContent("students/add_student.php", array(
            "form" => $this->common->get_user_registration_fields(
                    "add_student", // Hook
                    "students", // Redirect
                    null, // user object
                    $user_roles, // user roles
                    $fields, // custom fields
                    array(
                "new_user" => true,
                "credits" => false,
                "activate_user" => false
                    ) // Flags
            )
                )
        );
    }

    public function edit_student($id) {
        if (!$this->common->has_permissions(array("admin", "student_manager",
                        ), $this->user)) {
            $this->template->error(lang("error_2"));
        }
        $this->template->loadData("activeLink", array("students" => array("general" => 1)));

        $id = intval($id);
        $student = $this->user_model->get_user($id);
        if ($student->num_rows() == 0) {
            $this->template->error(lang("error_52"));
        }

        $student = $student->row();

        // Check user is a parent user role
        $role = $this->user_model->get_users_user_role($student->user_role);
        if ($role->num_rows() == 0) {
            $this->template->error(lang("error_114"));
        }
        $role = $role->row();
        if ($role->student != 1) {
            $this->template->error(lang("error_114"));
        }

        $fields = $this->user_model->get_custom_fields_answers(array(
                ), $id);

        $user_roles = $this->user_model->get_student_roles();

        /* $fields = $this->user_model->get_custom_fields_answers(array(
          ), $id); */

        $this->template->loadExternal(
                '<link rel="stylesheet" href="' . base_url() . 'scripts/libraries/bootstrap-datepicker-1.6.4/css/bootstrap-datepicker3.min.css" />
			<script src="' . base_url() . 'scripts/libraries/bootstrap-datepicker-1.6.4/js/bootstrap-datepicker.min.js"></script>'
        );
        
        $this->template->loadContent("students/edit_student.php", array(
            "form" => $this->common->get_user_registration_fields(
                    "edit_student", "students", $student, $user_roles, $fields, // custom fields
                    array(
                "credits" => false,
                "activate_user" => false
                    ) // Flags
            )
                )
        );
    }

    public function edit_student_pro($id) {
        if (!$this->common->has_permissions(array("admin", "student_manager",
                        ), $this->user)) {
            $this->template->error(lang("error_2"));
        }
        $id = intval($id);
        $student = $this->students_model->get_student($id);
        if ($student->num_rows() == 0) {
            $this->template->error(lang("error_114"));
        }
        $member = $student->row();

        $fields = $this->user_model->get_custom_fields_answers(array(
                ), $id);

        $this->load->model("register_model");
        $email = $this->input->post("email", true);
        $first_name = $this->common->nohtml(
                $this->input->post("first_name", true));
        $last_name = $this->common->nohtml(
                $this->input->post("last_name", true));
        $pass = $this->common->nohtml(
                $this->input->post("password", true));
        $username = $this->common->nohtml(
                $this->input->post("username", true));
        $aboutme = $this->common->nohtml($this->input->post("aboutme"));
        $points = abs($this->input->post("credits"));
        $active = intval($this->input->post("active"));

        $address_1 = $this->common->nohtml($this->input->post("address_1"));
        $address_2 = $this->common->nohtml($this->input->post("address_2"));
        $city = $this->common->nohtml($this->input->post("city"));
        $state = $this->common->nohtml($this->input->post("state"));
        $zipcode = $this->common->nohtml($this->input->post("zipcode"));
        $country = $this->common->nohtml($this->input->post("country"));

        if (strlen($username) < 3)
            $this->template->error(lang("error_14"));

        if (!preg_match("/^[a-z0-9_]+$/i", $username)) {
            $this->template->error(lang("error_15"));
        }

        if ($username != $member->username) {
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
            $pass = $member->password;
        }

        $this->load->helper('email');
        $this->load->library('upload');

        if (empty($email)) {
            $this->template->error(lang("error_18"));
        }

        if (!valid_email($email)) {
            $this->template->error(lang("error_19"));
        }

        if ($email != $member->email) {
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
                "xss_clean" => TRUE,
            ));

            if (!$this->upload->do_upload()) {
                $this->template->error(lang("error_21")
                        . $this->upload->display_errors());
            }

            $data = $this->upload->data();

            $image = $data['file_name'];
        } else {
            $image = $member->avatar;
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


        $this->user_model->update_user($id, array(
            "username" => $username,
            "email" => $email,
            "first_name" => $first_name,
            "last_name" => $last_name,
            "password" => $pass,
            "avatar" => $image,
            "aboutme" => $aboutme,
            "points" => $points,
            "active" => $active,
            "address_line_1" => $address_1,
            "address_line_2" => $address_2,
            "city" => $city,
            "state" => $state,
            "zip" => $zipcode,
            "country" => $country
                )
        );

        // Update CF
        // Add Custom Fields data
        foreach ($answers as $answer) {
            // Check if field exists
            $field = $this->user_model->get_user_cf($answer['fieldid'], $id);
            if ($field->num_rows() == 0) {
                $this->user_model->add_custom_field(array(
                    "userid" => $id,
                    "fieldid" => $answer['fieldid'],
                    "value" => $answer['answer']
                        )
                );
            } else {
                $this->user_model->update_custom_field($answer['fieldid'], $id, $answer['answer']);
            }
        }


        $this->session->set_flashdata("globalmsg", lang("success_114"));
        redirect(site_url("students"));
    }

    public function delete_student($id, $hash) {
        if (!$this->common->has_permissions(array("admin", "student_manager",
                        ), $this->user)) {
            $this->template->error(lang("error_2"));
        }
        if ($hash != $this->security->get_csrf_hash()) {
            $this->template->error(lang("error_6"));
        }
        $id = intval($id);
        $student = $this->students_model->get_student($id);
        if ($student->num_rows() == 0) {
            $this->template->error(lang("error_114"));
        }
        $student = $student->row();

        $this->students_model->delete_student($id);
        $this->session->set_flashdata("globalmsg", lang("success_115"));
        redirect(site_url("students"));
    }
    
    public function delete_dropped_student($id, $hash){
        if (!$this->common->has_permissions(array("admin", "student_manager",
                        ), $this->user)) {
            $this->template->error(lang("error_2"));
        }
        if ($hash != $this->security->get_csrf_hash()) {
            $this->template->error(lang("error_6"));
        }
        $id = intval($id);
        $student = $this->students_model->get_dropped_student($id);
        if ($student->num_rows() == 0) {
            $this->template->error(lang("error_114"));
        }
        $student = $student->row();

        $this->students_model->delete_dropped_student($id);
        $this->session->set_flashdata("globalmsg", lang("success_115"));
        redirect(site_url("students/dropped_students"));
    }

    public function dropped_students() {
        if (!$this->common->has_permissions(array("admin", "student_manager",
                    "student_viewer"), $this->user)) {
            $this->template->error(lang("error_2"));
        }
        $this->template->loadData("activeLink", array("students" => array("dropped" => 1)));

        $this->template->loadContent("students/dropped_students.php", array(
                )
        );
    }
    
    public function dropped_studens_page() {
        if (!$this->common->has_permissions(array("admin", "student_manager",
                    "student_viewer"), $this->user)) {
            $this->template->error(lang("error_2"));
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
                $this->students_model
                        ->get_total_dropped_students()
        );
        $users = $this->students_model->get_dropped_students($this->datatables);


        foreach ($users->result() as $r) {

            $options = '<a href="' . site_url("students/view/" . $r->ID) . '" class="btn btn-primary btn-xs">' . lang("ctn_552") . '</a>';
            if ($this->common->has_permissions(array("admin", "student_manager"), $this->user)) {
                $options .= ' <a href="' . site_url("students/delete_dropped_student/" . $r->ID . "/" . $this->security->get_csrf_hash()) . '" class="btn btn-danger btn-xs" onclick="return confirm(\'' . lang("ctn_317") . '\')" data-toggle="tooltip" data-placement="bottom" title="' . lang("ctn_57") . '"><span class="glyphicon glyphicon-trash"></span></a>';
            }


            $this->datatables->data[] = array(
                $this->common->get_user_display(array("username" => $r->username, "avatar" => $r->avatar, "online_timestamp" => $r->online_timestamp, "first_name" => $r->first_name, "last_name" => $r->last_name)),
                $r->email,
                $r->class_name,
                $r->agreement_number,
                $options
            );
        }
        echo json_encode($this->datatables->process());
    }

    public function view($id) {
        $id = intval($id);
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

        $this->template->loadData("activeLink", array("students" => array("general" => 1)));

        $fields = $this->user_model->get_custom_fields_answers(array(
                ), $student->ID);

        $this->template->loadContent("students/view.php", array(
            "student" => $student,
            "fields" => $fields
                )
        );
    }

    public function view_report($id) {
        $id = intval($id);
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

        $this->template->loadData("activeLink", array("students" => array("general" => 1)));

        $fields = $this->user_model->get_custom_fields_answers(array(
            "report" => 1
                ), $student->ID);

        $classes = $this->classes_model->get_all_user_classes($student->ID);
        $report = $this->students_model->get_student_report($student->ID);

        $this->template->loadContent("students/view_report.php", array(
            "student" => $student,
            "fields" => $fields,
            "classes" => $classes,
            "report" => $report
                )
        );
    }

    public function report_pdf($id) {
        $id = intval($id);
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

        $this->template->loadData("activeLink", array("students" => array("general" => 1)));

        $fields = $this->user_model->get_custom_fields_answers(array(
            "report" => 1
                ), $student->ID);

        $classes = $this->classes_model->get_all_user_classes($student->ID);
        $report = $this->students_model->get_student_report($student->ID);

        ob_start();
        $this->template->loadAjax("students/view_report_pdf.php", array(
            "student" => $student,
            "fields" => $fields,
            "classes" => $classes,
            "report" => $report
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

    public function edit_report($id) {
        if (!$this->common->has_permissions(array("admin", "student_manager",
                    "student_viewer"), $this->user)) {
            $this->template->error(lang("error_2"));
        }
        $id = intval($id);
        $student = $this->students_model->get_student($id);
        if ($student->num_rows() == 0) {
            $this->template->error(lang("error_114"));
        }
        $student = $student->row();

        $this->template->loadData("activeLink", array("students" => array("general" => 1)));

        $notes = $this->lib_filter->go($this->input->post("report"));

        $report = $this->students_model->get_student_report($student->ID);
        if ($report->num_rows() == 0) {
            $this->students_model->add_report(array(
                "userid" => $student->ID,
                "notes" => $notes,
                "timestamp" => time()
                    )
            );
        } else {
            $report = $report->row();
            $this->students_model->update_report($report->ID, array(
                "notes" => $notes,
                "timestamp" => time()
                    )
            );
        }

        $this->session->set_flashdata("globalmsg", lang("success_130"));
        redirect(site_url("students/view_report/" . $student->ID));
    }

    public function view_classes($id) {
        $id = intval($id);
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

        $this->template->loadData("activeLink", array("students" => array("general" => 1)));

        $this->template->loadContent("students/view_classes.php", array(
            "student" => $student,
                )
        );
    }

    public function view_classes_page($id) {
        $id = intval($id);
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

        $this->datatables->set_total_rows(
                $this->classes_model
                        ->get_classes_total_your($id)
        );
        $classes = $this->classes_model->get_classes_your($id, $this->datatables);



        foreach ($classes->result() as $r) {

            $max = $r->students;
            if ($r->max_students > 0) {
                $max = $max . "/" . $r->max_students;
            }

            $options = '<a href="' . site_url("classes/view/" . $r->ID) . '" class="btn btn-primary btn-xs">' . lang("ctn_552") . '</a>';
            if ($this->common->has_permissions(array("admin", "class_manager"), $this->user)) {
                $options .= ' <a href="' . site_url("classes/edit_class/" . $r->ID) . '" class="btn btn-warning btn-xs" data-toggle="tooltip" data-placement="bottom" title="' . lang("ctn_55") . '"><span class="glyphicon glyphicon-cog"></span></a> <a href="' . site_url("classes/delete_class/" . $r->ID . "/" . $this->security->get_csrf_hash()) . '" class="btn btn-danger btn-xs" onclick="return confirm(\'' . lang("ctn_317") . '\')" data-toggle="tooltip" data-placement="bottom" title="' . lang("ctn_57") . '"><span class="glyphicon glyphicon-trash"></span></a>';
            }

            $this->datatables->data[] = array(
                $r->name,
                $r->subject_name,
                $r->cat_name,
                $max,
                $options
            );
        }

        echo json_encode($this->datatables->process());
    }

    public function view_attendance($id) {
        $id = intval($id);
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

        $this->template->loadData("activeLink", array("students" => array("general" => 1)));

        $this->template->loadContent("students/view_attendance.php", array(
            "student" => $student,
                )
        );
    }

    public function view_attendance_page($id) {
        $id = intval($id);
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

        $this->load->library("datatables");

        $this->datatables->set_default_order("attendance_sheet_entries.ID", "desc");

        // Set page ordering options that can be used
        $this->datatables->ordering(
                array(
                    0 => array(
                        "classes.name" => 0
                    ),
                    2 => array(
                        "attendance_sheets.attendance_date" => 0
                    )
                )
        );

        $this->datatables->set_total_rows(
                $this->students_model
                        ->get_student_attendance_total($id)
        );
        $entries = $this->students_model->get_student_attendance($id, $this->datatables);



        foreach ($entries->result() as $r) {

            if ($r->present) {
                $attendance = lang("ctn_496");
            } elseif ($r->absent) {
                $attendance = lang("ctn_497");
            } elseif ($r->late) {
                $attendance = lang("ctn_498");
            } elseif ($r->holiday) {
                $attendance = lang("ctn_499");
            } else {
                $attendance = lang("ctn_836");
            }

            $this->datatables->data[] = array(
                $r->name,
                $attendance,
                $r->title . " " . $r->start,
                date($this->settings->info->date_format, $r->attendance_date),
                $r->notes
            );
        }

        echo json_encode($this->datatables->process());
    }

    public function view_attendance_class_page($id) {
        $id = intval($id);
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

        $this->load->library("datatables");

        $this->datatables->set_default_order("classes.ID", "desc");

        // Set page ordering options that can be used
        $this->datatables->ordering(
                array(
                    0 => array(
                        "classes.name" => 0
                    ),
                    1 => array(
                        "attendance_sheet_entries.present" => 0
                    ),
                    2 => array(
                        "attendance_sheet_entries.absent" => 0
                    ),
                    3 => array(
                        "attendance_sheet_entries.late" => 0
                    ),
                    4 => array(
                        "attendance_sheet_entries.holiday" => 0
                    )
                )
        );

        $this->datatables->set_total_rows(
                $this->students_model
                        ->get_student_attendance_by_class_total($id)
        );
        $entries = $this->students_model->get_student_attendance_by_class($id, $this->datatables);



        foreach ($entries->result() as $r) {

            $total = $r->present + $r->absent + $r->late + $r->holiday;

            $present_p = @intval(($r->present / $total) * 100);
            $absent_p = @intval(($r->absent / $total) * 100);
            $late_p = @intval(($r->late / $total) * 100);
            $holiday_p = @intval(($r->holiday / $total) * 100);

            $this->datatables->data[] = array(
                $r->name,
                $present_p . "%",
                $absent_p . "%",
                $late_p . "%",
                $holiday_p . "%"
            );
        }

        echo json_encode($this->datatables->process());
    }

    public function view_behaviour($id) {
        $id = intval($id);
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

        $this->template->loadData("activeLink", array("students" => array("general" => 1)));

        $this->template->loadContent("students/view_behaviour.php", array(
            "student" => $student,
                )
        );
    }

    public function view_behaviour_page($id) {
        $this->load->model("behaviour_model");
        $id = intval($id);
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

        $this->load->library("datatables");

        $this->datatables->set_default_order("behaviour_records.ID", "desc");

        // Set page ordering options that can be used
        $this->datatables->ordering(
                array(
                    0 => array(
                        "users.username" => 0
                    ),
                    1 => array(
                        "rules.name" => 0
                    ),
                    2 => array(
                        "behaviour_records.timestamp" => 0
                    ),
                    3 => array(
                        "users2.username" => 0
                    )
                )
        );

        $this->datatables->set_total_rows(
                $this->behaviour_model
                        ->get_records_total_user($id)
        );
        $records = $this->behaviour_model->get_records_user($id, $this->datatables);



        foreach ($records->result() as $r) {
            $options = "";
            if ($this->common->has_permissions(array("admin", "behaviour_manager"), $this->user)) {
                $options = ' <a href="' . site_url("behaviour/edit_record/" . $r->ID) . '" class="btn btn-warning btn-xs" data-toggle="tooltip" data-placement="bottom" title="' . lang("ctn_55") . '"><span class="glyphicon glyphicon-cog"></span></a> <a href="' . site_url("behaviour/delete_record/" . $r->ID . "/" . $this->security->get_csrf_hash()) . '" class="btn btn-danger btn-xs" onclick="return confirm(\'' . lang("ctn_317") . '\')" data-toggle="tooltip" data-placement="bottom" title="' . lang("ctn_57") . '"><span class="glyphicon glyphicon-trash"></span></a>';
            }

            $this->datatables->data[] = array(
                $this->common->get_user_display(array("username" => $r->username, "avatar" => $r->avatar, "online_timestamp" => $r->online_timestamp, "first_name" => $r->first_name, "last_name" => $r->last_name)),
                $r->name,
                date($this->settings->info->date_format, $r->timestamp),
                $this->common->get_user_display(array("username" => $r->username2, "avatar" => $r->avatar2, "online_timestamp" => $r->online_timestamp2, "first_name" => $r->first_name2, "last_name" => $r->last_name2)),
                $options
            );
        }

        echo json_encode($this->datatables->process());
    }

    public function your() {
        if (!$this->common->has_permissions(array("admin", "student_group_manager",
                    "student_group_viewer"), $this->user)) {
            $this->template->error(lang("error_2"));
        }

        $this->template->loadData("activeLink", array("students" => array("your" => 1)));
        $this->template->loadContent("students/groups.php", array(
            "page" => "your"
                )
        );
    }

    public function groups() {
        if (!$this->common->has_permissions(array("admin", "student_group_manager",
                    "student_group_viewer"), $this->user)) {
            $this->template->error(lang("error_2"));
        }

        $this->template->loadData("activeLink", array("students" => array("groups" => 1)));
        $this->template->loadContent("students/groups.php", array(
            "page" => "index"
                )
        );
    }

    public function group_page($page) {
        if (!$this->common->has_permissions(array("admin", "student_group_manager",
                    "student_group_viewer"), $this->user)) {
            $this->template->error(lang("error_2"));
        }
        $this->load->library("datatables");

        $this->datatables->set_default_order("student_groups.ID", "desc");

        // Set page ordering options that can be used
        $this->datatables->ordering(
                array(
                    0 => array(
                        "student_groups.name" => 0
                    )
                )
        );

        if ($page == "index") {

            $this->datatables->set_total_rows(
                    $this->students_model
                            ->get_total_groups()
            );
            $groups = $this->students_model->get_groups($this->datatables);
        } elseif ($page == "your") {
            $this->datatables->set_total_rows(
                    $this->students_model
                            ->get_total_groups_your($this->user->info->ID)
            );
            $groups = $this->students_model->get_groups_your($this->user->info->ID, $this->datatables);
        }

        foreach ($groups->result() as $r) {

            $options = '<a href="' . site_url("students/view_group/" . $r->ID) . '" class="btn btn-primary btn-xs">' . lang("ctn_552") . '</a>';
            if ($this->common->has_permissions(array("admin", "student_group_manager"), $this->user) || ($this->settings->info->teacher_group_manage && $this->user->info->ID == $r->teacherid)) {
                $options .= ' <a href="' . site_url("students/edit_student_group/" . $r->ID) . '" class="btn btn-warning btn-xs" data-toggle="tooltip" data-placement="bottom" title="' . lang("ctn_55") . '"><span class="glyphicon glyphicon-cog"></span></a> <a href="' . site_url("students/delete_student_group/" . $r->ID . "/" . $this->security->get_csrf_hash()) . '" class="btn btn-danger btn-xs" onclick="return confirm(\'' . lang("ctn_317") . '\')" data-toggle="tooltip" data-placement="bottom" title="' . lang("ctn_57") . '"><span class="glyphicon glyphicon-trash"></span></a>';
            }

            $this->datatables->data[] = array(
                $r->name,
                $this->common->get_user_display(array("username" => $r->username, "avatar" => $r->avatar, "online_timestamp" => $r->online_timestamp, "first_name" => $r->first_name, "last_name" => $r->last_name)),
                $options
            );
        }
        echo json_encode($this->datatables->process());
    }

    public function view_group($id) {
        if (!$this->common->has_permissions(array("admin", "student_group_manager",
                    "student_group_viewer"), $this->user)) {
            $this->template->error(lang("error_2"));
        }
        $id = intval($id);
        $group = $this->students_model->get_student_group($id);
        if ($group->num_rows() == 0) {
            $this->template->error(lang("error_154"));
        }

        $group = $group->row();

        // Check user can view if they are not an admin/manager
        if (!$this->settings->info->student_view_groups &&
                !$this->common->has_permissions(array("admin", "student_group_manager",
                        ), $this->user)) {
            if ($group->teacherid != $this->user->info->ID) {
                $member = $this->students_model->get_group_user_check($this->user->info->ID, $id);
                if ($member->num_rows() == 0) {
                    $this->template->error(lang("error_155"));
                }
            }
        }

        $this->template->loadData("activeLink", array("students" => array("groups" => 1)));

        $announcements = $this->students_model->get_announcements($id);

        $this->template->loadContent("students/view_group.php", array(
            "group" => $group,
            "announcements" => $announcements
                )
        );
    }

    public function view_announcements($id) {
        if (!$this->common->has_permissions(array("admin", "student_group_manager",
                    "student_group_viewer"), $this->user)) {
            $this->template->error(lang("error_2"));
        }
        $id = intval($id);
        $group = $this->students_model->get_student_group($id);
        if ($group->num_rows() == 0) {
            $this->template->error(lang("error_154"));
        }

        $group = $group->row();

        // Check user can view if they are not an admin/manager
        if (!$this->settings->info->student_view_groups &&
                !$this->common->has_permissions(array("admin", "student_group_manager",
                        ), $this->user)) {
            if ($group->teacherid != $this->user->info->ID) {
                $member = $this->students_model->get_group_user_check($this->user->info->ID, $id);
                if ($member->num_rows() == 0) {
                    $this->template->error(lang("error_155"));
                }
            }
        }

        $this->template->loadData("activeLink", array("students" => array("groups" => 1)));

        $this->template->loadContent("students/view_announcements.php", array(
            "group" => $group
                )
        );
    }

    public function announcement_page($id) {
        if (!$this->common->has_permissions(array("admin", "student_group_manager",
                    "student_group_viewer"), $this->user)) {
            $this->template->error(lang("error_2"));
        }
        $id = intval($id);
        $group = $this->students_model->get_student_group($id);
        if ($group->num_rows() == 0) {
            $this->template->error(lang("error_154"));
        }

        $group = $group->row();

        // Check user can view if they are not an admin/manager
        if (!$this->settings->info->student_view_groups &&
                !$this->common->has_permissions(array("admin", "student_group_manager",
                        ), $this->user)) {
            if ($group->teacherid != $this->user->info->ID) {
                $member = $this->students_model->get_group_user_check($this->user->info->ID, $id);
                if ($member->num_rows() == 0) {
                    $this->template->error(lang("error_155"));
                }
            }
        }

        $this->load->library("datatables");

        $this->datatables->set_default_order("student_group_announcements.timestamp", "desc");

        // Set page ordering options that can be used
        $this->datatables->ordering(
                array(
                    0 => array(
                        "student_group_announcements.title" => 0
                    ),
                    1 => array(
                        "users.username" => 0
                    ),
                    2 => array(
                        "student_group_announcements.timestamp" => 0
                    )
                )
        );

        $this->datatables->set_total_rows(
                $this->students_model
                        ->get_total_announcements($id)
        );
        $news = $this->students_model->get_announcements_dt($id, $this->datatables);

        foreach ($news->result() as $r) {

            $options = '<a href="' . site_url("students/view_announcement/" . $r->ID) . '" class="btn btn-primary btn-xs">' . lang("ctn_552") . '</a> ';
            if ($this->common->has_permissions(array("admin", "student_group_manager"), $this->user) || ($this->settings->info->teacher_group_manage && $this->user->info->ID == $group->teacherid)) {
                $options .= '<a href="' . site_url("students/edit_announcement/" . $r->ID) . '" class="btn btn-warning btn-xs" data-toggle="tooltip" data-placement="bottom" title="' . lang("ctn_55") . '"><span class="glyphicon glyphicon-cog"></span></a> <a href="' . site_url("students/delete_announcement/" . $r->ID . "/" . $this->security->get_csrf_hash()) . '" class="btn btn-danger btn-xs" onclick="return confirm(\'' . lang("ctn_317") . '\')" data-toggle="tooltip" data-placement="bottom" title="' . lang("ctn_57") . '"><span class="glyphicon glyphicon-trash"></span></a>';
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
        if (!$this->common->has_permissions(array("admin", "student_group_manager"), $this->user)) {
            $this->template->error(lang("error_2"));
        }
        $id = intval($id);
        $announcement = $this->students_model->get_announcement($id);
        if ($announcement->num_rows() == 0) {
            $this->template->error(lang("error_86"));
        }

        $announcement = $announcement->row();

        $group = $this->students_model->get_student_group($announcement->groupid);
        if ($group->num_rows() == 0) {
            $this->template->error(lang("error_154"));
        }
        $group = $group->row();

        if ($this->settings->info->teacher_group_manage && $this->user->info->ID == $group->teacherid) {
            
        } else {
            if (!$this->common->has_permissions(array("admin", "student_group_manager"), $this->user)) {
                $this->template->error(lang("error_2"));
            }
        }

        $this->template->loadContent("students/edit_announcement.php", array(
            "announcement" => $announcement
                )
        );
    }

    public function edit_announcement_pro($id) {
        $id = intval($id);
        $announcement = $this->students_model->get_announcement($id);
        if ($announcement->num_rows() == 0) {
            $this->template->error(lang("error_86"));
        }

        $announcement = $announcement->row();

        $group = $this->students_model->get_student_group($announcement->groupid);
        if ($group->num_rows() == 0) {
            $this->template->error(lang("error_154"));
        }
        $group = $group->row();

        if ($this->settings->info->teacher_group_manage && $this->user->info->ID == $group->teacherid) {
            
        } else {
            if (!$this->common->has_permissions(array("admin", "student_group_manager"), $this->user)) {
                $this->template->error(lang("error_2"));
            }
        }

        $title = $this->common->nohtml($this->input->post("title"));
        $body = $this->lib_filter->go($this->input->post("announcement"));

        if (empty($title)) {
            $this->template->error(lang("error_85"));
        }

        $this->students_model->update_announcement($id, array(
            "title" => $title,
            "body" => $body,
                )
        );

        // Send notification to all students

        $this->session->set_flashdata("globalmsg", lang("success_50"));
        redirect(site_url("students/view_group/" . $announcement->groupid));
    }

    public function delete_announcement($id, $hash) {
        if ($hash != $this->security->get_csrf_hash()) {
            $this->template->error(lang("error_6"));
        }
        $id = intval($id);
        $announcement = $this->students_model->get_announcement($id);
        if ($announcement->num_rows() == 0) {
            $this->template->error(lang("error_86"));
        }

        $announcement = $announcement->row();

        $group = $this->students_model->get_student_group($announcement->groupid);
        if ($group->num_rows() == 0) {
            $this->template->error(lang("error_154"));
        }
        $group = $group->row();

        if ($this->settings->info->teacher_group_manage && $this->user->info->ID == $group->teacherid) {
            
        } else {
            if (!$this->common->has_permissions(array("admin", "student_group_manager"), $this->user)) {
                $this->template->error(lang("error_2"));
            }
        }

        $this->students_model->delete_announcement($id);
        $this->session->set_flashdata("globalmsg", lang("success_51"));
        redirect(site_url("students/view_group/" . $announcement->groupid));
    }

    public function view_announcement($id) {
        if (!$this->common->has_permissions(array("admin", "student_group_manager",
                    "student_group_viewer"), $this->user)) {
            $this->template->error(lang("error_2"));
        }
        $this->template->loadData("activeLink", array("students" => array("groups" => 1)));
        $id = intval($id);
        $announcement = $this->students_model->get_announcement($id);
        if ($announcement->num_rows() == 0) {
            $this->template->error(lang("error_86"));
        }

        $announcement = $announcement->row();

        // Check user can view if they are not an admin/manager
        if (!$this->settings->info->student_view_groups &&
                !$this->common->has_permissions(array("admin", "student_group_manager",
                        ), $this->user)) {
            if ($group->teacherid != $this->user->info->ID) {
                $member = $this->students_model->get_group_user_check($this->user->info->ID, $id);
                if ($member->num_rows() == 0) {
                    $this->template->error(lang("error_155"));
                }
            }
        }


        // Check if user has viewed
        $c = $this->students_model->get_user_announcement($this->user->info->ID, $id);
        if ($c->num_rows() == 0) {
            $this->students_model->add_user_announcement(array(
                "announcementid" => $id,
                "userid" => $this->user->info->ID,
                "timestamp" => time()
                    )
            );
        }

        $users = $this->students_model->get_user_announcements($id);

        $this->template->loadContent("students/view_announcement.php", array(
            "announcement" => $announcement,
            "users" => $users
                )
        );
    }

    public function add_student_group() {
        if (!$this->common->has_permissions(array("admin", "student_group_manager"), $this->user)) {
            $this->template->error(lang("error_2"));
        }
        $name = $this->common->nohtml($this->input->post("name"));
        $username = $this->common->nohtml($this->input->post("username"));
        $description = $this->lib_filter->go($this->input->post("description"));

        if (empty($name)) {
            $this->template->error(lang("error_156"));
        }

        $userid = 0;
        if (!empty($username)) {
            $user = $this->user_model->get_user_by_username($username);
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
        }

        $groupid = $this->students_model->add_student_group(array(
            "name" => $name,
            "teacherid" => $userid,
            "description" => $description
                )
        );

        if ($userid > 0) {
            // Add teacher as a member
            $this->students_model->add_student_to_group(array(
                "userid" => $userid,
                "groupid" => $groupid
                    )
            );
        }


        $this->session->set_flashdata("globalmsg", lang("success_116"));
        redirect(site_url("students/groups"));
    }

    public function edit_student_group($id) {
        $id = intval($id);
        $group = $this->students_model->get_student_group($id);
        if ($group->num_rows() == 0) {
            $this->template->error(lang("error_154"));
        }

        $group = $group->row();

        if ($this->settings->info->teacher_group_manage && $this->user->info->ID == $group->teacherid) {
            
        } else {
            if (!$this->common->has_permissions(array("admin", "student_group_manager"), $this->user)) {
                $this->template->error(lang("error_2"));
            }
        }

        $this->template->loadData("activeLink", array("students" => array("groups" => 1)));

        // Get teacher
        $username = "";
        if ($group->teacherid > 0) {
            $user = $this->user_model->get_user_by_id($group->teacherid);
            if ($user->num_rows() > 0) {
                $user = $user->row();
                $username = $user->username;
            }
        }
        $this->template->loadContent("students/edit_group.php", array(
            "group" => $group,
            "username" => $username
                )
        );
    }

    public function edit_student_group_pro($id) {
        $id = intval($id);
        $group = $this->students_model->get_student_group($id);
        if ($group->num_rows() == 0) {
            $this->template->error(lang("error_154"));
        }

        $group = $group->row();

        if ($this->settings->info->teacher_group_manage && $this->user->info->ID == $group->teacherid) {
            
        } else {
            if (!$this->common->has_permissions(array("admin", "student_group_manager"), $this->user)) {
                $this->template->error(lang("error_2"));
            }
        }

        $name = $this->common->nohtml($this->input->post("name"));
        $username = $this->common->nohtml($this->input->post("username"));
        $description = $this->lib_filter->go($this->input->post("description"));

        if (empty($name)) {
            $this->template->error(lang("error_156"));
        }

        $userid = 0;
        if (!empty($username)) {
            $user = $this->user_model->get_user_by_username($username);
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
        }

        $this->students_model->update_student_group($id, array(
            "name" => $name,
            "teacherid" => $userid,
            "description" => $description
                )
        );

        if ($userid != $group->teacherid) {
            // Add teacher as a member
            $this->students_model->add_student_to_group(array(
                "userid" => $userid,
                "groupid" => $id
                    )
            );
        }

        $this->session->set_flashdata("globalmsg", lang("success_117"));
        redirect(site_url("students/groups"));
    }

    public function delete_student_group($id, $hash) {
        if (!$this->common->has_permissions(array("admin", "student_group_manager"), $this->user)) {
            $this->template->error(lang("error_2"));
        }
        if ($hash != $this->security->get_csrf_hash()) {
            $this->template->error(lang("error_6"));
        }
        $id = intval($id);
        $group = $this->students_model->get_student_group($id);
        if ($group->num_rows() == 0) {
            $this->template->error(lang("error_154"));
        }

        $this->students_model->delete_student_group($id);
        $this->session->set_flashdata("globalmsg", lang("success_118"));
        redirect(site_url("students/groups"));
    }

    public function student_group_page($id) {
        if (!$this->common->has_permissions(array("admin", "student_group_manager",
                    "student_group_viewer"), $this->user)) {
            $this->template->error(lang("error_2"));
        }
        $id = intval($id);
        $group = $this->students_model->get_student_group($id);
        if ($group->num_rows() == 0) {
            $this->template->error(lang("error_154"));
        }
        $group = $group->row();

        // Check user can view if they are not an admin/manager
        if (!$this->settings->info->student_view_groups &&
                !$this->common->has_permissions(array("admin", "student_group_manager",
                        ), $this->user)) {
            if ($group->teacherid != $this->user->info->ID) {
                $member = $this->students_model->get_group_user_check($this->user->info->ID, $id);
                if ($member->num_rows() == 0) {
                    $this->template->error(lang("error_155"));
                }
            }
        }

        $this->load->library("datatables");

        $this->datatables->set_default_order("student_group_users.name", "desc");

        // Set page ordering options that can be used
        $this->datatables->ordering(
                array(
                    0 => array(
                        "users.username" => 0
                    )
                )
        );

        $this->datatables->set_total_rows(
                $this->students_model
                        ->get_total_users_in_group($id)
        );
        $users = $this->students_model->get_group_users($id, $this->datatables);

        foreach ($users->result() as $r) {

            $options = "";
            if ($this->common->has_permissions(array("admin", "student_group_manager"), $this->user) || ($this->settings->info->teacher_group_manage && $this->user->info->ID == $group->teacherid)) {
                $options = '<a href="' . site_url("students/delete_student_from_group/" . $r->ID . "/" . $this->security->get_csrf_hash()) . '" class="btn btn-danger btn-xs" onclick="return confirm(\'' . lang("ctn_317") . '\')" data-toggle="tooltip" data-placement="bottom" title="' . lang("ctn_57") . '"><span class="glyphicon glyphicon-trash"></span></a>';
            }



            $this->datatables->data[] = array(
                $this->common->get_user_display(array("username" => $r->username, "avatar" => $r->avatar, "online_timestamp" => $r->online_timestamp, "first_name" => $r->first_name, "last_name" => $r->last_name)),
                $options
            );
        }
        echo json_encode($this->datatables->process());
    }

    public function add_student_to_group($id) {
        $id = intval($id);
        $group = $this->students_model->get_student_group($id);
        if ($group->num_rows() == 0) {
            $this->template->error(lang("error_154"));
        }
        $group = $group->row();

        if ($this->settings->info->teacher_group_manage && $this->user->info->ID == $group->teacherid) {
            
        } else {
            if (!$this->common->has_permissions(array("admin", "student_group_manager"), $this->user)) {
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

        // Check user role
        $user_role = $this->user_model->get_users_user_role($user->user_role);
        if ($user_role->num_rows() == 0) {
            $this->template->error(lang("error_88"));
            $user_role = $user_role->row();
            if (!$user_role->student) {
                $this->template->error(lang("error_89"));
            }
        }

        // Check user is not already part of the group
        $group_user = $this->students_model->get_group_user_check($userid, $id);
        if ($group_user->num_rows() > 0) {
            $this->template->error(lang("error_157"));
        }

        // Add
        $this->students_model->add_student_to_group(array(
            "groupid" => $id,
            "userid" => $userid
                )
        );

        // Send notification
        $this->user_model->increment_field($userid, "noti_count", 1);
        $this->user_model->add_notification(array(
            "userid" => $userid,
            "url" => "students/view_group/" . $id,
            "timestamp" => time(),
            "message" => lang("ctn_837") . ": <strong>" . $group->name . "</strong>",
            "status" => 0,
            "fromid" => $this->user->info->ID,
            "email" => $user->email,
            "username" => $user->username,
            "email_notification" => $user->email_notification
                )
        );

        $this->session->set_flashdata("globalmsg", lang("success_119"));
        redirect(site_url("students/view_group/" . $id));
    }

    public function delete_student_from_group($id, $hash) {
        if ($hash != $this->security->get_csrf_hash()) {
            $this->template->error(lang("error_6"));
        }
        $id = intval($id);
        $student = $this->students_model->get_student_from_group($id);
        if ($student->num_rows() == 0) {
            $this->template->error(lang("error_71"));
        }
        $student = $student->row();

        $group = $this->students_model->get_student_group($student->groupid);
        if ($group->num_rows() == 0) {
            $this->template->error(lang("error_154"));
        }
        $group = $group->row();

        if ($this->settings->info->teacher_group_manage && $this->user->info->ID == $group->teacherid) {
            
        } else {
            if (!$this->common->has_permissions(array("admin", "student_group_manager"), $this->user)) {
                $this->template->error(lang("error_2"));
            }
        }

        $this->students_model->delete_user_from_group($id);
        $this->session->set_flashdata("globalmsg", lang("success_120"));
        redirect(site_url("students/view_group/" . $student->groupid));
    }

    public function get_students() {
        $query = $this->common->nohtml($this->input->get("query"));

        if (!empty($query)) {
            $usernames = $this->user_model->get_usernames_by_user_role($query, "student");
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

    public function add_group_announcement($id) {
        $id = intval($id);
        $group = $this->students_model->get_student_group($id);
        if ($group->num_rows() == 0) {
            $this->template->error(lang("error_154"));
        }
        $group = $group->row();

        if ($this->settings->info->teacher_group_manage && $this->user->info->ID == $group->teacherid) {
            
        } else {
            if (!$this->common->has_permissions(array("admin", "student_group_manager"), $this->user)) {
                $this->template->error(lang("error_2"));
            }
        }

        $title = $this->common->nohtml($this->input->post("title"));
        $body = $this->lib_filter->go($this->input->post("announcement"));

        if (empty($title)) {
            $this->template->error(lang("error_85"));
        }

        $this->students_model->add_announcement(array(
            "title" => $title,
            "body" => $body,
            "timestamp" => time(),
            "userid" => $this->user->info->ID,
            "groupid" => $id
                )
        );

        // Send notification to all students
        $students = $this->students_model->get_students_from_group($id);
        foreach ($students->result() as $r) {
            // Send notification
            $this->user_model->increment_field($r->userid, "noti_count", 1);
            $this->user_model->add_notification(array(
                "userid" => $r->userid,
                "url" => "students/view_group/" . $id,
                "timestamp" => time(),
                "message" => lang("ctn_838"),
                "status" => 0,
                "fromid" => $this->user->info->ID,
                "email" => $r->email,
                "username" => $r->username,
                "email_notification" => $r->email_notification
                    )
            );
        }

        $this->session->set_flashdata("globalmsg", lang("success_49"));
        redirect(site_url("students/view_group/" . $id));
    }

}

?>