<?php

namespace App\Controllers;

use App\Models\User;
use Symfony\Component\HttpFoundation\Request;

class RegisterController extends BaseController
{
    /**
     * Выводим форму регистрации
     * @throws \App\Exceptions\LibraryException
     */
    public function index()
    {
        $config = $this->getConfig();
        $params = [
            'url' => $config['url']
        ];

        return $this->view('register', $params);
    }

    /**
     * Обработчик регистрации
     */
    public function register()
    {
        $request = $this->di['request'];
        $data = $this->di['request']->request->all();
        $requestMethod = $request->server->get('REQUEST_METHOD');
        $this->di['session']->put('error', null);
        $this->di['session']->put('oldValue', $data);

        if ($requestMethod != 'POST')
            abort404();

        if (!isset($data['password_confirmation']) || $data['password_confirmation'] != $data['password']){
            $this->di['session']->put('error', "Неверное повторение пароля");
        }
        unset($data['password_confirmation']);

        $objUser = new User($this->di['db']);
        if (!$objUser->validate($request)){
            $this->di['session']->put('error', $objUser->getError());
        }

        if ($this->di['session']->get('error') !== null){
            return redirect($this->getConfig()['url'] . "/?c=register&error=".$this->di['session']->get('error'));
        }

        $data['password'] = _hpswd($data['password']);
        $data['created'] = date("Y-m-d H:i:s");
        if (is_array($data)) {
            if ($objUser = $objUser->insertData($data)) {
                return redirect($this->getConfig()['url'] . '/?c=login');
            }
        }

        $this->di['session']->put('error', "Ошибка сервера, попробуйте позже");
        return redirect($this->getConfig()['url'] . "/?c=register&error=true");
    }
}