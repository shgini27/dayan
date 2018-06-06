<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Reports extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model("user_model");
        $this->load->model("reports_model");
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

        $this->template->loadData("activeLink", array("reports" => array("general" => 1)));

        if (!$this->common->has_permissions(array("admin", "report_manager"), $this->user)) {
            $this->template->error(lang("error_2"));
        }

        if (!$this->settings->info->reports_section) {
            $this->template->error(lang("error_84"));
        }
    }

    public function index() {
        $this->template->loadData("activeLink", array("reports" => array("general" => 1)));

        $this->template->loadContent("reports/index.php", array(
                )
        );
    }

    public function finance() {
        $this->template->loadData("activeLink", array("reports" => array("finance" => 1)));

        $this->template->loadExternal(
                '<script type="text/javascript" src="'
                . base_url() . 'scripts/libraries/Chart.min.js" /></script>'
        );


        if (!isset($_POST['start_date'])) {
            $range1 = date($this->settings->info->date_picker_format, time() - (3600 * 24 * 7));
            $range2 = date($this->settings->info->date_picker_format);
        } else {
            $range1 = $this->common->nohtml($this->input->post("start_date"));
            $range2 = $this->common->nohtml($this->input->post("end_date"));
        }

        $dates = $this->common->getDatesFromRange($range1, $range2, $this->settings->info->date_picker_format, "Y-m-d");
        $results = array();
        $results2 = array();

        $total_revenue = 0;
        $total_expense = 0;

        foreach ($dates as $date) {
            // Revenue
            $count = $this->reports_model->get_finance_sum($date['db'], 1);
            $total_revenue += $count;
            $results[] = array(
                "date" => $date['display'],
                "count" => $count
            );
            // Exepenses
            $count = $this->reports_model->get_finance_sum($date['db'], 0);
            $total_expense += $count;
            $count *= -1;
            $results2[] = array(
                "date" => $date['display'],
                "count" => $count
            );
        }

        $this->template->loadContent("reports/finance.php", array(
            "results" => $results,
            "results2" => $results2,
            "dates" => $results,
            "total_revenue" => $total_revenue,
            "total_expense" => $total_expense,
            "range1" => $range1,
            "range2" => $range2
                )
        );
    }

    public function attendance() {
        $this->template->loadData("activeLink", array("reports" => array("attendance" => 1)));

        $this->template->loadExternal(
                '<script type="text/javascript" src="'
                . base_url() . 'scripts/libraries/Chart.min.js" /></script>'
        );


        if (!isset($_POST['start_date'])) {
            $range1 = date($this->settings->info->date_picker_format, time() - (3600 * 24 * 7));
            $range2 = date($this->settings->info->date_picker_format);
        } else {
            $range1 = $this->common->nohtml($this->input->post("start_date"));
            $range2 = $this->common->nohtml($this->input->post("end_date"));
        }

        $dates = $this->common->getDatesFromRange($range1, $range2, $this->settings->info->date_picker_format, "Y-m-d");
        $results = array();
        $results2 = array();



        foreach ($dates as $date) {
            $res = $this->reports_model->get_attendance($date['db']);
            if ($res->num_rows() > 0) {
                foreach ($res->result() as $res) {
                    $results[] = array(
                        "date" => $date['display'],
                        "present" => $res->present,
                        "absent" => $res->absent,
                        "late" => $res->late,
                        "holiday" => $res->holiday
                    );
                }
            } else {
                $results[] = array(
                    "date" => $date['display'],
                    "present" => 0,
                    "absent" => 0,
                    "late" => 0,
                    "holiday" => 0
                );
            }
        }

        $this->template->loadContent("reports/attendance.php", array(
            "results" => $results,
            "dates" => $results,
            "range1" => $range1,
            "range2" => $range2
                )
        );
    }

    //custom reports
    public function region() {
        
    }

    public function sex() {
        
    }

    public function statistics() {
        $this->template->loadExternal(
                '<script src="' . base_url() . 'scripts/libraries/devextreme/cldr/cldr.min.js" /></script>'
                . '<script src="' . base_url() . 'scripts/libraries/devextreme/cldr/event.min.js" /></script>'
                . '<script src="' . base_url() . 'scripts/libraries/devextreme/cldr/supplemental.min.js" /></script>'
                . '<script src="' . base_url() . 'scripts/libraries/devextreme/cldr/unresolved.min.js" /></script>'
                . '<script type="text/javascript" src="'
                . base_url() . 'scripts/libraries/devextreme/globalize/globalize.min.js" /></script>'
                . '<script type="text/javascript" src="'
                . base_url() . 'scripts/libraries/devextreme/globalize/message.min.js" /></script>'
                . '<script type="text/javascript" src="'
                . base_url() . 'scripts/libraries/devextreme/globalize/number.min.js" /></script>'
                . '<script type="text/javascript" src="'
                . base_url() . 'scripts/libraries/devextreme/globalize/currency.min.js" /></script>'
                . '<script type="text/javascript" src="'
                . base_url() . 'scripts/libraries/devextreme/globalize/date.min.js" /></script>'
                . '<link rel="stylesheet" type="text/css" href="'
                . base_url() . 'scripts/libraries/devextreme/css/dx.spa.css" />'
                . '<link rel="stylesheet" type="text/css" href="'
                . base_url() . 'scripts/libraries/devextreme/css/dx.common.css" />'
                . '<link rel="dx-theme" data-theme="generic.light" href="'
                . base_url() . 'scripts/libraries/devextreme/css/dx.light.css" />'
                . '<script src="' . base_url() . 'scripts/libraries/devextreme/js/dx.all.js" /></script>'
        );

        $results = array();
        $now_date = date("Y-m-d H:i:s");
        //$res = $this->reports_model->get_active_statistics($now_date);
        //log_message("debug", "result: " . json_encode($res->result()));
        
        $res = $this->reports_model->get_statistics();
        //log_message("debug", "result: " .$res->num_rows());
        if ($res->num_rows() > 0) {
            foreach ($res->result() as $res) {
                $fields = $this->get_fields($res->user_id);
                $results = array(
                    "id" => $res->ID,
                    "age" => $this->calculate_age($res->birth_date),
                    "year" => substr($res->year, 0, 4),
                    "category" => $res->category_name,
                    "class" => $res->class_name,
                    "city" => $res->city,
                    //"state" => $res->state,
                );
                foreach($fields as $field){
                    $results = array_merge($results, $field);
                }
            }
        }
        
        $this->template->loadContent("reports/statistics.php", ["results" => json_encode($results)]);
    }

    private function get_fields($id) {
        $custom_fields = $this->reports_model->get_custom_fields(intval($id));
        $data = array();
        if ($custom_fields->num_rows() > 0) {
            foreach ($custom_fields->result() as $custom_field) {
                $data[] = array(
                    $custom_field->field_name => $custom_field->field_value
                );
                
            }
            //log_message("debug", json_encode($data));
        }
        
        return $data;
    }
    
    private function calculate_age($date){
        $clean_date = trim(str_replace("/", ".", $date));
        return intval(date('Y', time() - strtotime($clean_date))) - 1970;
    }

}

?>