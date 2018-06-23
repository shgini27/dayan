<?php

class Invoices_Model extends CI_Model 
{

	public function add_paying_account($data) 
	{
		$this->db->insert("paying_accounts", $data);
	}

	public function get_user_paying_account($userid) 
	{
		return $this->db->where("userid", $userid)->get("paying_accounts");
	}

	public function get_paying_account($id) 
	{
		return $this->db->where("ID", $id)->get("paying_accounts");
	}

	public function update_paying_account($id, $data) 
	{
		$this->db->where("ID", $id)->update("paying_accounts", $data);
	}

	public function delete_paying_account($id) 
	{
		$this->db->where("ID", $id)->delete("paying_accounts");
	}

	public function get_total_paying_accounts() 
	{
		$s = $this->db->where("userid", 0)->select("COUNT(*) as num")->get("paying_accounts");
		$r = $s->row();
		if(isset($r->num)) return $r->num;
		return 0;
	}

	public function get_paying_accounts($datatable) 
	{
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

	public function get_all_paying_accounts() 
	{
		return $this->db->where("userid", 0)->get("paying_accounts");
	}

	public function get_currencies() 
	{
		return $this->db->get("currencies");
	}

	public function get_currency($id) 
	{
		return $this->db->where("ID", $id)->get("currencies");
	}

	public function get_last_invoice() 
	{
		return $this->db->order_by("ID", "DESC")->get("invoices");
	}

	public function add_invoice($data) 
	{
		$this->db->insert("invoices", $data);
		return $this->db->insert_id();
	}

	public function add_invoice_item($data) 
	{
		$this->db->insert("invoice_items", $data);
	}

	public function get_invoices($template, $userid, $datatable) 
	{
		$datatable->db_order();

		$datatable->db_search(array(
			"invoices.invoice_id",
			"invoices.title",
			"users.username",
			)
		);

		if($userid > 0) {
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

	public function get_invoices_total($template, $userid) 
	{
		if($userid > 0) {
			$this->db->where("invoices.clientid", $userid);
		}
		$s = $this->db->select("COUNT(*) as num")
			->where("invoices.template", $template)
			->join("users", "users.ID = invoices.clientid")
			->join("currencies", "currencies.ID = invoices.currencyid")
			->get("invoices");
		$r = $s->row();
		if(isset($r->num)) return $r->num;
		return 0;
	}

	public function get_invoice($id) 
	{
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

	public function get_invoice_items($id) 
	{
		return $this->db->where("invoiceid", $id)->get("invoice_items");
	}

	public function delete_invoice_items($id) 
	{
		$this->db->where("invoiceid", $id)->delete("invoice_items");
	}

	public function update_invoice($id, $data) 
	{
		$this->db->where("ID", $id)->update("invoices", $data);
	}

	public function delete_invoice($id) 
	{
		$this->db->where("ID", $id)->delete("invoices");
	}

	public function get_invoice_settings()
	{
		return $this->db->where("ID", 1)->get("invoice_settings");
	}

	public function update_settings($data) 
	{
		$this->db->where("ID", 1)->update("invoice_settings", $data);
	}

	public function add_reoccuring_invoice($data) 
	{
		$this->db->insert("invoice_reoccur", $data);
	}

	public function get_reoccuring_invoices($datatable) 
	{
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

	public function get_reoccuring_invoices_all() 
	{
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

	public function get_reoccuring_invoices_total() 
	{
		$s = $this->db
			->select("COUNT(*) as num")
			->join("users", "users.ID = invoice_reoccur.clientid", "left outer")
			->get("invoice_reoccur");
		$r = $s->row();
		if(isset($r->num)) return $r->num;
		return 0;
	}

	public function get_reoccuring_invoice($id) 
	{
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

	public function delete_reoccuring_invoice($id) 
	{
		$this->db->where("ID", $id)->delete("invoice_reoccur");
	}

	public function update_reoccuring_invoice($id, $data) 
	{
		$this->db->where("ID", $id)->update("invoice_reoccur", $data);
	}

	public function get_invoice_templates_all() 
	{
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

	public function get_invoice_serviceid($id) 
	{
		return $this->db
			->where("serviceid", $id)
			->order_by("ID", "DESC")
			->get("invoices");
	}

	public function get_invoice_items_db() 
	{
		return $this->db->get("invoice_item_db");
	}

	public function get_invoice_item_db($id) 
	{
		return $this->db->where("ID", $id)->get("invoice_item_db");
	}

	public function add_invoice_item_db($data) 
	{
		$this->db->insert("invoice_item_db", $data);
	}

	public function get_invoice_themes() 
	{
		return $this->db->get("invoice_themes");
	}

	public function get_invoice_theme($id) 
	{
		return $this->db->where("ID", $id)->get("invoice_themes");
	}

	public function get_invoice_payments($id) 
	{
		return $this->db->where("invoiceid", $id)->get("payment_logs");
	}

	public function add_invoice_payment($data) 
	{
		$this->db->insert("payment_logs", $data);
	}

	public function delete_invoice_payment($id) 
	{
		$this->db->where("ID", $id)->delete("payment_logs");
	}

	public function get_invoice_payment($id) 
	{
		return $this->db->where("ID", $id)->get("payment_logs");
	}

	public function get_invoice_payments_total($id) 
	{
		$s = $this->db->select("SUM(amount) as total")->where("invoiceid", $id)->get("payment_logs");
		$r = $s->row();
		if(isset($r->total)) return $r->total;
		return 0;
	}

	public function update_invoice_payment($id, $data) 
	{
		$this->db->where("ID", $id)->update("payment_logs", $data);
	}

	public function get_item_db_total() 
	{
		return $this->db->from("invoice_item_db")->count_all_results();
	}

	public function get_item_db($datatable) 
	{
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

	public function get_item_db_single($id) 
	{
		return $this->db->where("ID", $id)->get("invoice_item_db");
	}

	public function delete_item_db($id) 
	{
		$this->db->where("ID", $id)->delete("invoice_item_db");
	}

	public function update_item_db($id, $data) 
	{
		$this->db->where("ID", $id)->update("invoice_item_db", $data);
	}

	public function get_overdue_invoices($time) 
	{
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