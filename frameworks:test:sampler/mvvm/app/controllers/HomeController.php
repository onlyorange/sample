<?php
namespace app\controllers;

class HomeController extends \jlmvc\core\WebController
{
	public function index()
	{
		//echo 'index! echo';
		return 'index';
	}

	public function about()
	{
		return 'about';
	}

	public function contact()
	{
		return 'contact';
	}
}

?>