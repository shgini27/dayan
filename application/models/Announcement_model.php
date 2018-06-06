<?php

class Announcement_Model extends CI_Model 
{

	public function add($data) 
	{
		$this->db->insert("announcements", $data);
		return $this->db->insert_id();
	}

	public function update($id, $data) 
	{
		$this->db->where("ID", $id)->update("announcements", $data);
	}

	public function delete_announcement($id) 
	{
		$this->db->where("ID", $id)->delete("announcements");
	}

	public function get_announcement($id) 
	{
		return $this->db
			->where("announcements.ID", $id)
			->select("users.ID as userid, users.username, users.avatar,
    			users.online_timestamp, users.first_name, users.last_name,
    			announcements.title, announcements.timestamp, announcements.ID,
    			announcements.announcement")
    		->join("users", "users.ID = announcements.userid")
			->get("announcements");
	}

	public function get_announcements_total() 
	{
		$s = $this->db->select("COUNT(*) as num")->get("announcements");
		$r = $s->row();
		if(isset($r->num)) return $r->num;
		return 0;
	}

	public function get_announcements($datatable) 
	{
		$datatable->db_order();

		$datatable->db_search(array(
			"announcements.title",
			"users.username",
			)
		);

    	return $this->db
    		->select("users.ID as userid, users.username, users.avatar,
    			users.online_timestamp, users.first_name, users.last_name,
    			announcements.title, announcements.timestamp, announcements.ID")
    		->join("users", "users.ID = announcements.userid")
    		->limit($datatable->length, $datatable->start)
    		->get("announcements");
	}

}

?>