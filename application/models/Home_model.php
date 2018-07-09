<?php

class Home_Model extends CI_Model 
{

	public function get_home_stats() 
	{
		return $this->db->get("home_stats");
	}

	public function update_home_stats($data) 
	{
		$this->db->where("ID", 1)->update("home_stats", $data);
	}

	public function get_email_template($id) 
	{
		return $this->db->where("ID", $id)->get("email_templates");
	}

	public function get_email_template_hook($hook, $language) 
	{
		return $this->db->where("hook", $hook)
			->where("language", $language)->get("email_templates");
	}

	public function get_class_count() 
	{
		$s = $this->db->select("COUNT(*) as num")->get("classes");
		$r = $s->row();
		if(isset($r->num)) return $r->num;
		return 0;
	}

	public function get_news($limit) 
	{
		return $this->db
    		->select("users.ID as userid, users.username, users.avatar,
    			users.online_timestamp, users.first_name, users.last_name,
    			announcements.title, announcements.timestamp, announcements.ID, announcements.roles")
    		->join("users", "users.ID = announcements.userid")
    		->limit($limit)
    		->order_by("announcements.ID", "DESC")
    		->get("announcements");
	}

	public function get_user_assignments($userid, $limit) 
	{
		return $this->db
			->select("class_assignments.title, class_assignments.ID,
				class_assignments.due_date, class_assignments.type,
				user_assignments.mark, user_assignments.timestamp,
				classes.name, classes.ID as classid")
			->join("classes", "classes.ID = class_assignments.classid")
			->join("class_students", "class_students.classid = class_assignments.classid", "left outer")
			->join("user_assignments", "user_assignments.assignmentid = class_assignments.ID AND user_assignments.userid =" . $userid, "left outer")
			->group_start()
			->where("class_students.userid", $userid)
			->group_end()
			->order_by("class_assignments.due_date")
			->limit($limit)
			->get("class_assignments");
	}

	public function get_user_invoices($userid, $limit) 
	{
		return $this->db
			->where("invoices.clientid", $userid)
			->select("invoices.ID, invoices.invoice_id, 
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
			->where("invoices.template", 0)
			->join("users", "users.ID = invoices.clientid")
			->join("currencies", "currencies.ID = invoices.currencyid")
			->order_by("invoices.ID", "DESC")
			->limit($limit)
			->get("invoices");
	}

}

?>