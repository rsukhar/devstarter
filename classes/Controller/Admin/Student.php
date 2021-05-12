<?php

class Controller_Admin_Student extends Controller_Admin {

	public function action_list()
	{
		$this->template->content->set_filename('admin/student/list');
		$this->assets->add('student.list');
		$this->template->title = 'Список учеников';
		$this->template->body_classes .= 'admin-student-list';

		$students = [];
		foreach (ORM::factory('Student')->find_all() as $student)
		{
			$students[] = $student->as_array();
		}
		$this->template->content->students = $students;
	}

	public function action_get()
	{}

}