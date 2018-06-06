<?php

class Behaviour_Model extends CI_Model 
{

	public function add_rule($data) 
	{
		$this->db->insert("rules", $data);
	}

	public function get_rule($id)
	{
		return $this->db->where("ID", $id)->get("rules");
	}

	public function delete_rule($id) 
	{
		$this->db->where("ID", $id)->delete("rules");
	}

	public function update_rule($id, $data) 
	{
		$this->db->where("ID", $id)->update("rules", $data);
	}

	public function get_rules($datatable) 
	{
		$datatable->db_order();

		$datatable->db_search(array(
			"rules.name"
			)
		);

		return $this->db
			->limit($datatable->length, $datatable->start)
			->get("rules");
	}

	public function get_rules_total() 
	{
		$s = $this->db->select("COUNT(*) as num")->get("rules");
		$r = $s->row();
		if(isset($r->num)) return $r->num;
		return 0;
	}

	public function add_record($data) 
	{
		$this->db->insert("behaviour_records", $data);
	}

	public function get_record($id) 
	{
		return $this->db->where("behaviour_records.ID", $id)
			->select("behaviour_records.ID, behaviour_records.userid,
				behaviour_records.incident, behaviour_records.timestamp,
				behaviour_records.teacherid, behaviour_records.ruleid,
				users.username, users.avatar, users.online_timestamp,
				users.first_name, users.last_name")
			->join("users", "users.ID = behaviour_records.userid")
			->get("behaviour_records");
	}

	public function delete_record($id) 
	{
		$this->db->where("ID", $id)->delete("behaviour_records");
	}

	public function update_record($id, $data) 
	{
		$this->db->where("ID", $id)->update("behaviour_records", $data);
	}

	public function get_records_total() 
	{
		$s = $this->db->select("COUNT(*) as num")->get("behaviour_records");
		$r = $s->row();
		if(isset($r->num)) return $r->num;
		return 0;
	}

	public function get_records($datatable) 
	{
		$datatable->db_order();

		$datatable->db_search(array(
			"users.username",
			"rules.name",
			"users2.username"
			)
		);

		return $this->db
			->select("behaviour_records.ID, behaviour_records.userid,
				behaviour_records.incident, behaviour_records.timestamp,
				behaviour_records.teacherid,
				users.username, users.avatar, users.online_timestamp,
				users.first_name, users.last_name,
				users2.username as username2, users2.avatar as avatar2,
				users2.online_timestamp as online_timestamp2,
				users2.first_name as first_name2, 
				users2.last_name as last_name2,
				rules.name")
			->join("users", "users.ID = behaviour_records.userid")
			->join("rules", "rules.ID = behaviour_records.ruleid")
			->join("users as users2", "users2.ID = behaviour_records.teacherid")
			->limit($datatable->length, $datatable->start)
			->get("behaviour_records");
	}

	public function get_records_total_user($id) 
	{
		$s = $this->db->where("userid", $id)->select("COUNT(*) as num")->get("behaviour_records");
		$r = $s->row();
		if(isset($r->num)) return $r->num;
		return 0;
	}

	public function get_records_user($id, $datatable) 
	{
		$datatable->db_order();

		$datatable->db_search(array(
			"users.username",
			"rules.name",
			"users2.username"
			)
		);

		return $this->db
			->where("behaviour_records.userid", $id)
			->select("behaviour_records.ID, behaviour_records.userid,
				behaviour_records.incident, behaviour_records.timestamp,
				behaviour_records.teacherid,
				users.username, users.avatar, users.online_timestamp,
				users.first_name, users.last_name,
				users2.username as username2, users2.avatar as avatar2,
				users2.online_timestamp as online_timestamp2,
				users2.first_name as first_name2, 
				users2.last_name as last_name2,
				rules.name")
			->join("users", "users.ID = behaviour_records.userid")
			->join("rules", "rules.ID = behaviour_records.ruleid")
			->join("users as users2", "users2.ID = behaviour_records.teacherid")
			->limit($datatable->length, $datatable->start)
			->get("behaviour_records");
	}

	public function get_all_rules() 
	{
		return $this->db->get("rules");
	}

}

?>