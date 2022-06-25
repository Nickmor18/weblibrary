<?php

namespace App\Controllers;

use App\Exceptions\LibraryException;

abstract class BaseController
{
    protected $di;
    protected $config;

    public function __construct($di)
    {
        //Уточняем что пришел именно нужный нам объект
        if ($di instanceof \Pimple\Container) {
            $this->di = $di;
        } else {
            abort404();
        }

        //возьмем настройки
        if ($this->di) {
            $this->config = $this->di['config'];
        } else {
            $config = include(__DIR__ . '/phinx.php');
            $this->config = $config['environments'];
        }

    }

    /**
     * Основные конфиги
     *
     * @return array
     */
    protected function getConfig(): array
    {
        if ($this->di) {
            return $this->di['config'];
        }

        $config = include(__DIR__ . '/phinx.php');
        return $config['environments'];
    }

    /**
     * Генерация views и параметров
     *
     * @param $file_name
     * @param array $params
     * @throws LibraryException
     */
    protected function view($file_name, array $params = [])
    {
        $ext = ".phtml";
        $path = __DIR__ . "/../Views/";
        if (!file_exists($path . $file_name . $ext)) {
            throw new LibraryException("View file not found");
        }

        ob_start();
        if ($params) {
            extract($params);
        }
        $template = include($path . $file_name . $ext);
        echo $template;
        ob_get_contents();
        ob_end_flush();
    }

    /**
     * Получение авторизованного пользователя
     *
     * @return void
     */
    protected function getUserIfExist()
    {
        $user = $this->di['session']->get('user') ?? null;
        if (!$user) {
            return redirect($this->config['url'] . '/?c=login');
        }

        return $user;
    }
}