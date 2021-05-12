<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin extends Controller_Native {

	/**
	 * @var View General template
	 */
	public $template = 'admin/template';

	protected $benchmark;

	public function before()
	{
		if (Kohana::$profiling === TRUE)
		{
			$this->benchmark = Profiler::start('Action', $this->request->action());
		}
		parent::before();

		$this->assets = Assets::instance();
		$this->assets->add('base');
		$this->template->menu_items = [
			'/admin/students/' => 'Ученики',
		];

		if ( ! Auth::user_is_logged_in() AND $this->request->controller() !== 'Auth')
		{
			$this->redirect('/sign_in/');
		}
	}

	public function after()
	{
		if (isset($this->benchmark))
		{
			Profiler::stop($this->benchmark);
		}

		parent::after();
	}
}
