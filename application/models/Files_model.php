<?php

class Files_Model extends CI_Model 
{

	public function get_categories() 
	{
		return $this->db->get("file_categories");
	}

	public function get_category($id) 
	{
		return $this->db->where("ID", $id)->get("file_categories");
	}

	public function delete_category($id) 
	{
		$this->db->where("ID", $id)->delete("file_categories");
	}

	public function update_category($id, $data) 
	{
		$this->db->where("ID", $id)->update("file_categories", $data);
	}

	public function add_category($data) 
	{
		$this->db->insert("file_categories", $data);
	}

	public function add_file($data) 
	{
		$this->db->insert("files", $data);
		return $this->db->insert_id();
	}

	public function get_file($id) 
	{
		return $this->db
			->where("files.ID", $id)
			->select("files.ID, files.file_name, files.file_size, files.notes,
				files.timestamp, files.userid, files.categoryid,
				files.upload_file_name, files.extension, files.file_url,
				files.file_type,
				users.username, users.avatar, users.online_timestamp, 
				users.first_name, users.last_name")
			->join("users", "users.ID = files.userid")
			->get("files");
	}

	public function update_file($id, $data) 
	{
		$this->db->where("ID", $id)->update("files", $data);
	}

	public function delete_file($id) 
	{
		$this->db->where("ID", $id)->delete("files");
	}

	public function get_files_total() 
	{
		$s = $this->db->select("COUNT(*) as num")->get("files");
		$r = $s->row();
		if(isset($r->num)) return $r->num;
		return 0;
	}

	public function get_files($datatable) 
	{
		$datatable->db_order();

		$datatable->db_search(array(
			"files.file_name",
			"file_categories.name",
			"users.username"
			)
		);

		return $this->db->select("files.ID, files.file_name, files.file_size,
			files.file_type, files.timestamp, files.upload_file_name,
			users.username, users.avatar, users.online_timestamp, users.first_name,
			users.last_name,
			file_categories.name as catname")
			->join("users", "users.ID = files.userid")
			->join("file_categories", "file_categories.ID = files.categoryid")
			->limit($datatable->length, $datatable->start)
			->get("files");
	}

	public function get_files_by_name($name) 
	{
		return $this->db->like("file_name", $name)->get("files");
	}

	public function get_file_by_name($name) 
	{
		return $this->db->where("file_name", $name)->get("files");
	}

}

?>