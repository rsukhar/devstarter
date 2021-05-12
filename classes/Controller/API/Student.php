<?php

class Controller_API_Student extends Controller_API {

	public function action_delete()
	{
		if ( ! Auth::user_is('admin'))
		{
			return $this->add_error('access.403');
		}

		if ( ! Security::check_nonce($this->request->post('_nonce'), 'delete_student'))
		{
			return $this->add_error('access.403');
		}

		/** @var Model_Student $student */
		$student = ORM::factory('Student', $this->request->param('id'));
		if ( ! $student->loaded())
		{
			return $this->add_error('access.404');
		}

		$student->delete();
	}

}