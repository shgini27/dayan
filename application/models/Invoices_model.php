<?php

class Invoices_Model extends CI_Model {

    public function add_paying_account($data) {
        $this->db->insert("paying_accounts", $data);
    }

    public function get_user_paying_account($userid) {
        return $this->db->where("userid", $userid)->get("paying_accounts");
    }

    public function get_paying_account($id) {
        return $this->db->where("ID", $id)->get("paying_accounts");
    }

    public function update_paying_account($id, $data) {
        $this->db->where("ID", $id)->update("paying_accounts", $data);
    }

    public function delete_paying_account($id) {
        $this->db->where("ID", $id)->delete("paying_accounts");
    }

    public function get_total_paying_accounts() {
        $s = $this->db->where("userid", 0)->select("COUNT(*) as num")->get("paying_accounts");
        $r = $s->row();
        if (isset($r->num))
            return $r->num;
        return 0;
    }

    public function get_paying_accounts($datatable) {
        $datatable->db_order();

        $datatable->db_search(array(
            "paying_accounts.name",
            "paying_accounts.paypal_email",
            "paying_accounts.address_line_1"
                )
        );

        return $this->db
                        ->where("userid", 0)
                        ->limit($datatable->length, $datatable->start)
                        ->get("paying_accounts");
    }

    public function get_all_paying_accounts() {
        return $this->db->where("userid", 0)->get("paying_accounts");
    }

    public function get_payed_and_partially_payed_students_ids() {
        $now = date("Y-m-d");
        return $this->db->select("class_students.ID")
                        ->where("class_students.teacher_flag", 0)
                        ->where_in("invoices.status", [2, 3, 4])
                        ->where("class_categories.end_date >=", $now)
                        ->join("users", "users.ID = class_students.userid", "INNER")
                        ->join("classes", "classes.ID = class_students.classid", "INNER")
                        ->join("invoices", "invoices.clientid = class_students.userid", "INNER")
                        ->join("branch", "branch.branch_id = classes.branch_id", "INNER")
                        ->join("room", "room.room_id = classes.room_id", "INNER")
                        ->join("class_categories", "class_categories.ID = classes.categoryid", "INNER")
                        ->join("currencies", "currencies.ID = invoices.currencyid", "INNER")
                        ->join("invoice_item_db", "invoice_item_db.class_id = classes.ID", "INNER")
                        ->join("invoice_items", "invoice_items.invoice_item_db_id = invoice_item_db.ID", "INNER")
                        ->get("class_students");
    }
    
