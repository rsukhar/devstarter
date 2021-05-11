<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Devtests extends Controller_Template {

	public $template = 'devtests/template';
	/**
	 * @var Assets Instance of Assets class
	 */
	protected $assets;

	protected $profiler;

	public function before()
	{
		parent::before();
		$this->assets = Assets::instance();
		$this->assets->add('base');
		$this->template->content = '';//View::factory();
		$this->template->title = $this->request->action();
		$this->profiler = Profiler::start('Devtest', $this->request->action());
	}

	public function action_test()
	{

	}

	public function after()
	{
		Profiler::stop($this->profiler);
		parent::after();
	}
}