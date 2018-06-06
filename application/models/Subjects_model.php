<?php

class Subjects_Model extends CI_Model 
{

	public function add_subject($data) 
	{
		$this->db->insert("subjects", $data);
		return $this->db->insert_id();
	}

	public function get_subject($id) 
	{
		return $this->db->where("ID", $id)->get("subjects");
	}

	public function update_subject($id, $data) 
	{
		$this->db->where("ID", $id)->update("subjects", $data);
	}

	public function delete_subject($id) 
	{
		$this->db->where("ID", $id)->delete("subjects");
	}

	public function get_total_subjects() 
	{
		$s = $this->db->select("COUNT(*) as num")->get("subjects");
		$r = $s->row();
		if(isset($r->num)) return $r->num;
		return 0;
	}

	public function get_subjects($datatable) 
	{
		$datatable->db_order();

		$datatable->db_search(array(
			"subjects.name"
			)
		);

		return $this->db
			->limit($datatable->length, $datatable->start)
			->get("subjects");
	}

	public function get_all_subjects() 
	{
		return $this->db->get("subjects");
	}

	public function add_teacher_to_subject($data) 
	{
		$this->db->insert("subject_teachers", $data);
	}

	public function get_total_subject_teachers() 
	{
		$s = $this->db->select("COUNT(*) as num")->get("subject_teachers");
		$r = $s->row();
		if(isset($r->num)) return $r->num;
		return 0;
	}

	public function get_subject_teachers($datatable) 
	{
		$datatable->db_order();

		$datatable->db_search(array(
			"users.username",
			"subjects.name"
			)
		);

		return $this->db
			->select("subject_teachers.ID, subject_teachers.head,
				subjects.name,
				users.username, users.avatar, users.online_timestamp,
				users.first_name, users.last_name")
			->join("subjects", "subject_teachers.subjectid = subjects.ID")
			->join("users", "users.ID = subject_teachers.teacherid")
			->limit($datatable->length, $datatable->start)
			->get("subject_teachers");
	}

	public function get_subject_teacher($id) 
	{
		return $this->db
			->where("subject_teachers.ID", $id)
			->select("subject_teachers.ID, subject_teachers.teacherid, 
				subject_teachers.subjectid, subject_teachers.head,
				users.username, users.avatar, users.online_timestamp")
			->join("users", "users.ID = subject_teachers.teacherid")
			->get("subject_teachers");
	}

	public function delete_subject_teacher($id) 
	{
		$this->db->where("ID", $id)->delete("subject_teachers");
	}

	public function update_teacher_to_subject($id, $data) 
	{
		$this->db->where("ID", $id)->update("subject_teachers", $data);
	}

	public function get_teachers_by_subject($subjectid) 
	{
		return $this->db
			->where("subject_teachers.subjectid", $subjectid)
			->select("users.username, users.avatar, users.online_timestamp,
				users.first_name, users.last_name, 
				subject_teachers.head")
			->join("users", "users.ID = subject_teachers.teacherid")
			->join("subjects", "subjects.ID = subject_teachers.subjectid")
			->order_by("subject_teachers.head", "DESC")
			->get("subject_teachers");
	}

	public function get_total_books($id) 
	{
		$s = $this->db->where("subjectid", $id)->select("COUNT(*) as num")->get("library_books");
		$r = $s->row();
		if(isset($r->num)) return $r->num;
		return 0;
	}

	public function get_books($id, $datatable) 
	{
		$datatable->db_order();

		$datatable->db_search(array(
			"library_books.title"
			)
		);

		return $this->db
			->where("library_books.subjectid", $id)
			->select("library_books.title, library_books.ID, library_books.author,
				library_books.stock, library_books.image")
			->limit($datatable->length, $datatable->start)
			->get("library_books");
	}

}

?>