    public function get_not_payed_students($datatable, $ids) {
        /* 

         *SELECT `classes`.`name` as `class_name`, `branch`.`name` as `branch_name`, 
         * `users`.`email`, `users`.`first_name` as `client_first_name`, `users`.`last_name` as 
         * `client_last_name`, `users`.`username` as `client_username`, `users`.`avatar` as 
         * `client_avatar`, `users`.`online_timestamp` as `client_online_timestamp`, `users`.`mobile_phone`, 
         * `room`.`code` as `r_code`, `class_categories`.`name` as `cat_name`, `classes`.`class_days`, 
         * `classes`.`start_hour`, `invoice_item_db`.`price` as `total`
        FROM `class_students`
        LEFT JOIN `users` ON `users`.`ID` = `class_students`.`userid`
        LEFT JOIN `classes` ON `classes`.`ID` = `class_students`.`classid`
        LEFT JOIN `branch` ON `branch`.`branch_id` = `classes`.`branch_id`
        LEFT JOIN `room` ON `room`.`room_id` = `classes`.`room_id`
        LEFT JOIN `class_categories` ON `class_categories`.`ID` = `classes`.`categoryid`
        LEFT JOIN `invoice_item_db` ON `invoice_item_db`.`class_id` = `classes`.`ID`
        WHERE `class_students`.`teacher_flag` = 0
        AND `class_students`.`ID` NOT IN(0)
        AND `class_categories`.`end_date` >= '2018-07-09'         */
        $now = date("Y-m-d");
        $datatable->db_order();

        $datatable->db_search(array(
            "users.username",
            "users.first_name",
            "users.mobile_phone"
                )
        );

        return $this->db->select("classes.name as class_name, branch.name as branch_name, users.email, currencies.symbol,
                            users.first_name as client_first_name, users.last_name as client_last_name, users.username as client_username, 
                            users.avatar as client_avatar, users.online_timestamp as client_online_timestamp, users.mobile_phone, room.code as r_code,
                            class_categories.name as cat_name, classes.class_days, classes.start_hour, invoice_item_db.price as total, currencies.symbol")
                        ->where("class_students.teacher_flag", 0)
                        ->where_not_in("class_students.ID", $ids)
                        ->where("class_categories.end_date >=", $now)
                        ->join("users", "users.ID = class_students.userid", "LEFT")
                        ->join("classes", "classes.ID = class_students.classid", "LEFT")
                        ->join("branch", "branch.branch_id = classes.branch_id", "LEFT")
                        ->join("room", "room.room_id = classes.room_id", "LEFT")
                        ->join("class_categories", "class_categories.ID = classes.categoryid", "LEFT")
                        ->join("invoice_item_db", "invoice_item_db.class_id = classes.ID", "LEFT")
                        ->join("currencies", "currencies.ID = 4", "LEFT")
                        ->limit($datatable->length, $datatable->start)
                        ->get("class_students");
    }
    public function get_payed_students($datatable, $status) {
        /*
         SELECT `classes`.`name` as `class_name`, `branch`.`name` as `branch_name`, `invoices`.`total`, `users`.`email`, `currencies`.`symbol`, `users`.`first_name` as `client_first_name`, `users`.`last_name` as `client_last_name`, `users`.`username` as `client_username`, `users`.`avatar` as `client_avatar`, `users`.`online_timestamp` as `client_online_timestamp`, `users`.`mobile_phone`, `invoices`.`ID` as `invoice_id`, `invoices`.`hash`
        FROM `class_students`
        INNER JOIN `users` ON `users`.`ID` = `class_students`.`userid`
        INNER JOIN `classes` ON `classes`.`ID` = `class_students`.`classid`
        INNER JOIN `invoices` ON `invoices`.`clientid` = `class_students`.`userid`
        INNER JOIN `branch` ON `branch`.`branch_id` = `classes`.`branch_id`
        INNER JOIN `class_categories` ON `class_categories`.`ID` = `classes`.`categoryid`
        INNER JOIN `currencies` ON `currencies`.`ID` = `invoices`.`currencyid`
        INNER JOIN `invoice_item_db` ON `invoice_item_db`.`class_id` = `classes`.`ID`
        INNER JOIN `invoice_items` ON `invoice_items`.`invoice_item_db_id` = `invoice_item_db`.`ID`
        WHERE `class_students`.`teacher_flag` = 0
        AND `invoices`.`status` = 2
        AND `class_categories`.`end_date` >= '2018-07-07'
         */

        $now = date("Y-m-d");
        $datatable->db_order();

        $datatable->db_search(array(
            "users.username",
            "users.first_name",
            "users.mobile_phone"
                )
        );

        return $this->db->select("classes.name as class_name, branch.name as branch_name, invoices.total, users.email, currencies.symbol,
                            users.first_name as client_first_name, users.last_name as client_last_name, users.username as client_username, 
                            users.avatar as client_avatar, users.online_timestamp as client_online_timestamp, users.mobile_phone, room.code as r_code,
                            invoices.ID as invoice_id, invoices.hash, class_categories.name as cat_name, classes.class_days, classes.start_hour")
                        ->where("class_students.teacher_flag", 0)
                        ->where("invoices.status", $status)
                        ->where("class_categories.end_date >=", $now)
                        ->join("users", "users.ID = class_students.userid", "INNER")
                        ->join("classes", "classes.ID = class_students.classid", "INNER")
                        ->join("invoices", "invoices.clientid = class_students.userid", "INNER")
                        ->join("branch", "branch.branch_id = classes.branch_id", "INNER")
                        ->join("room", "room.room_id = classes.room_id", "INNER")
                        ->join("class_categories", "class_categories.ID = classes.categoryid", "INNER")
                        ->join("currencies", "currencies.ID = invoices.currencyid", "INNER")
                        ->join("invoice_item_db", "invoice_item_db.class_id = classes.ID", "INNER")
                        ->join("invoice_items", "invoice_items.invoice_item_db_id = invoice_item_db.ID", "INNER")
                        ->limit($datatable->length, $datatable->start)
                        ->get("class_students");
    }
    
    public function get_total_not_payed_students($ids) {
        $now = date("Y-m-d");
        $s = $this->db->select("COUNT(*) as num")
                        ->where("class_students.teacher_flag", 0)
                        ->where_not_in("class_students.ID", $ids)
                        ->where("class_categories.end_date >=", $now)
                        ->join("users", "users.ID = class_students.userid", "LEFT")
                        ->join("classes", "classes.ID = class_students.classid", "LEFT")
                        ->join("branch", "branch.branch_id = classes.branch_id", "LEFT")
                        ->join("room", "room.room_id = classes.room_id", "LEFT")
                        ->join("class_categories", "class_categories.ID = classes.categoryid", "LEFT")
                        ->join("invoice_item_db", "invoice_item_db.class_id = classes.ID", "LEFT")
                        ->get("class_students");
        $r = $s->row();
        if (isset($r->num))
            return $r->num;
        return 0;
    }
    
    public function get_total_payed_students($status) {
        $now = date("Y-m-d");
        $s = $this->db->where("class_students.teacher_flag", 0)
                        ->where("invoices.status", $status)
                        ->where("class_categories.end_date >=", $now)
                        ->join("users", "users.ID = class_students.userid", "LEFT")
                        ->join("classes", "classes.ID = class_students.classid", "LEFT")
                        ->join("invoices", "invoices.clientid = class_students.userid", "LEFT")
                        ->join("branch", "branch.branch_id = classes.branch_id", "LEFT")
                        ->join("class_categories", "class_categories.ID = classes.categoryid", "LEFT")
                        ->select("COUNT(*) as num")
                        ->get("class_students");
        $r = $s->row();
        if (isset($r->num))
            return $r->num;
        return 0;
    }

    public function get_currencies() {
        return $this->db->get("currencies");
    }

    public function get_currency($id) {
        return $this->db->where("ID", $id)->get("currencies");
    }

    public function get_last_invoice() {
        return $this->db->order_by("ID", "DESC")->get("invoices");
    }

    public function add_invoice($data) {
        $this->db->insert("invoices", $data);
        return $this->db->insert_id();
    }

    public function add_invoice_item($data) {
        $this->db->insert("invoice_items", $data);
    }

    public function get_invoices($template, $userid, $datatable) {
        $datatable->db_order();

        $datatable->db_search(array(
            "invoices.invoice_id",
            "invoices.title",
            "users.username",
                )
        );

        if ($userid > 0) {
            $this->db->where("invoices.clientid", $userid);
        }

        return $this->db->select("invoices.ID, invoices.invoice_id, 
			invoices.title, invoices.notes, invoices.due_date, invoices.timestamp,
			invoices.userid, invoices.clientid,
			invoices.total, invoices.tax_name_1, invoices.tax_rate_1,
			invoices.tax_name_2, invoices.tax_rate_2, invoices.status,
			invoices.hash,
			users.username as client_username, users.avatar as client_avatar,
			users.online_timestamp as client_online_timestamp, 
			users.first_name as client_first_name, 
			users.last_name as client_last_name,
			currencies.name as currencyname, currencies.symbol,
			currencies.code")
                        ->where("invoices.template", $template)
                        ->join("users", "users.ID = invoices.clientid", "left outer")
                        ->join("currencies", "currencies.ID = invoices.currencyid")
                        ->order_by("invoices.ID", "DESC")
                        ->limit($datatable->length, $datatable->start)
                        ->get("invoices");
    }

    public function get_invoices_total($template, $userid) {
        if ($userid > 0) {
            $this->db->where("invoices.clientid", $userid);
        }
        $s = $this->db->select("COUNT(*) as num")
                ->where("invoices.template", $template)
                ->join("users", "users.ID = invoices.clientid")
                ->join("currencies", "currencies.ID = invoices.currencyid")
                ->get("invoices");
        $r = $s->row();
        if (isset($r->num))
            return $r->num;
        return 0;
    }

    public function get_invoice($id) {
        return $this->db
                        ->where("invoices.ID", $id)
                        ->select("invoices.ID, invoices.invoice_id, 
			invoices.title, invoices.notes, invoices.due_date, invoices.timestamp,
			invoices.userid, invoices.clientid,
			invoices.total, invoices.tax_name_1, invoices.tax_rate_1,
			invoices.tax_name_2, invoices.tax_rate_2, invoices.status,
			invoices.currencyid, invoices.hash, invoices.date_paid,
			invoices.paid_by, invoices.time_date_paid,
			invoices.template, invoices.themeid, invoices.term_notes,
			invoices.hidden_notes,
			invoices.guest_name, invoices.guest_email, invoices.paying_accountid,
			users.username as client_username, users.email as client_email,
			users.first_name as client_first_name, 
			users.last_name as client_last_name,
			users.address_line_1 as client_address_1, 
			users.mobile_phone as client_address_2,
			users.city as client_city,users.state as client_state,
			users.phone as client_zipcode, users.country as client_country,
			u2.username as acc_username, u2.email as acc_email, 
			u2.first_name as acc_first_name, u2.last_name as acc_last_name,
			currencies.name as currencyname, currencies.symbol,
			currencies.code,
			paying_accounts.address_line_1, paying_accounts.address_line_2,
			paying_accounts.city, paying_accounts.state, paying_accounts.zip,
			paying_accounts.country, paying_accounts.email,
			paying_accounts.paypal_email, paying_accounts.stripe_secret_key,
			paying_accounts.stripe_publishable_key, 
			paying_accounts.checkout2_account_number, 
			paying_accounts.checkout2_secret_key, paying_accounts.first_name,
			paying_accounts.last_name, paying_accounts.userid as pa_userid,
			invoice_themes.file as theme_file")
                        ->join("invoice_themes", "invoice_themes.ID = invoices.themeid")
                        ->join("paying_accounts", "paying_accounts.ID = invoices.paying_accountid")
                        ->join("users", "users.ID = invoices.clientid", "left outer")
                        ->join("users as u2", "u2.ID = invoices.userid", "left outer")
                        ->join("currencies", "currencies.ID = invoices.currencyid")
                        ->get("invoices");
    }

    public function get_invoice_items($id) {
        return $this->db->where("invoiceid", $id)->get("invoice_items");
    }

    public function delete_invoice_items($id) {
        $this->db->where("invoiceid", $id)->delete("invoice_items");
    }

    public function update_invoice($id, $data) {
        $this->db->where("ID", $id)->update("invoices", $data);
    }

    public function delete_invoice($id) {
        $this->db->where("ID", $id)->delete("invoices");
    }

    public function get_invoice_settings() {
        return $this->db->where("ID", 1)->get("invoice_settings");
    }

    public function update_settings($data) {
        $this->db->where("ID", 1)->update("invoice_settings", $data);
    }

    public function add_reoccuring_invoice($data) {
        $this->db->insert("invoice_reoccur", $data);
    }

    public function get_reoccuring_invoices($datatable) {
        $datatable->db_order();

        $datatable->db_search(array(
            "invoices.title",
            "users.username",
            "invoice_reoccur.start_date"
                )
        );

        return $this->db
                        ->select("invoice_reoccur.ID, invoice_reoccur.templateid,
				invoice_reoccur.clientid, invoice_reoccur.userid,
				invoice_reoccur.timestamp, invoice_reoccur.amount,
				invoice_reoccur.amount_time, invoice_reoccur.status,
				invoice_reoccur.start_date, invoice_reoccur.end_date,
				invoice_reoccur.last_occurence, invoice_reoccur.next_occurence,
				users.username, users.email, users.email_notification,
				users.avatar, users.online_timestamp, users.first_name,
				users.last_name,
				invoices.title")
                        ->join("users", "users.ID = invoice_reoccur.clientid", "left outer")
                        ->join("invoices", "invoices.ID = invoice_reoccur.templateid")
                        ->limit($datatable->length, $datatable->start)
                        ->get("invoice_reoccur");
    }

    public function get_reoccuring_invoices_all() {
        return $this->db
                        ->select("invoice_reoccur.ID, invoice_reoccur.templateid,
				invoice_reoccur.clientid, invoice_reoccur.userid,
				invoice_reoccur.timestamp, invoice_reoccur.amount,
				invoice_reoccur.amount_time, invoice_reoccur.status,
				invoice_reoccur.start_date, invoice_reoccur.end_date,
				invoice_reoccur.last_occurence, invoice_reoccur.next_occurence,
				users.username, users.email, users.email_notification,
				users.avatar, users.online_timestamp, users.first_name,
				users.last_name,
				invoices.title")
                        ->join("users", "users.ID = invoice_reoccur.clientid", "left outer")
                        ->join("invoices", "invoices.ID = invoice_reoccur.templateid")
                        ->get("invoice_reoccur");
    }

    public function get_reoccuring_invoices_total() {
        $s = $this->db
                ->select("COUNT(*) as num")
                ->join("users", "users.ID = invoice_reoccur.clientid", "left outer")
                ->get("invoice_reoccur");
        $r = $s->row();
        if (isset($r->num))
            return $r->num;
        return 0;
    }

    public function get_reoccuring_invoice($id) {
        return $this->db
                        ->where("invoice_reoccur.ID", $id)
                        ->select("invoice_reoccur.ID, invoice_reoccur.templateid,
				invoice_reoccur.clientid, invoice_reoccur.userid,
				invoice_reoccur.timestamp, invoice_reoccur.amount,
				invoice_reoccur.amount_time, invoice_reoccur.status,
				invoice_reoccur.start_date, invoice_reoccur.end_date,
				invoice_reoccur.last_occurence, invoice_reoccur.next_occurence,
				users.username, users.email, users.email_notification,
				users.avatar, users.online_timestamp, users.first_name,
				users.last_name,
				invoices.title")
                        ->join("users", "users.ID = invoice_reoccur.clientid", "left outer")
                        ->join("invoices", "invoices.ID = invoice_reoccur.templateid")
                        ->get("invoice_reoccur");
    }

    public function delete_reoccuring_invoice($id) {
        $this->db->where("ID", $id)->delete("invoice_reoccur");
    }

    public function update_reoccuring_invoice($id, $data) {
        $this->db->where("ID", $id)->update("invoice_reoccur", $data);
    }

    public function get_invoice_templates_all() {
        return $this->db->select("invoices.ID, invoices.invoice_id, 
			invoices.title, invoices.notes, invoices.due_date, invoices.timestamp,
			invoices.userid, invoices.clientid,
			invoices.total, invoices.tax_name_1, invoices.tax_rate_1,
			invoices.tax_name_2, invoices.tax_rate_2, invoices.status,
			invoices.hash,
			users.username as client_username, users.avatar as client_avatar,
			users.online_timestamp as client_online_timestamp,
			users.first_name as client_first_name, 
			users.last_name as client_last_name,
			currencies.name as currencyname, currencies.symbol,
			currencies.code")
                        ->where("invoices.template", 1)
                        ->join("users", "users.ID = invoices.clientid", "left outer")
                        ->join("currencies", "currencies.ID = invoices.currencyid")
                        ->order_by("invoices.ID", "DESC")
                        ->get("invoices");
    }

    public function get_invoice_serviceid($id) {
        return $this->db
                        ->where("serviceid", $id)
                        ->order_by("ID", "DESC")
                        ->get("invoices");
    }

    public function get_invoice_items_db() {
        return $this->db->get("invoice_item_db");
    }
    
    public function get_invoice_items_db_classids() {
        return $this->db->select("invoice_item_db.class_id")->where("class_id !=", 0)->get("invoice_item_db");
    }

    public function get_invoice_item_db($id) {
        return $this->db->where("ID", $id)->get("invoice_item_db");
    }

    public function add_invoice_item_db($data) {
        $this->db->insert("invoice_item_db", $data);
    }

    public function get_invoice_themes() {
        return $this->db->get("invoice_themes");
    }

    public function get_invoice_theme($id) {
        return $this->db->where("ID", $id)->get("invoice_themes");
    }

    public function get_invoice_payments($id) {
        return $this->db->where("invoiceid", $id)->get("payment_logs");
    }

    public function add_invoice_payment($data) {
        $this->db->insert("payment_logs", $data);
    }

    public function delete_invoice_payment($id) {
        $this->db->where("ID", $id)->delete("payment_logs");
    }

    public function get_invoice_payment($id) {
        return $this->db->where("ID", $id)->get("payment_logs");
    }

    public function get_invoice_payments_total($id) {
        $s = $this->db->select("SUM(amount) as total")->where("invoiceid", $id)->get("payment_logs");
        $r = $s->row();
        if (isset($r->total))
            return $r->total;
        return 0;
    }

    public function update_invoice_payment($id, $data) {
        $this->db->where("ID", $id)->update("payment_logs", $data);
    }

    public function get_item_db_total() {
        return $this->db->from("invoice_item_db")->count_all_results();
    }

    public function get_item_db($datatable) {
        $datatable->db_order();

        $datatable->db_search(array(
            "invoice_item_db.name",
                )
        );

        return $this->db
                        ->select("invoice_item_db.name, invoice_item_db.ID, invoice_item_db.description,
				invoice_item_db.price, invoice_item_db.quantity")
                        ->limit($datatable->length, $datatable->start)
                        ->get("invoice_item_db");
    }

    public function get_item_db_single($id) {
        return $this->db->where("ID", $id)->get("invoice_item_db");
    }

    public function delete_item_db($id) {
        $this->db->where("ID", $id)->delete("invoice_item_db");
    }

    public function update_item_db($id, $data) {
        $this->db->where("ID", $id)->update("invoice_item_db", $data);
    }

    public function get_overdue_invoices($time) {
        return $this->db
                        ->where("invoices.due_date <", $time)
                        ->where("invoices.status !=", 2)->where("invoices.status !=", 3)
                        ->select("invoices.ID, invoices.invoice_id, 
			invoices.title, invoices.notes, invoices.due_date, invoices.timestamp,
			invoices.userid, invoices.clientid,
			invoices.total, invoices.tax_name_1, invoices.tax_rate_1,
			invoices.tax_name_2, invoices.tax_rate_2, invoices.status,
			invoices.currencyid, invoices.hash, invoices.date_paid,
			invoices.paid_by, invoices.time_date_paid,
			invoices.template, invoices.themeid, invoices.term_notes,
			invoices.hidden_notes,
			invoices.guest_name, invoices.guest_email, invoices.paying_accountid,
			users.username as client_username, users.email as client_email,
			users.first_name as client_first_name, 
			users.last_name as client_last_name,
			users.address_line_1 as client_address_1, 
			users.address_line_2 as client_address_2,
			users.city as client_city,users.state as client_state,
			users.zip as client_zipcode, users.country as client_country,
			u2.username as acc_username, u2.email as acc_email, 
			u2.first_name as acc_first_name, u2.last_name as acc_last_name,
			currencies.name as currencyname, currencies.symbol,
			currencies.code,
			paying_accounts.address_line_1, paying_accounts.address_line_2,
			paying_accounts.city, paying_accounts.state, paying_accounts.zip,
			paying_accounts.country, paying_accounts.email,
			paying_accounts.paypal_email, paying_accounts.stripe_secret_key,
			paying_accounts.stripe_publishable_key, 
			paying_accounts.checkout2_account_number, 
			paying_accounts.checkout2_secret_key, paying_accounts.first_name,
			paying_accounts.last_name,
			invoice_themes.file as theme_file")
                        ->join("invoice_themes", "invoice_themes.ID = invoices.themeid")
                        ->join("paying_accounts", "paying_accounts.ID = invoices.paying_accountid")
                        ->join("users", "users.ID = invoices.clientid", "left outer")
                        ->join("users as u2", "u2.ID = invoices.userid", "left outer")
                        ->join("currencies", "currencies.ID = invoices.currencyid")
                        ->get("invoices");
    }

}

?>