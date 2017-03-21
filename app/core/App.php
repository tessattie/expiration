<?php 
class App{

	protected $controller = 'home';

	protected $method = 'index';

	protected $params = [];

	public function __construct()
	{
		ini_set('memory_limit', '512M');
		ini_set('max_execution_time', 1000);

		$url = $this->parseUrl();

		if(file_exists('../app/controllers/' . $url[0] . '.php'))
		{
			$this->controller = $url[0];
			unset($url[0]);
		}

		require_once '../app/controllers/' . $this->controller . '.php';

		$controllerName = $this->controller;
		$this->controller = new $this->controller;

		if($controllerName != 'login')
		{
			$this->controller->checkSession();
		}

		$methodName = $this->method;

		if(isset($url[1]))
		{
			$methodName = $url[1];
			if(method_exists($this->controller, $url[1]))
			{
				$this->method = $url[1];
				unset($url[1]);
			}
		}

		$this->params = $url ? array_values($url) : [] ;

		call_user_func_array([$this->controller, $this->method], $this->params);
		

		
	}

	public function parseUrl()
	{
		if(isset($_GET['url']))
		{
			return $url = explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
		}
	}
}