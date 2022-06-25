<?php

namespace App\Controllers;

use App\Models\User;

class UserController extends BaseController
{
    public function index()
    {
        $config = $this->getConfig();
        $user = $this->getUserIfExist();

        $params = [
            'config' => $config,
            'user' => $user,
        ];

//        return $this->view();
    }

    public function logout()
    {
        $config = $this->getConfig();
        $this->di['session']->forget('user');

        return redirect($config['url'] . '/?c=login');
    }

}