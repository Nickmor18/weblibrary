<?php namespace App;

class Router
{
    private $controller, $action, $container;

    public function __construct($container)
    {
        $request = $container['request'];

        $this->container = $container;
        $controller = $request->get('c') ?? 'Login';
        $this->controller = ucfirst($controller) . "Controller";
        $this->action = $request->get('a') ?? 'index';

    }

    /**
     * Подключаем классы динамически
     */
    public function init()
    {
        $className = 'App\\Controllers\\' . $this->controller;

        if (!class_exists($className)) {
            abort404();
        }
        $objController = new  $className($this->container);
        $objController->{$this->action}();
    }
}