<?php

namespace App\Classes;

class Session
{
    public function has($key): bool
    {
        if (isset($_SESSION[$key])) {
            return true;
        }

        return false;
    }

    public function put($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public function get($key)
    {
        if ($this->has($key)) {
            return $_SESSION[$key];
        }

        return null;
    }

    public function forget($key): bool
    {
        if ($this->has($key)) {
            unset($_SESSION[$key]);
            return session_destroy();
        }
        return false;
    }
}