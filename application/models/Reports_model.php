<?php

class Reports_Model extends CI_Model 
{

	public function get_finance_sum($date, $type) 
	{
		if($type) {
			$this->db->where("finance.amount >", 0);
		} else {
			$this->db->where("finance.amount <", 0);
		}

		$s = $this->db
			->where("finance.time_date", $date)
			->select("SUM(finance.amount) as num")
			->get("finance");
		$r = $s->row();
		if(isset($r->num)) return $r->num;
		return 0;
	}

	public function get_attendance($date) 
	{
		return $this->db->where("attendance_sheets.time_date", $date)
			->select("SUM(attendance_sheet_entries.present) as present, 
				SUM(attendance_sheet_entries.absent) as absent,
			 SUM(attendance_sheet_entries.late) as late, 
			 SUM(attendance_sheet_entries.holiday) as holiday")
			->join("attendance_sheets", "attendance_sheets.ID = attendance_sheet_entries.attendanceid")
			->get("attendance_sheet_entries");

	}
        
        public function get_custom_fields($user_id){
            
            return $this->db->where("user_custom_fields.userid", $user_id)
			->select("custom_fields.name as field_name, user_custom_fields.value as field_value")
			->join("custom_fields", "custom_fields.ID = user_custom_fields.fieldid")
			->get("user_custom_fields");
        }
        
        public function get_statistics() 
	{
            //select class_students.ID, class_categories.name as category_name, 
            //classes.name as class_name, users.city, users.state, class_students.userid as age, 
            //classes.subjectid as sex from class_students left join classes 
            //on(class_students.classid = classes.ID) left join class_categories 
            //on(classes.categoryid = class_categories.ID) left join users 
            //on(users.ID = class_students.userid)
		return $this->db->where("class_students.teacher_flag", 0)
			->select("class_students.ID, users.ID as user_id, class_categories.name as category_name,
                                classes.name as class_name, class_categories.end_date as year, users.birth_date, 
                                users.city, users.state, class_students.userid as age,
                                classes.subjectid as sex")
			->join("classes", "class_students.classid = classes.ID")
                        ->join("class_categories", "classes.categoryid = class_categories.ID")
                        ->join("users", "users.ID = class_students.userid")
			->get("class_students");

	}
        
        public function get_active_statistics($end_date) 
	{
		return $this->db->where("class_students.teacher_flag", 0)
                        ->where("end_date >=", $end_date)
			->select("class_students.ID, users.ID as user_id, class_categories.name as category_name,
                                classes.name as class_name, class_categories.end_date as year, users.birth_date, 
                                users.city, users.state, class_students.userid as age,
                                classes.subjectid as sex")
			->join("classes", "class_students.classid = classes.ID")
                        ->join("class_categories", "classes.categoryid = class_categories.ID")
                        ->join("users", "users.ID = class_students.userid")
			->get("class_students");

	}
}

?>