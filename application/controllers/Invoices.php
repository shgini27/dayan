<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Invoices extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model("user_model");
        $this->load->model("invoices_model");
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

        $this->template->loadData("activeLink", array("invoice" => array("general" => 1)));

        if (!$this->common->has_permissions(array("admin", "invoice_manager",
                    "invoice_viewer"), $this->user)) {
            $this->template->error(lang("error_2"));
        }

        if (!$this->settings->info->invoices_section) {
            $this->template->error(lang("error_84"));
        }
    }

    public function index() {
        if (!$this->common->has_permissions(array("admin", "invoice_manager"), $this->user)) {
            $this->template->error(lang("error_2"));
        }
        $this->template->loadData("activeLink", array("invoice" => array("general" => 1)));

        $this->template->loadContent("invoices/index.php", array(
            "page" => "index"
                )
        );
    }

    public function your() {
        $this->template->loadData("activeLink", array("invoice" => array("your" => 1)));

        $this->template->loadContent("invoices/index.php", array(
            "page" => "your"
                )
        );
    }

    public function templates() {
        if (!$this->common->has_permissions(array("admin", "invoice_manager"), $this->user)) {
            $this->template->error(lang("error_2"));
        }
        $this->template->loadData("activeLink", array("invoice" => array("templates" => 1)));

        $this->template->loadContent("invoices/index.php", array(
            "page" => "templates"
                )
        );
    }

    public function invoice_page($page = "index") {
        $this->load->library("datatables");

        $this->datatables->set_default_order("invoices.ID", "desc");

        // Set page ordering options that can be used
        $this->datatables->ordering(
                array(
                    0 => array(
                        "invoices.ID" => 0
                    ),
                    1 => array(
                        "invoices.invoice_id" => 0
                    ),
                    2 => array(
                        "invoices.title" => 0
                    ),
                    5 => array(
                        "invoices.status" => 0
                    ),
                    6 => array(
                        "invoices.due_date" => 0
                    ),
                    7 => array(
                        "invoices.total" => 0
                    )
                )
        );

        if ($page == "index") {
            if (!$this->common->has_permissions(array("admin", "invoice_manager"), $this->user)) {
                $this->template->error(lang("error_2"));
            }
            $this->datatables->set_total_rows(
                    $this->invoices_model->get_invoices_total(0, 0)
            );

            $invoices = $this->invoices_model->get_invoices(0, 0, $this->datatables);
        } elseif ($page == "templates") {
            if (!$this->common->has_permissions(array("admin", "invoice_manager"), $this->user)) {
                $this->template->error(lang("error_2"));
            }
            $invoices = $this->invoices_model->get_invoices(1, 0, $this->datatables);
            $this->datatables->set_total_rows(
                    $this->invoices_model->get_invoices_total(1, 0)
            );
        } elseif ($page == "your") {
            $invoices = $this->invoices_model->get_invoices(0, $this->user->info->ID, $this->datatables);
            $this->datatables->set_total_rows(
                    $this->invoices_model->get_invoices_total(0, $this->user->info->ID)
            );
        }


        foreach ($invoices->result() as $r) {
            if ($r->status == 1) {
                $status = "<label class='label label-danger'>" . lang("ctn_619") . "</label>";
            } elseif ($r->status == 2) {
                $status = "<label class='label label-success'>" . lang("ctn_620") . "</label>";
            } elseif ($r->status == 3) {
                $status = "<label class='label label-default'>" . lang("ctn_621") . "</label>";
            } elseif ($r->status == 4) {
                $status = "<label class='label label-warning'>" . lang("ctn_874") . "</label>";
            }

            $options = '';
            if ($page != "templates") {
                $options .= '<a href="' . site_url("invoices/view/" . $r->ID . "/" . $r->hash) . '" class="btn btn-primary btn-xs">' . lang("ctn_552") . '</a> <a href="' . site_url("invoices/get_pdf/" . $r->ID . "/" . $r->hash) . '" class="btn btn-info btn-xs" title="' . lang("ctn_828") . '" data-toggle="tooltip" data-placement="bottom"><span class="glyphicon glyphicon-save"></span></a> ';
            }
            if ($this->common->has_permissions(array("admin", "invoice_manager"), $this->user)) {
                $options .= '<a href="' . site_url("invoices/edit_invoice/" . $r->ID) . '" class="btn btn-warning btn-xs" title="' . lang("ctn_55") . '"  data-toggle="tooltip" data-placement="bottom"><span class="glyphicon glyphicon-cog"></span></a> <a href="' . site_url("invoices/delete_invoice/" . $r->ID . "/" . $this->security->get_csrf_hash()) . '" class="btn btn-danger btn-xs"  data-toggle="tooltip" data-placement="bottom" onclick="return confirm(\'' . lang("ctn_317") . '\')" title="' . lang("ctn_57") . '"><span class="glyphicon glyphicon-trash"></span></a>';
            }

            $client = $this->common->get_user_display(array("username" => $r->client_username, "avatar" => $r->client_avatar, "online_timestamp" => $r->client_online_timestamp, "first_name" => $r->client_first_name, "last_name" => $r->client_last_name));


            $this->datatables->data[] = array(
                $r->ID,
                $r->invoice_id,
                $r->title,
                $client,
                $status,
                date($this->settings->info->date_format, $r->due_date),
                $r->symbol . number_format($r->total, 2),
                $options
            );
        }
        echo json_encode($this->datatables->process());
    }

    public function add() {
        if (!$this->common->has_permissions(array("admin", "invoice_manager"), $this->user)) {
            $this->template->error(lang("error_2"));
        }

        $this->template->loadData("activeLink", array("invoice" => array("general" => 1)));

        $accounts = $this->invoices_model->get_all_paying_accounts();
        $currencies = $this->invoices_model->get_currencies();

        $last_invoice = $this->invoices_model
                ->get_last_invoice();
        if ($last_invoice->num_rows() == 0) {
            $invoice_tmp_id = "invoice_0001";
        } else {
            $inv = $last_invoice->row();
            $invoice_tmp_id = $inv->invoice_id;
            // Get last 4 digits
            if (preg_match('#(\d+)$#', $invoice_tmp_id, $matches)) {
                $num = intval($matches[1]);
                $pad = strlen($matches[1]);
                $num++;
                $num = str_pad($num, $pad, '0', STR_PAD_LEFT);
                $invoice_tmp_id = substr($invoice_tmp_id, 0, strlen($invoice_tmp_id) - $pad);
                $invoice_tmp_id = $invoice_tmp_id . $num;
            } else {
                $invoice_tmp_id = $invoice_tmp_id . "_0001";
            }
        }

        // Get invoice themes
        $themes = $this->invoices_model->get_invoice_themes();

        $this->template->loadExternal(
                '<link rel="stylesheet" href="' . base_url() . 'scripts/libraries/bootstrap-datepicker-1.6.4/css/bootstrap-datepicker3.min.css" />
			<script src="' . base_url() . 'scripts/libraries/bootstrap-datepicker-1.6.4/js/bootstrap-datepicker.min.js"></script>'
        );

        $this->template->loadContent("invoices/add.php", array(
            "accounts" => $accounts,
            "currencies" => $currencies,
            "invoice_id" => $invoice_tmp_id,
            "themes" => $themes
                )
        );
    }

    public function add_pro() {
        if (!$this->common->has_permissions(array("admin", "invoice_manager"), $this->user)) {
            $this->template->error(lang("error_2"));
        }
        $invoice_id = $this->common->nohtml($this->input->post("invoice_id"));
        $title = $this->common->nohtml($this->input->post("title"));
        $notes = $this->common->nohtml($this->input->post("notes"));
        $term_notes = $this->common->nohtml($this->input->post("term_notes"));
        $hidden_notes = $this->common->nohtml($this->input->post("hidden_notes"));

        $client_username = $this->common->nohtml($this->input->post("client_username"));
        $clientid = intval($this->input->post("clientid"));
        $guest_name = $this->common->nohtml($this->input->post("guest_name"));
        $guest_email = $this->common->nohtml($this->input->post("guest_email"));

        $projectid = intval($this->input->post("projectid"));
        $status = intval($this->input->post("status"));
        $currencyid = intval($this->input->post("currencyid"));
        $due_date = $this->common->nohtml($this->input->post("due_date"));

        $template = intval($this->input->post("template"));
        $paying_accountid = intval($this->input->post("paying_accountid"));
        $themeid = intval($this->input->post("themeid"));

        if ($paying_accountid == -1) {
            // Check valid Paying Account
            $account = $this->invoices_model->get_user_paying_account($this->user->info->ID);
            if ($account->num_rows() == 0) {
                $this->template->error(lang("error_182"));
            }
            $account = $account->row();
            $paying_accountid = $account->ID;
        } else {
            // Check valid Paying Account
            $account = $this->invoices_model->get_paying_account($paying_accountid);
            if ($account->num_rows() == 0) {
                $this->template->error(lang("error_182"));
            }
        }

        // Check no empty fields
        if (empty($invoice_id)) {
            $this->template->error(lang("error_125"));
        }
        if (empty($title)) {
            $this->template->error(lang("error_126"));
        }

        // Check for valid theme
        $theme = $this->invoices_model->get_invoice_theme($themeid);
        if ($theme->num_rows() == 0) {
            $this->template->error(lang("error_183"));
        }


        // Check the user has entered a valid client for the invoice.
        // 0 = no client selected
        // -1 = Guest Client
        // -2 = Enter username
        // > 0 = Selected user from dropdown list
        $userid = 0;
        if ($clientid == 0) {
            $this->template->error(lang("error_184"));
        } elseif ($clientid == -1) {
            // Check valid guest email (?)
            if (empty($guest_name)) {
                $this->template->error(lang("error_178"));
            }
            if (empty($guest_email)) {
                $this->template->error(lang("error_185"));
            }

            $this->load->helper('email');
            if (!valid_email($guest_email)) {
                $this->template->error(lang("error_186"));
            }
        } elseif ($clientid == -2) {
            $client = $this->user_model->get_user_by_username($client_username);
            if ($client->num_rows() == 0) {
                $this->template->error(lang("error_187"));
            }
            $client = $client->row();
            $userid = $client->ID;
        }

        // Valid Status check
        if ($status < 1 || $status > 4) {
            $this->template->error(lang("error_140"));
        }

        // Valid Currency check
        $currency = $this->invoices_model->get_currency($currencyid);
        if ($currency->num_rows() == 0) {
            $this->template->error(lang("error_82"));
        }

        // Make sure Due Date is of right format, otherwise set to 0.
        if (!empty($due_date)) {
            $dd = DateTime::createFromFormat($this->settings->info->date_picker_format, $due_date);
            $dd_timestamp = $dd->getTimestamp();
        } else {
            $dd_timestamp = 0;
        }


        // Loop through all the Invoice Items.
        // Make sure quantity and cost are > 0 and each item has a name
        // If item marked for saving, add to ItemDB for Invoice.
        $items = intval($this->input->post("items_count"));


        $sub_total = 0;
        $count = 0;
        $invoice_items = array();
        for ($i = 1; $i <= $items; $i++) {
            $quantity = floatval($this->input->post("item_quantity_" . $i));
            if ($quantity < 0)
                $this->template->error(lang("error_128"));
            $amount = floatval($this->input->post("item_price_" . $i));
            if ($amount < 0)
                $this->template->error(lang("error_188"));
            $name = $this->common->nohtml($this->input->post("item_name_" . $i));
            $desc = $this->common->nohtml($this->input->post("item_desc_" . $i));
            if (empty($name) && ($amount > 0 || $quantity > 0)) {
                $this->template->error(lang("error_189"));
            }
            $save = intval($this->input->post("save_" . $i));
            if (!empty($name)) {
                $count++;
                $invoice_items[] = array(
                    "name" => $name,
                    "desc" => $desc,
                    "amount" => $amount,
                    "quantity" => $quantity,
                    "save" => $save
                );
                $sub_total += $amount * $quantity;
            }
        }
        $total = $sub_total;

        if ($count == 0) {
            $this->template->error(lang("error_190"));
        }

        // Manage Invoice Tax
        // Add extra tax to total invoice bill based on user input %.
        $tax_name_1 = $this->common->nohtml($this->input->post("tax_name_1"));
        $tax_rate_1 = floatval($this->input->post("tax_rate_1"));
        $tax_name_2 = $this->common->nohtml($this->input->post("tax_name_2"));
        $tax_rate_2 = floatval($this->input->post("tax_rate_2"));

        if ($tax_rate_1 > 0) {
            $extra = floatval($sub_total / 100 * $tax_rate_1);
            $total = $total + $extra;
        }
        if ($tax_rate_2 > 0) {
            $extra = floatval($sub_total / 100 * $tax_rate_2);
            $total = $total + $extra;
        }

        // Invoice Hash for identifying the Invoice in URLs
        // Helps keep invoice's private
        $hash = sha1(rand(1, 100000) . $title);

        // If the status of the invocie is set to paid, set the paid date to
        // today
        $time_date_paid = "";
        if ($status == 2) {
            $time_date_paid = date("Y-m-d");
        }

        // Add invoice to the system
        $invoiceid = $this->invoices_model->add_invoice(array(
            "invoice_id" => $invoice_id,
            "title" => $title,
            "notes" => $notes,
            "term_notes" => $term_notes,
            "hidden_notes" => $hidden_notes,
            "themeid" => $themeid,
            "userid" => $this->user->info->ID,
            "status" => $status,
            "clientid" => $userid,
            "currencyid" => $currencyid,
            "timestamp" => time(),
            "due_date" => $dd_timestamp,
            "tax_name_1" => $tax_name_1,
            "tax_rate_1" => $tax_rate_1,
            "tax_name_2" => $tax_name_2,
            "tax_rate_2" => $tax_rate_2,
            "total" => $total,
            "hash" => $hash,
            "template" => $template,
            "time_date" => date("Y-m-d"),
            "time_date_paid" => $time_date_paid,
            "guest_name" => $guest_name,
            "guest_email" => $guest_email,
            "paying_accountid" => $paying_accountid
                )
        );

        // Add items to invoice, save any that have been selected to be saved
        foreach ($invoice_items as $item) {
            $this->invoices_model->add_invoice_item(array(
                "invoiceid" => $invoiceid,
                "name" => $item['name'],
                "quantity" => $item['quantity'],
                "amount" => $item['amount'],
                "description" => $item['desc']
                    )
            );

            if ($item['save']) {
                $this->invoices_model->add_invoice_item_db(array(
                    "name" => $item['name'],
                    "description" => $item['desc'],
                    "price" => $item['amount'],
                    "quantity" => $item['quantity'],
                        )
                );
            }
        }

        if ($userid > 0) {
            // Send notification
            $this->user_model->increment_field($client->ID, "noti_count", 1);
            $this->user_model->add_notification(array(
                "userid" => $client->ID,
                "url" => "invoices/view/" . $invoiceid . "/" . $hash,
                "timestamp" => time(),
                "message" => lang("ctn_824"),
                "status" => 0,
                "fromid" => $this->user->info->ID,
                "email" => $client->email,
                "username" => $client->username,
                "email_notification" => $client->email_notification
                    )
            );
        }

        $this->session->set_flashdata("globalmsg", lang("success_91"));
        redirect(site_url("invoices"));
    }

    public function add_check() {
        $formData = $this->input->post("formData");
        parse_str($formData, $data);

        // Invocie data
        $invoice_id = $this->common->nohtml($data["invoice_id"]);
        $title = $this->common->nohtml($data["title"]);
        $notes = $this->lib_filter->go($data["notes"]);
        $client_username = $this->common->nohtml($data["client_username"]);
        $guest_name = $this->common->nohtml($data["guest_name"]);
        $guest_email = $this->common->nohtml($data["guest_email"]);
        $clientid = intval($data['clientid']);
        $status = intval($data["status"]);
        $currencyid = intval($data["currencyid"]);
        $due_date = $this->common->nohtml($data["due_date"]);

        $template = 0;
        if (isset($data['template'])) {
            $template = intval($data["template"]);
        }
        $paying_accountid = intval($data["paying_accountid"]);

        $field_errors = array();

        if ($paying_accountid == -1) {
            // Check valid Paying Account
            $account = $this->invoices_model->get_user_paying_account($this->user->info->ID);
            if ($account->num_rows() == 0) {
                $field_errors['paying_accountid'] = lang("error_182");
            } else {
                $account = $account->row();
                $paying_accountid = $account->ID;
            }
        } else {
            // Check valid Paying Account
            $account = $this->invoices_model->get_paying_account($paying_accountid);
            if ($account->num_rows() == 0) {
                $field_errors['paying_accountid'] = lang("error_182");
            }
        }

        if (empty($invoice_id)) {
            $field_errors['invoice_id'] = lang("error_125");
        }
        if (empty($title)) {
            $field_errors['title'] = lang("error_126");
        }


        if ($clientid == -1) {
            // Looking for guest client
            if (empty($guest_name)) {
                $field_errors['guest_name'] = lang("error_178");
            }
            if (empty($guest_email)) {
                $field_errors['guest_email'] = lang("error_185");
            }
            // Check valid guest email (?)
            $this->load->helper('email');
            if (!valid_email($guest_email)) {
                $field_errors['guest_email'] = lang("error_186");
            }
        } elseif ($clientid == -2) {
            $client = $this->user_model->get_user_by_username($client_username);
            if ($client->num_rows() == 0) {
                $field_errors['client_username'] = lang("error_52");
            }
        } else {
            $field_errors['clientid'] = lang("error_142");
        }

        if ($status < 1 || $status > 4) {
            $field_errors['status'] = lang("error_140");
        }

        $currency = $this->invoices_model->get_currency($currencyid);
        if ($currency->num_rows() == 0) {
            $field_errors['currencyid'] = lang("error_82");
        }

        // Items 
        $items = intval($data["items_count"]);

        $sub_total = 0;
        $count = 0;
        for ($i = 1; $i <= $items; $i++) {
            if (isset($data['item_quantity_' . $i]) && isset($data['item_name_' . $i]) && isset($data['item_price_' . $i])) {
                $count++;
                $quantity = floatval($data["item_quantity_" . $i]);
                if ($quantity < 0) {
                    $field_errors['item_quantity_' . $i] = lang("error_191");
                }
                $amount = floatval($data["item_price_" . $i]);
                if ($amount < 0) {
                    $field_errors['item_price_' . $i] = lang("error_192");
                }
                $name = $this->common->nohtml($data["item_name_" . $i]);
                if (empty($name)) {
                    $field_errors['item_name_' . $i] = lang("error_133");
                }
                $sub_total += $amount * $quantity;
            }
        }
        $total = $sub_total;

        if ($count == 0) {
            $field_errors['items_count'] = lang("error_190");
        }

        if (empty($field_errors)) {
            echo json_encode(array("success" => 1));
        } else {
            echo json_encode(array("field_errors" => 1, "fieldErrors" => $field_errors));
        }
        exit();
    }

    public function edit_invoice($id) {
        if (!$this->common->has_permissions(array("admin", "invoice_manager"), $this->user)) {
            $this->template->error(lang("error_2"));
        }
        $id = intval($id);
        $invoice = $this->invoices_model->get_invoice($id);
        if ($invoice->num_rows() == 0) {
            $this->template->error(lang("error_134"));
        }
        $invoice = $invoice->row();

        $items = $this->invoices_model->get_invoice_items($id);
        
        $this->template->loadExternal(
                '<link rel="stylesheet" href="' . base_url() . 'scripts/libraries/bootstrap-datepicker-1.6.4/css/bootstrap-datepicker3.min.css" />
		<script src="' . base_url() . 'scripts/libraries/bootstrap-datepicker-1.6.4/js/bootstrap-datepicker.min.js"></script>
                <script src="' . base_url() . 'scripts/custom/invoice.js"></script>'
        );

        $this->template->loadData("activeLink", array("invoice" => array("general" => 1)));

        $accounts = $this->invoices_model->get_all_paying_accounts();
        $currencies = $this->invoices_model->get_currencies();

        // Get invoice themes
        $themes = $this->invoices_model->get_invoice_themes();

        $payments = $this->invoices_model->get_invoice_payments($invoice->ID);

        $payments_total = $this->invoices_model->get_invoice_payments_total($invoice->ID);

        $this->template->loadContent("invoices/edit.php", array(
            "accounts" => $accounts,
            "currencies" => $currencies,
            "invoice" => $invoice,
            "items" => $items,
            "themes" => $themes,
            "payments" => $payments,
            "payments_total" => $payments_total
                )
        );
    }

    public function edit_invoice_pro($id) {
        if (!$this->common->has_permissions(array("admin", "invoice_manager"), $this->user)) {
            $this->template->error(lang("error_2"));
        }
        $id = intval($id);
        $invoice = $this->invoices_model->get_invoice($id);
        if ($invoice->num_rows() == 0) {
            $this->template->error(lang("error_134"));
        }
        $invoice = $invoice->row();

        $invoice_id = $this->common->nohtml($this->input->post("invoice_id"));
        $title = $this->common->nohtml($this->input->post("title"));
        $notes = $this->common->nohtml($this->input->post("notes"));
        $term_notes = $this->common->nohtml($this->input->post("term_notes"));
        $hidden_notes = $this->common->nohtml($this->input->post("hidden_notes"));

        $client_username = $this->common->nohtml($this->input->post("client_username"));
        $clientid = intval($this->input->post("clientid"));
        $guest_name = $this->common->nohtml($this->input->post("guest_name"));
        $guest_email = $this->common->nohtml($this->input->post("guest_email"));

        $projectid = intval($this->input->post("projectid"));
        $status = intval($this->input->post("status"));
        $currencyid = intval($this->input->post("currencyid"));
        $due_date = $this->common->nohtml($this->input->post("due_date"));
        $remind = $this->common->nohtml($this->input->post("remind"));

        $template = intval($this->input->post("template"));
        $paying_accountid = intval($this->input->post("paying_accountid"));
        $themeid = intval($this->input->post("themeid"));

        if ($paying_accountid == -1) {
            // Check valid Paying Account
            $account = $this->invoices_model->get_user_paying_account($this->user->info->ID);
            if ($account->num_rows() == 0) {
                $this->template->error(lang("error_182"));
            }
            $account = $account->row();
            $paying_accountid = $account->ID;
        } else {
            // Check valid Paying Account
            $account = $this->invoices_model->get_paying_account($paying_accountid);
            if ($account->num_rows() == 0) {
                $this->template->error(lang("error_182"));
            }
        }

        // Check no empty fields
        if (empty($invoice_id)) {
            $this->template->error(lang("error_125"));
        }
        if (empty($title)) {
            $this->template->error(lang("error_126"));
        }

        // Check for valid theme
        $theme = $this->invoices_model->get_invoice_theme($themeid);
        if ($theme->num_rows() == 0) {
            $this->template->error(lang("error_183"));
        }


        // Check the user has entered a valid client for the invoice.
        // 0 = no client selected
        // -1 = Guest Client
        // -2 = Enter username
        // > 0 = Selected user from dropdown list
        $userid = 0;
        if ($clientid == 0) {
            $this->template->error(lang("error_184"));
        } elseif ($clientid == -1) {
            // Check valid guest email (?)
            if (empty($guest_name)) {
                $this->template->error(lang("error_178"));
            }
            if (empty($guest_email)) {
                $this->template->error(lang("error_185"));
            }

            $this->load->helper('email');
            if (!valid_email($guest_email)) {
                $this->template->error(lang("error_186"));
            }
        } elseif ($clientid == -2) {
            $client = $this->user_model->get_user_by_username($client_username);
            if ($client->num_rows() == 0) {
                $this->template->error(lang("error_187"));
            }
            $client = $client->row();
            $userid = $client->ID;
        }

        // Valid Status check
        if ($status < 1 || $status > 4) {
            $this->template->error(lang("error_140"));
        }

        // Valid Currency check
        $currency = $this->invoices_model->get_currency($currencyid);
        if ($currency->num_rows() == 0) {
            $this->template->error(lang("error_82"));
        }

        // Make sure Due Date is of right format, otherwise set to 0.
        if (!empty($due_date)) {
            $dd = DateTime::createFromFormat($this->settings->info->date_picker_format, $due_date);
            $dd_timestamp = $dd->getTimestamp();
        } else {
            $dd_timestamp = 0;
        }


        // Loop through all the Invoice Items.
        // Make sure quantity and cost are > 0 and each item has a name
        // If item marked for saving, add to ItemDB for Invoice.
        $items = intval($this->input->post("items_count"));


        $sub_total = 0;
        $count = 0;
        $invoice_items = array();
        for ($i = 1; $i <= $items; $i++) {
            $quantity = floatval($this->input->post("item_quantity_" . $i));
            if ($quantity < 0)
                $this->template->error(lang("error_128"));
            $amount = floatval($this->input->post("item_price_" . $i));
            if ($amount < 0)
                $this->template->error(lang("error_188"));
            $name = $this->common->nohtml($this->input->post("item_name_" . $i));
            $desc = $this->common->nohtml($this->input->post("item_desc_" . $i));
            if (empty($name) && ($amount > 0 || $quantity > 0)) {
                $this->template->error(lang("error_189"));
            }
            $save = intval($this->input->post("save_" . $i));
            if (!empty($name)) {
                $count++;
                $invoice_items[] = array(
                    "name" => $name,
                    "desc" => $desc,
                    "amount" => $amount,
                    "quantity" => $quantity,
                    "save" => $save
                );
                $sub_total += $amount * $quantity;
            }
        }
        $total = $sub_total;

        if ($count == 0) {
            $this->template->error(lang("error_190"));
        }

        // Manage Invoice Tax
        // Add extra tax to total invoice bill based on user input %.
        $tax_name_1 = $this->common->nohtml($this->input->post("tax_name_1"));
        $tax_rate_1 = floatval($this->input->post("tax_rate_1"));
        $tax_name_2 = $this->common->nohtml($this->input->post("tax_name_2"));
        $tax_rate_2 = floatval($this->input->post("tax_rate_2"));

        if ($tax_rate_1 > 0) {
            $extra = floatval($sub_total / 100 * $tax_rate_1);
            $total = $total + $extra;
        }
        if ($tax_rate_2 > 0) {
            $extra = floatval($sub_total / 100 * $tax_rate_2);
            $total = $total + $extra;
        }

        // If the status of the invocie is set to paid, set the paid date to
        // today
        $time_date_paid = $invoice->time_date_paid;
        if ($status == 2 && $invoice->status != 2) {
            $time_date_paid = date("Y-m-d");
        }

        // update invoice to the system
        $invoiceid = $id;
        $this->invoices_model->update_invoice($id, array(
            "invoice_id" => $invoice_id,
            "title" => $title,
            "notes" => $notes,
            "term_notes" => $term_notes,
            "hidden_notes" => $hidden_notes,
            "themeid" => $themeid,
            "userid" => $this->user->info->ID,
            "status" => $status,
            "clientid" => $userid,
            "currencyid" => $currencyid,
            "due_date" => $dd_timestamp,
            "tax_name_1" => $tax_name_1,
            "tax_rate_1" => $tax_rate_1,
            "tax_name_2" => $tax_name_2,
            "tax_rate_2" => $tax_rate_2,
            "total" => $total,
            "time_date_paid" => $time_date_paid,
            "guest_name" => $guest_name,
            "guest_email" => $guest_email,
            "paying_accountid" => $paying_accountid
                )
        );

        // Delete old invoice items and readd them
        // Lazy method (lol) ;)
        $this->invoices_model->delete_invoice_items($id);

        // Add items to invoice, save any that have been selected to be saved
        foreach ($invoice_items as $item) {
            $this->invoices_model->add_invoice_item(array(
                "invoiceid" => $invoiceid,
                "name" => $item['name'],
                "quantity" => $item['quantity'],
                "amount" => $item['amount'],
                "description" => $item['desc']
                    )
            );

            if ($item['save']) {
                $this->invoices_model->add_invoice_item_db(array(
                    "name" => $item['name'],
                    "description" => $item['desc'],
                    "price" => $item['amount'],
                    "quantity" => $item['quantity'],
                        )
                );
            }
        }

        if ($remind == 1 && $userid > 0) {
            $this->load->model("home_model");
            // Send notification
            $this->user_model->increment_field($client->ID, "noti_count", 1);
            $this->user_model->add_notification(array(
                "userid" => $client->ID,
                "url" => "invoices/view/" . $invoiceid . "/" . $invoice->hash,
                "timestamp" => time(),
                "message" => lang("ctn_829"),
                "status" => 0,
                "fromid" => $this->user->info->ID,
                "email" => $client->email,
                "username" => $client->username,
                "email_notification" => $client->email_notification
                    )
            );

            if (!isset($_COOKIE['language'])) {
                // Get first language in list as default
                $lang = $this->config->item("language");
            } else {
                $lang = $this->common->nohtml($_COOKIE["language"]);
            }

            log_message("debug", $lang);
            // Send Email
            $email_template = $this->home_model->get_email_template_hook("invoice_reminder", $lang);
            if ($email_template->num_rows() == 0) {
                $this->template->error(lang("error_48"));
            }
            $email_template = $email_template->row();

            $email_template->message = $this->common->replace_keywords(array(
                "[NAME]" => $client->username,
                "[SITE_URL]" => site_url(),
                "[INVOICE_URL]" =>
                site_url("invoices/view/" . $invoice->ID . "/" . $invoice->hash),
                "[SITE_NAME]" => $this->settings->info->site_name
                    ), $email_template->message);

            $this->common->send_email($email_template->title, $email_template->message, $client->email);
        }


        $this->session->set_flashdata("globalmsg", lang("success_92"));
        redirect(site_url("invoices"));
    }

    public function delete_invoice($id, $hash) {
        if (!$this->common->has_permissions(array("admin", "invoice_manager"), $this->user)) {
            $this->template->error(lang("error_2"));
        }
        if ($hash != $this->security->get_csrf_hash()) {
            $this->template->error(lang("error_6"));
        }
        $id = intval($id);
        $invoice = $this->invoices_model->get_invoice($id);
        if ($invoice->num_rows() == 0) {
            $this->template->error(lang("error_134"));
        }

        $this->invoices_model->delete_invoice($id);
        $this->session->set_flashdata("globalmsg", lang("success_93"));
        redirect(site_url("invoices"));
    }

    public function reoccuring() {
        if (!$this->common->has_permissions(array("admin", "invoice_manager"), $this->user)) {
            $this->template->error(lang("error_2"));
        }
        $this->template->loadData("activeLink", array("invoice" => array("reoccuring" => 1)));

        $templates = $this->invoices_model->get_invoice_templates_all();

        $this->template->loadContent("invoices/reoccuring.php", array(
            "templates" => $templates
                )
        );
    }

    public function reoccuring_page() {
        if (!$this->common->has_permissions(array("admin", "invoice_manager"), $this->user)) {
            $this->template->error(lang("error_2"));
        }
        $this->load->library("datatables");

        $this->datatables->set_default_order("invoice_reoccur.ID", "desc");

        // Set page ordering options that can be used
        $this->datatables->ordering(
                array(
                    0 => array(
                        "invoices.title" => 0
                    ),
                    2 => array(
                        "invoice_reoccur.status" => 0
                    ),
                    3 => array(
                        "invoice_reoccur.amount_time" => 0,
                        "invoice_reoccur.amount" => "desc"
                    ),
                    4 => array(
                        "invoice_reoccur.last_occurence" => 0
                    ),
                    5 => array(
                        "invoice_reoccur.next_occurence" => 0
                    )
                )
        );

        $this->datatables->set_total_rows(
                $this->invoices_model->get_reoccuring_invoices_total()
        );

        $invoices = $this->invoices_model
                ->get_reoccuring_invoices($this->datatables);

        foreach ($invoices->result() as $r) {
            if ($r->status == 0) {
                $status = "<label class='label label-warning'>" . lang("ctn_682") . "</label>";
            } elseif ($r->status == 1) {
                $status = "<label class='label label-success'>" . lang("ctn_683") . "</label>";
            } elseif ($r->status == 2) {
                $status = "<label class='label label-info'>" . lang("ctn_684") . "</label>";
            } elseif ($r->status == 3) {
                $status = "<label class='label label-info'>" . lang("ctn_874") . "</label>";
            }

            if ($r->amount > 1) {
                if ($r->amount_time == 0) {
                    $amount_time = lang("ctn_294");
                } elseif ($r->amount_time == 1) {
                    $amount_time = lang("ctn_830");
                } elseif ($r->amount_time == 2) {
                    $amount_time = lang("ctn_831");
                } elseif ($r->amount_time == 3) {
                    $amount_time = lang("ctn_832");
                }
            } else {
                if ($r->amount_time == 0) {
                    $amount_time = lang("ctn_676");
                } elseif ($r->amount_time == 1) {
                    $amount_time = lang("ctn_677");
                } elseif ($r->amount_time == 2) {
                    $amount_time = lang("ctn_678");
                } elseif ($r->amount_time == 3) {
                    $amount_time = lang("ctn_679");
                }
            }

            if ($r->last_occurence > 0) {
                $last_occurence = date($this->settings->info->date_format, $r->last_occurence);
            } else {
                $last_occurence = lang("ctn_833");
            }
            if ($r->next_occurence > 0) {
                $next_occurence = date($this->settings->info->date_format, $r->next_occurence);
            } else {
                $next_occurence = lang("ctn_833");
            }

            if ($r->clientid > 0) {
                $client = $this->common->get_user_display(array("username" => $r->username, "avatar" => $r->avatar, "online_timestamp" => $r->online_timestamp,
                    "first_name" => $r->first_name, "last_name" => $r->last_name));
            } else {
                $client = lang("ctn_876");
            }


            $this->datatables->data[] = array(
                $r->title,
                $client,
                $status,
                $r->amount . " " . $amount_time,
                $last_occurence,
                $next_occurence,
                '<a href="' . site_url("invoices/edit_reoccur_invoice/" . $r->ID) . '" class="btn btn-warning btn-xs"  data-toggle="tooltip" data-placement="bottom" title="' . lang("ctn_55") . '"><span class="glyphicon glyphicon-cog"></span></a> <a href="' . site_url("invoices/delete_reoccur_invoice/" . $r->ID . "/" . $this->security->get_csrf_hash()) . '" class="btn btn-danger btn-xs" onclick="return confirm(\'' . lang("ctn_317") . '\')" title="' . lang("ctn_57") . '" data-toggle="tooltip" data-placement="bottom"><span class="glyphicon glyphicon-trash"></span></a>'
            );
        }
        echo json_encode($this->datatables->process());
    }

    public function add_reoccuring_invoice() {
        if (!$this->common->has_permissions(array("admin", "invoice_manager"), $this->user)) {
            $this->template->error(lang("error_2"));
        }
        $this->template->loadData("activeLink", array("invoice" => array("reoccuring" => 1)));
        $client_username = $this->common->nohtml($this->input->post("client_username"));
        $templateid = intval($this->input->post("templateid"));
        $amount = intval($this->input->post("amount"));
        $amount_time = intval($this->input->post("amount_time"));
        $start_date = $this->common->nohtml($this->input->post("start_date"));
        $end_date = $this->common->nohtml($this->input->post("end_date"));
        $status = intval($this->input->post("status"));

        $userid = 0;
        if (!empty($client_username)) {
            $user = $this->user_model->get_user_by_username($client_username);
            if ($user->num_rows() == 0) {
                $this->template->error(lang("error_87"));
            }
            $user = $user->row();
            $userid = $user->ID;
        }

        $template = $this->invoices_model->get_invoice($templateid);
        if ($template->num_rows() == 0) {
            $this->template->error(lang("error_135"));
        }
        $template = $template->row();
        if (!$template->template) {
            $this->template->error(lang("error_135"));
        }

        if ($userid == 0) {
            if ($template->clientid == 0 && (empty($template->guest_name) && empty($template->guest_email))) {
                $this->template->error(lang("error_136"));
            }
            if ($template->clientid != 0) {
                $userid = $template->clientid;
            } else {
                
            }
        }

        if ($amount == 0) {
            $this->template->error(lang("error_137"));
        }
        if ($amount_time < 0 || $amount_time > 3) {
            $this->template->error(lang("error_138"));
        }

        if (!empty($start_date)) {
            $sd = DateTime::createFromFormat($this->settings->info->date_picker_format, $start_date);
            $sd_timestamp = $sd->getTimestamp();
        } else {
            $this->template->error(lang("error_139"));
        }

        if (!empty($end_date)) {
            $ed = DateTime::createFromFormat($this->settings->info->date_picker_format, $end_date);
            $ed_timestamp = $ed->getTimestamp();
        } else {
            $ed_timestamp = 0;
        }

        if ($status < 0 || $status > 3) {
            $this->template->error(lang("error_140"));
        }

        $this->invoices_model->add_reoccuring_invoice(array(
            "clientid" => $userid,
            "templateid" => $templateid,
            "amount" => $amount,
            "amount_time" => $amount_time,
            "start_date" => $sd_timestamp,
            "end_date" => $ed_timestamp,
            "status" => $status,
            "userid" => $this->user->info->ID,
            "timestamp" => time(),
            "next_occurence" => $sd_timestamp
                )
        );

        $this->session->set_flashdata("globalmsg", lang("success_94"));
        redirect(site_url("invoices/reoccuring"));
    }

    public function edit_reoccur_invoice($id) {
        if (!$this->common->has_permissions(array("admin", "invoice_manager"), $this->user)) {
            $this->template->error(lang("error_2"));
        }
        $this->template->loadData("activeLink", array("invoice" => array("reoccuring" => 1)));

        $id = intval($id);
        $invoice = $this->invoices_model->get_reoccuring_invoice($id);
        if ($invoice->num_rows() == 0) {
            $this->template->error(lang("error_141"));
        }
        $invoice = $invoice->row();

        $templates = $this->invoices_model->get_invoice_templates_all();

        $this->template->loadContent("invoices/edit_reoccuring.php", array(
            "templates" => $templates,
            "invoice" => $invoice
                )
        );
    }

    public function edit_reoccur_invoice_pro($id) {
        if (!$this->common->has_permissions(array("admin", "invoice_manager"), $this->user)) {
            $this->template->error(lang("error_2"));
        }
        $this->template->loadData("activeLink", array("invoice" => array("reoccuring" => 1)));

        $id = intval($id);
        $invoice = $this->invoices_model->get_reoccuring_invoice($id);
        if ($invoice->num_rows() == 0) {
            $this->template->error(lang("error_141"));
        }
        $invoice = $invoice->row();

        $client_username = $this->common->nohtml($this->input->post("client_username"));
        $templateid = intval($this->input->post("templateid"));
        $amount = intval($this->input->post("amount"));
        $amount_time = intval($this->input->post("amount_time"));
        $start_date = $this->common->nohtml($this->input->post("start_date"));
        $end_date = $this->common->nohtml($this->input->post("end_date"));
        $status = intval($this->input->post("status"));

        $userid = 0;
        if (!empty($client_username)) {
            $user = $this->user_model->get_user_by_username($client_username);
            if ($user->num_rows() == 0) {
                $this->template->error(lang("error_87"));
            }
            $user = $user->row();
            $userid = $user->ID;
        }

        $template = $this->invoices_model->get_invoice($templateid);
        if ($template->num_rows() == 0) {
            $this->template->error(lang("error_141"));
        }
        $template = $template->row();
        if (!$template->template) {
            $this->template->error(lang("error_141"));
        }

        if ($userid == 0) {
            if ($template->clientid == 0 && (empty($template->guest_name) && empty($template->guest_email))) {
                $this->template->error(lang("error_136"));
            }
            if ($template->clientid != 0) {
                $userid = $template->clientid;
            } else {
                
            }
        }

        if ($amount == 0) {
            $this->template->error(lang("error_137"));
        }
        if ($amount_time < 0 || $amount_time > 3) {
            $this->template->error(lang("error_138"));
        }

        if (!empty($start_date)) {
            $sd = DateTime::createFromFormat($this->settings->info->date_picker_format, $start_date);
            $sd_timestamp = $sd->getTimestamp();
        } else {
            $this->template->error(lang("error_143"));
        }

        if (!empty($end_date)) {
            $ed = DateTime::createFromFormat($this->settings->info->date_picker_format, $end_date);
            $ed_timestamp = $ed->getTimestamp();
        } else {
            $ed_timestamp = 0;
        }

        if ($status < 0 || $status > 3) {
            $this->template->error(lang("error_140"));
        }

        // Calculate next occurence
        $current_date = DateTime::createFromFormat("m/d/Y h:i:s", date("m/d/Y", $invoice->last_occurence) . " 00:00:00");
        $amount = $amount;
        $amount_time = $amount_time;
        $day = 3600 * 24;
        $week = ((3600 * 24) * 7);
        $month = ((3600 * 24) * 30);
        $year = ((3600 * 24) * 365);
        if ($amount_time == 0) {
            // Days 
            $next = $current_date->getTimestamp() + ( $day * $amount );
        } elseif ($amount_time == 1) {
            // Weeks
            $next = $current_date->getTimestamp() + ( $week * $amount );
        } elseif ($amount_time == 2) {
            // Months
            $next = $current_date->getTimestamp() + ( $month * $amount);
        } elseif ($amount_time == 3) {
            // Year
            $next = $current_date->getTimestamp() + ( $year * $amount);
        }

        if ($ed_timestamp > 0) {
            // Check to make sure end date isn't exceeded
            $end_date = DateTime::createFromFormat("m/d/Y h:i:s", date("m/d/Y", $ed->getTimestamp()) . " 00:00:00");

            if ($end_date->getTimestamp() < $next) {
                $next = 0;
            }
        }

        $this->invoices_model->update_reoccuring_invoice($id, array(
            "clientid" => $userid,
            "templateid" => $templateid,
            "amount" => $amount,
            "amount_time" => $amount_time,
            "start_date" => $sd_timestamp,
            "end_date" => $ed_timestamp,
            "status" => $status,
            "next_occurence" => $next
                )
        );

        $this->session->set_flashdata("globalmsg", lang("success_95"));
        redirect(site_url("invoices/reoccuring"));
    }

    public function delete_reoccur_invoice($id, $hash) {
        if (!$this->common->has_permissions(array("admin", "invoice_manager"), $this->user)) {
            $this->template->error(lang("error_2"));
        }
        $this->template->loadData("activeLink", array("invoice" => array("reoccuring" => 1)));
        if ($hash != $this->security->get_csrf_hash()) {
            $this->template->error(lang("error_6"));
        }
        $id = intval($id);
        $invoice = $this->invoices_model->get_reoccuring_invoice($id);
        if ($invoice->num_rows() == 0) {
            $this->template->error(lang("error_141"));
        }

        // Delete
        $this->invoices_model->delete_reoccuring_invoice($id);
        $this->session->set_flashdata("globalmsg", lang("success_96"));
        redirect(site_url("invoices/reoccuring"));
    }

    public function paying_accounts() {
        if (!$this->common->has_permissions(array("admin", "invoice_manager"), $this->user)) {
            $this->template->error(lang("error_2"));
        }
        $this->template->loadData("activeLink", array("invoice" => array("pay" => 1)));

        $this->template->loadContent("invoices/paying_accounts.php", array(
                )
        );
    }

    public function paying_account_page() {
        if (!$this->common->has_permissions(array("admin", "invoice_manager"), $this->user)) {
            $this->template->error(lang("error_2"));
        }
        $this->load->library("datatables");

        $this->datatables->set_default_order("paying_accounts.ID", "desc");

        // Set page ordering options that can be used
        $this->datatables->ordering(
                array(
                    0 => array(
                        "paying_accounts.name" => 0
                    ),
                    1 => array(
                        "paying_accounts.paypal_email" => 0
                    ),
                )
        );

        $this->datatables->set_total_rows(
                $this->invoices_model
                        ->get_total_paying_accounts()
        );
        $accounts = $this->invoices_model->get_paying_accounts($this->datatables);


        foreach ($accounts->result() as $r) {

            $this->datatables->data[] = array(
                $r->name,
                $r->paypal_email,
                $r->address_line_1,
                $r->country,
                '<a href="' . site_url("invoices/edit_paying_account/" . $r->ID) . '" class="btn btn-warning btn-xs" data-toggle="tooltip" data-placement="bottom" title="' . lang("ctn_55") . '"><span class="glyphicon glyphicon-cog"></span></a> <a href="' . site_url("invoices/delete_paying_account/" . $r->ID . "/" . $this->security->get_csrf_hash()) . '" class="btn btn-danger btn-xs" onclick="return confirm(\'' . lang("ctn_317") . '\')" data-toggle="tooltip" data-placement="bottom" title="' . lang("ctn_57") . '"><span class="glyphicon glyphicon-trash"></span></a>'
            );
        }
        echo json_encode($this->datatables->process());
    }
    
    public function payed_students(){
        if (!$this->common->has_permissions(array("admin", "invoice_manager"), $this->user)) {
            $this->template->error(lang("error_2"));
        }
        $this->template->loadData("activeLink", array("invoice" => array("payed" => 1)));

        $this->template->loadContent("invoices/payed_students.php", array(
                )
        );
    }
    
    public function payed_students_page($status) {
        if (!$this->common->has_permissions(array("admin", "invoice_manager"), $this->user)) {
            $this->template->error(lang("error_2"));
        }
        $this->load->library("datatables");

        $this->datatables->set_default_order("users.ID", "desc");

        // Set page ordering options that can be used 
        $this->datatables->ordering(
                array(
                    0 => array(
                        "users.username" => 0
                    ),
                    1 => array(
                        "users.first_name" => 0
                    ),
                    2 => array(
                        "users.mobile_phone" => 0
                    )
                )
        );

        $this->datatables->set_total_rows(
                $this->invoices_model
                        ->get_total_payed_students(intval($status))
        );
        $payed_students = $this->invoices_model->get_payed_students($this->datatables, intval($status));

        foreach ($payed_students->result() as $r) {

            $user = $this->common->get_user_display(array("username" => $r->client_username, "avatar" => $r->client_avatar, "online_timestamp" => $r->client_online_timestamp, "first_name" => $r->client_first_name, "last_name" => $r->client_last_name));
            $this->datatables->data[] = array(
                $user,
                $r->mobile_phone,
                $r->email,
                $r->class_name,
                $r->branch_name,
                $r->total . " " . $r->symbol
            );
        }
        echo json_encode($this->datatables->process());
    }
    
    public function not_payed_students(){
        if (!$this->common->has_permissions(array("admin", "invoice_manager"), $this->user)) {
            $this->template->error(lang("error_2"));
        }
        $this->template->loadData("activeLink", array("invoice" => array("not_payed" => 1)));

        $this->template->loadContent("invoices/not_payed_students.php", array(
                )
        );
    }
    
    public function partially_payed_students(){
        if (!$this->common->has_permissions(array("admin", "invoice_manager"), $this->user)) {
            $this->template->error(lang("error_2"));
        }
        $this->template->loadData("activeLink", array("invoice" => array("partially_payed" => 1)));

        $this->template->loadContent("invoices/partially_payed_students.php", array(
                )
        );
    }

    public function add_payment_account() {
        if (!$this->common->has_permissions(array("admin", "invoice_manager"), $this->user)) {
            $this->template->error(lang("error_2"));
        }
        $name = $this->common->nohtml($this->input->post("name"));

        if (empty($name)) {
            $this->template->error(lang("error_81"));
        }

        $email = $this->common->nohtml($this->input->post("email"));
        $paypal_email = $this->common->nohtml($this->input->post("paypal_email"));
        $stripe_secret_key = $this->common->nohtml($this->input->post("stripe_secret_key"));
        $stripe_publishable_key = $this->common->nohtml($this->input->post("stripe_publishable_key"));
        $checkout2_account_number = $this->common->nohtml($this->input->post("checkout2_account_number"));
        $checkout2_secret_key = $this->common->nohtml($this->input->post("checkout2_secret_key"));

        $address_line_1 = $this->common->nohtml($this->input->post("address_line_1"));
        $address_line_2 = $this->common->nohtml($this->input->post("address_line_2"));
        $city = $this->common->nohtml($this->input->post("city"));
        $state = $this->common->nohtml($this->input->post("state"));
        $zip = $this->common->nohtml($this->input->post("zip"));
        $country = $this->common->nohtml($this->input->post("country"));

        $first_name = $this->common->nohtml($this->input->post("first_name"));
        $last_name = $this->common->nohtml($this->input->post("last_name"));

        $this->invoices_model->add_paying_account(array(
            "name" => $name,
            "paypal_email" => $paypal_email,
            "stripe_secret_key" => $stripe_secret_key,
            "stripe_publishable_key" => $stripe_publishable_key,
            "checkout2_account_number" => $checkout2_account_number,
            "checkout2_secret_key" => $checkout2_secret_key,
            "address_line_1" => $address_line_1,
            "address_line_2" => $address_line_2,
            "city" => $city,
            "state" => $state,
            "zip" => $zip,
            "country" => $country,
            "first_name" => $first_name,
            "last_name" => $last_name,
            "email" => $email
                )
        );

        $this->session->set_flashdata("globalmsg", lang("success_97"));
        redirect(site_url("invoices/paying_accounts"));
    }

    public function delete_paying_account($id, $hash) {
        if (!$this->common->has_permissions(array("admin", "invoice_manager"), $this->user)) {
            $this->template->error(lang("error_2"));
        }
        if ($hash != $this->security->get_csrf_hash()) {
            $this->template->error(lang("error_6"));
        }
        $id = intval($id);
        $account = $this->invoices_model->get_paying_account($id);
        if ($account->num_rows() == 0) {
            $this->template->error(lang("error_124"));
        }

        $this->invoices_model->delete_paying_account($id);
        $this->session->set_flashdata("globalmsg", lang("success_98"));
        redirect(site_url("invoices/paying_accounts"));
    }

    public function edit_paying_account($id) {
        if (!$this->common->has_permissions(array("admin", "invoice_manager"), $this->user)) {
            $this->template->error(lang("error_2"));
        }
        $id = intval($id);
        $account = $this->invoices_model->get_paying_account($id);
        if ($account->num_rows() == 0) {
            $this->template->error(lang("error_124"));
        }
        $account = $account->row();

        $this->template->loadData("activeLink", array("invoice" => array("pay" => 1)));

        $this->template->loadContent("invoices/edit_paying_account.php", array(
            "account" => $account
                )
        );
    }

    public function edit_paying_account_pro($id) {
        if (!$this->common->has_permissions(array("admin", "invoice_manager"), $this->user)) {
            $this->template->error(lang("error_2"));
        }
        $id = intval($id);
        $account = $this->invoices_model->get_paying_account($id);
        if ($account->num_rows() == 0) {
            $this->template->error(lang("error_124"));
        }
        $account = $account->row();

        $this->template->loadData("activeLink", array("invoice" => array("pay" => 1)));

        $name = $this->common->nohtml($this->input->post("name"));

        if (empty($name)) {
            $this->template->error(lang("error_81"));
        }

        $paypal_email = $this->common->nohtml($this->input->post("paypal_email"));
        $stripe_secret_key = $this->common->nohtml($this->input->post("stripe_secret_key"));
        $stripe_publishable_key = $this->common->nohtml($this->input->post("stripe_publishable_key"));
        $checkout2_account_number = $this->common->nohtml($this->input->post("checkout2_account_number"));
        $checkout2_secret_key = $this->common->nohtml($this->input->post("checkout2_secret_key"));
        $email = $this->common->nohtml($this->input->post("email"));

        $address_line_1 = $this->common->nohtml($this->input->post("address_line_1"));
        $address_line_2 = $this->common->nohtml($this->input->post("address_line_2"));
        $city = $this->common->nohtml($this->input->post("city"));
        $state = $this->common->nohtml($this->input->post("state"));
        $zip = $this->common->nohtml($this->input->post("zip"));
        $country = $this->common->nohtml($this->input->post("country"));

        $first_name = $this->common->nohtml($this->input->post("first_name"));
        $last_name = $this->common->nohtml($this->input->post("last_name"));

        $this->invoices_model->update_paying_account($id, array(
            "name" => $name,
            "paypal_email" => $paypal_email,
            "stripe_secret_key" => $stripe_secret_key,
            "stripe_publishable_key" => $stripe_publishable_key,
            "checkout2_account_number" => $checkout2_account_number,
            "checkout2_secret_key" => $checkout2_secret_key,
            "address_line_1" => $address_line_1,
            "address_line_2" => $address_line_2,
            "city" => $city,
            "state" => $state,
            "zip" => $zip,
            "country" => $country,
            "first_name" => $first_name,
            "last_name" => $last_name,
            "email" => $email
                )
        );

        $this->session->set_flashdata("globalmsg", lang("success_99"));
        redirect(site_url("invoices/paying_accounts"));
    }

    public function view($id, $hash) {
        $id = intval($id);
        $invoice = $this->invoices_model->get_invoice($id);
        if ($invoice->num_rows() == 0) {
            $this->template->error(lang("error_134"));
        }

        $invoice = $invoice->row();

        if ($invoice->hash != $hash) {
            $this->template->error(lang("error_6"));
        }

        $items = $this->invoices_model->get_invoice_items($id);
        $settings = $this->invoices_model->get_invoice_settings();
        $settings = $settings->row();


        $stripe = null;
        $checkout2 = null;
        $paypal = null;

        if ($settings->enable_stripe) {
            if (!empty($invoice->stripe_secret_key) && !empty($invoice->stripe_publishable_key)) {
                $stripe = true;
            }
        }

        if ($settings->enable_paypal) {
            if (!empty($invoice->paypal_email)) {
                $paypal = true;
            }
        }

        if ($settings->enable_checkout2) {
            if (!empty($invoice->checkout2_account_number) && !empty($invoice->checkout2_secret_key)) {
                $checkout2 = true;
            }
        }


        if ($invoice->clientid == 0) {
            $invoice->client_first_name = $invoice->guest_name;
            $invoice->client_last_name = "";
            $invoice->client_address_1 = "";
            $invoice->client_address_2 = "";
            $invoice->client_city = "";
            $invoice->client_state = "";
            $invoice->client_zipcode = "";
            $invoice->client_country = "";
            $invoice->client_username = lang("ctn_819");
            $invoice->client_email = $invoice->guest_email;
        }

        // Calulate amount left to pay
        $payments_total = $this->invoices_model->get_invoice_payments_total($invoice->ID);
        $payments = $this->invoices_model->get_invoice_payments($invoice->ID);


        $this->template->loadAjax("invoices/themes/" . $invoice->theme_file . ".php", array(
            "invoice" => $invoice,
            "items" => $items,
            "settings" => $settings,
            "payments_total" => $payments_total,
            "payments" => $payments,
            "stripe" => $stripe,
            "paypal" => $paypal,
            "checkout2" => $checkout2
                ), 1
        );
    }

    public function get_pdf($id, $hash) {
        $id = intval($id);
        $invoice = $this->invoices_model->get_invoice($id);
        if ($invoice->num_rows() == 0) {
            $this->template->error(lang("error_134"));
        }

        $invoice = $invoice->row();

        if ($invoice->hash != $hash) {
            $this->template->error(lang("error_6"));
        }

        $items = $this->invoices_model->get_invoice_items($id);
        $settings = $this->invoices_model->get_invoice_settings();
        $settings = $settings->row();

        if ($invoice->clientid == 0) {
            $invoice->client_first_name = $invoice->guest_name;
            $invoice->client_last_name = "";
            $invoice->client_address_1 = "";
            $invoice->client_address_2 = "";
            $invoice->client_city = "";
            $invoice->client_state = "";
            $invoice->client_zipcode = "";
            $invoice->client_country = "";
            $invoice->client_username = lang("ctn_876");
            $invoice->client_email = $invoice->guest_email;
        }

        // Calulate amount left to pay
        $payments_total = $this->invoices_model->get_invoice_payments_total($invoice->ID);
        $payments = $this->invoices_model->get_invoice_payments($invoice->ID);

        ob_start();
        $this->template->loadAjax("invoices/themes/" . $invoice->theme_file . "_pdf.php", array(
            "invoice" => $invoice,
            "items" => $items,
            "settings" => $settings,
            "payments" => $payments,
            "payments_total" => $payments_total
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

    public function get_itemdb_items() {

        $items = $this->invoices_model->get_invoice_items_db();
        $html = $this->template->loadAjaxReturn("invoices/get_itemdb.php", array(
            "items" => $items,
                )
        );

        echo json_encode(array("success" => 1, "html" => $html));
        exit();
    }

    public function get_itemdb_item($itemid) {
        $itemid = intval($itemid);
        $item = $this->invoices_model->get_invoice_item_db($itemid);
        if ($item->num_rows() == 0) {
            $this->template->jsonError("Invalid Item");
        }
        $item = $item->row();

        echo json_encode(array(
            "item_name" => $item->name,
            "item_desc" => $item->description,
            "item_price" => $item->price,
            "item_quantity" => $item->quantity
                )
        );
        exit();
    }

    public function add_invoice_payment($id) {
        $id = intval($id);
        $invoice = $this->invoices_model->get_invoice($id);
        if ($invoice->num_rows() == 0) {
            $this->template->error(lang("error_194"));
        }

        $invoice = $invoice->row();

        $amount = floatval($this->input->post("amount"));
        $type = intval($this->input->post("type"));
        $email = $this->common->nohtml($this->input->post("email"));
        $date = $this->common->nohtml($this->input->post("date"));
        $notes = $this->common->nohtml($this->input->post("notes"));


        if (!empty($date)) {
            $dd = DateTime::createFromFormat($this->settings->info->date_picker_format, $date);
            $dd_timestamp = $dd->getTimestamp();
        } else {
            $dd_timestamp = time();
        }



        $this->invoices_model->add_invoice_payment(array(
            "invoiceid" => $invoice->ID,
            "amount" => $amount,
            "processor" => $type,
            "timestamp" => $dd_timestamp,
            "userid" => $this->user->info->ID,
            "email" => $email,
            "notes" => $notes
                )
        );

        // Check total amount is greater than the Invoice total cost
        // If it is, set Invoice status to paid
        $payments_total = $this->invoices_model->get_invoice_payments_total($invoice->ID);
        if ($payments_total >= $invoice->total) {
            $this->invoices_model->update_invoice($invoice->ID, array(
                "status" => 2
                    )
            );
        } else {
            // Check for partial payments
            if ($payments_total > 0) {
                $status = 4; // Partial Payment Status
            } else {
                $status = 1;
            }
            $this->invoices_model->update_invoice($invoice->ID, array(
                "status" => $status
                    )
            );
        }

        $this->session->set_flashdata("globalmsg", lang("success_149"));
        redirect(site_url("invoices/edit_invoice/" . $invoice->ID . "?tab=4#invoice-bottom"));
    }

    public function delete_invoice_payment($id, $hash) {
        if ($hash != $this->security->get_csrf_hash()) {
            $this->template->error(lang("error_6"));
        }
        $id = intval($id);
        $payment = $this->invoices_model->get_invoice_payment($id);
        if ($payment->num_rows() == 0) {
            $this->template->error(lang("error_193"));
        }
        $payment = $payment->row();

        $invoice = $this->invoices_model->get_invoice($payment->invoiceid);
        if ($invoice->num_rows() == 0) {
            $this->template->error(lang("error_194"));
        }

        $invoice = $invoice->row();

        $this->invoices_model->delete_invoice_payment($id);


        // Check total amount is greater than the Invoice total cost
        // If it is, set Invoice status to paid
        $payments_total = $this->invoices_model->get_invoice_payments_total($payment->invoiceid);

        if ($payments_total >= $invoice->total) {
            $this->invoices_model->update_invoice($payment->invoiceid, array(
                "status" => 2
                    )
            );
        } else {
            // Check for partial payments
            if ($payments_total > 0) {
                $status = 4; // Partial Payment Status
            } else {
                $status = 1;
            }
            $this->invoices_model->update_invoice($invoice->ID, array(
                "status" => $status
                    )
            );
        }
        $this->session->set_flashdata("globalmsg", lang("success_150"));
        redirect(site_url("invoices/edit_invoice/" . $payment->invoiceid . "?tab=4#invoice-bottom"));
    }

    public function edit_invoice_payment($id) {
        $id = intval($id);
        $payment = $this->invoices_model->get_invoice_payment($id);
        if ($payment->num_rows() == 0) {
            $this->template->error(lang("error_193"));
        }
        $payment = $payment->row();

        $invoice = $this->invoices_model->get_invoice($payment->invoiceid);
        if ($invoice->num_rows() == 0) {
            $this->template->error(lang("error_194"));
        }

        $invoice = $invoice->row();

        $this->template->loadAjax("invoices/edit_invoice_payment.php", array(
            "payment" => $payment,
            "invoice" => $invoice
                )
        );
    }

    public function edit_invoice_payment_pro($id) {
        $id = intval($id);
        $payment = $this->invoices_model->get_invoice_payment($id);
        if ($payment->num_rows() == 0) {
            $this->template->error(lang("error_193"));
        }
        $payment = $payment->row();

        $invoice = $this->invoices_model->get_invoice($payment->invoiceid);
        if ($invoice->num_rows() == 0) {
            $this->template->error(lang("error_194"));
        }

        $invoice = $invoice->row();

        $amount = floatval($this->input->post("amount"));
        $type = intval($this->input->post("type"));
        $email = $this->common->nohtml($this->input->post("email"));
        $date = $this->common->nohtml($this->input->post("date"));
        $notes = $this->common->nohtml($this->input->post("notes"));


        if (!empty($date)) {
            $dd = DateTime::createFromFormat($this->settings->info->date_picker_format, $date);
            $dd_timestamp = $dd->getTimestamp();
        } else {
            $dd_timestamp = time();
        }



        $this->invoices_model->update_invoice_payment($id, array(
            "amount" => $amount,
            "processor" => $type,
            "timestamp" => $dd_timestamp,
            "userid" => $this->user->info->ID,
            "email" => $email,
            "notes" => $notes
                )
        );

        // Check total amount is greater than the Invoice total cost
        // If it is, set Invoice status to paid
        $payments_total = $this->invoices_model->get_invoice_payments_total($invoice->ID);
        if ($payments_total >= $invoice->total) {
            $this->invoices_model->update_invoice($invoice->ID, array(
                "status" => 2
                    )
            );
        } else {
            // Check for partial payments
            if ($payments_total > 0) {
                $status = 4; // Partial Payment Status
            } else {
                $status = 1;
            }
            $this->invoices_model->update_invoice($invoice->ID, array(
                "status" => $status
                    )
            );
        }

        $this->session->set_flashdata("globalmsg", lang("success_151"));
        redirect(site_url("invoices/edit_invoice/" . $invoice->ID . "?tab=4#invoice-bottom"));
    }

    public function items() {

        $this->template->loadData("activeLink", array("invoice" => array("items" => 1)));

        $this->template->loadContent("invoices/items.php", array(
                )
        );
    }

    public function item_page() {
        $this->load->library("datatables");

        $this->datatables->set_default_order("invoice_item_db.ID", "desc");

        // Set page ordering options that can be used
        $this->datatables->ordering(
                array(
                    0 => array(
                        "invoice_item_db.name" => 0
                    ),
                    2 => array(
                        "invoice_item_db.price" => 0
                    ),
                    3 => array(
                        "invoice_item_db.quantity" => 0
                    )
                )
        );

        $this->datatables->set_total_rows(
                $this->invoices_model->get_item_db_total()
        );

        $items = $this->invoices_model->get_item_db($this->datatables);

        foreach ($items->result() as $r) {



            $this->datatables->data[] = array(
                $r->name,
                $r->description,
                $r->price,
                $r->quantity,
                '<a href="' . site_url("invoices/edit_item/" . $r->ID) . '" class="btn btn-warning btn-xs" title="' . lang("ctn_55") . '"  data-toggle="tooltip" data-placement="bottom"><span class="glyphicon glyphicon-cog"></span></a> <a href="' . site_url("invoices/delete_item/" . $r->ID . "/" . $this->security->get_csrf_hash()) . '" class="btn btn-danger btn-xs"  data-toggle="tooltip" data-placement="bottom" onclick="return confirm(\'' . lang("ctn_317") . '\')" title="' . lang("ctn_57") . '"><span class="glyphicon glyphicon-trash"></span></a>'
            );
        }
        echo json_encode($this->datatables->process());
    }

    public function add_item() {
        $name = $this->common->nohtml($this->input->post("name"));
        $desc = $this->common->nohtml($this->input->post("description"));
        $price = floatval($this->input->post("price"));
        $quantity = floatval($this->input->post("quantity"));

        if (empty($name)) {
            $this->template->error(lang("error_195"));
        }

        $this->invoices_model->add_invoice_item_db(array(
            "name" => $name,
            "description" => $desc,
            "price" => $price,
            "quantity" => $quantity,
                )
        );

        $this->session->set_flashdata("globalmsg", lang("success_152"));
        redirect(site_url("invoices/items"));
    }

    public function edit_item($id) {
        $id = intval($id);
        $item = $this->invoices_model->get_item_db_single($id);
        if ($item->num_rows() == 0) {
            $this->template->error(lang("error_196"));
        }
        $item = $item->row();

        $this->template->loadData("activeLink", array("invoice" => array("items" => 1)));


        $this->template->loadContent("invoices/edit_item.php", array(
            "item" => $item
                )
        );
    }

    public function edit_item_pro($id) {
        $id = intval($id);
        $item = $this->invoices_model->get_item_db_single($id);
        if ($item->num_rows() == 0) {
            $this->template->error(lang("error_196"));
        }
        $item = $item->row();

        $name = $this->common->nohtml($this->input->post("name"));
        $desc = $this->common->nohtml($this->input->post("description"));
        $price = floatval($this->input->post("price"));
        $quantity = floatval($this->input->post("quantity"));

        if (empty($name)) {
            $this->template->error(lang("error_133"));
        }

        $this->invoices_model->update_item_db($id, array(
            "name" => $name,
            "description" => $desc,
            "price" => $price,
            "quantity" => $quantity,
                )
        );

        $this->session->set_flashdata("globalmsg", lang("success_153"));
        redirect(site_url("invoices/items"));
    }

    public function delete_item($id, $hash) {
        if ($hash != $this->security->get_csrf_hash()) {
            $this->template->error(lang("error_6"));
        }
        $id = intval($id);
        $item = $this->invoices_model->get_item_db_single($id);
        if ($item->num_rows() == 0) {
            $this->template->error(lang("error_196"));
        }

        $this->invoices_model->delete_item_db($id);
        $this->session->set_flashdata("globalmsg", lang("success_154"));
        redirect(site_url("invoices/items"));
    }

    public function get_payment_gateway($id, $hash) {
        $id = intval($id);
        $invoice = $this->invoices_model->get_invoice($id);
        if ($invoice->num_rows() == 0) {
            $this->template->error(lang("error_134"));
        }

        $invoice = $invoice->row();

        if ($invoice->hash != $hash) {
            $this->template->error(lang("error_6"));
        }

        $settings = $this->invoices_model->get_invoice_settings();
        $settings = $settings->row();

        // Calulate amount left to pay
        $payments_total = $this->invoices_model->get_invoice_payments_total($invoice->ID);


        $type = intval($this->input->get("type"));
        if ($type == 0) {
            // PayPal
            if (!$settings->enable_paypal) {
                $this->template->errori(lang("error_197"));
            }
            $amount_left = $invoice->total - $payments_total;
            $this->template->loadAjax("invoices/paypal_form.php", array(
                "invoice" => $invoice,
                "amount_left" => $amount_left
                    ), 1
            );
        }

        // Stripe Payment Gateway
        if ($type == 1) {
            $amount = floatval($this->input->get("amount"));
            if (!$settings->enable_stripe || ((empty($invoice->stripe_secret_key) || empty($invoice->stripe_publishable_key)) )) {
                $this->template->errori(lang("error_198"));
            }
            if ($amount > 0) {
                $amount_left = $invoice->total - $payments_total;
                $this->template->loadAjax("invoices/stripe_form.php", array(
                    "invoice" => $invoice,
                    "amount" => $amount
                        ), 1
                );
            } else {
                $amount_left = $invoice->total - $payments_total;
                $this->template->loadAjax("invoices/stripe_form_p1.php", array(
                    "invoice" => $invoice,
                    "amount_left" => $amount_left
                        ), 1
                );
            }
        }

        // 2Checkout
        if ($type == 2) {
            if (!$settings->enable_checkout2) {
                $this->template->errori(lang("error_199"));
            }
            $amount_left = $invoice->total - $payments_total;
            $this->template->loadAjax("invoices/2checkout_form.php", array(
                "invoice" => $invoice,
                "amount_left" => $amount_left
                    ), 1
            );
        }

        exit();
    }

}

?>