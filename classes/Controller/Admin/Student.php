<?php

class Controller_Admin_Student extends Controller_Admin {

	public function before()
	{
		parent::before();

		if ( ! Auth::user_is('admin'))
		{
			die('here');
			return $this->add_error('access.403');
		}
	}

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

	public function action_create()
	{
		$this->template->content->set_filename('admin/formpage');
		$this->template->title = 'Добавление ученика';
		$this->template->body_classes .= 'admin-student-create';
		$this->template->content->button_title = 'Добавить';

		/* @var Model_Student $student */
		$student = ORM::factory('Student');
		$this->template->content->fields = $student->describe_vof_fields();

		$values = $this->request->post();
		$this->template->content->bind('values', $values);
		$this->template->content->bind('errors', $this->_errors);

		if ( ! empty($values) AND $student->set_vof_values($values, $this->_errors))
		{
			$student->save();
			$this->redirect('/admin/students/');
		}
	}

	public function action_update()
	{
		/* @var Model_Student $student */
		$student = ORM::factory('Student')->where('id', '=', $this->request->param('id'))->find();
		if ( ! $student->loaded())
		{
			return $this->add_error('access.404');
		}

		$this->template->content->set_filename('admin/formpage');
		$this->template->title = 'Редактирование ученика '.$student->first_name.' '.$student->last_name;
		$this->template->body_classes .= 'admin-student-update';
		$this->template->content->button_title = 'Сохранить изменения';

		$this->template->content->fields = $student->describe_vof_fields();
		$values = array_merge($student->as_array(), $this->request->post());
		$this->template->content->bind('values', $values);
		$this->template->content->bind('errors', $this->_errors);

		if ( ! empty($this->request->post()) AND $student->set_vof_values($values, $this->_errors))
		{
			$student->save();
			$this->template->content->message = 'Изменения сохранены';
		}
	}

}