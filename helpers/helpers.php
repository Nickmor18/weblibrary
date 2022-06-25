<?php

function abort404()
{
    echo "Страница не найдена";
    die;
}

function dump($data)
{
    echo "<pre>";
    print_r($data);
    echo "</pre>";
    die;
}

/**
 * Редирект
 *
 * @param $url
 * @param bool $permanent
 */
if (!function_exists('redirect')) {
    function redirect($url, $permanent = false)
    {
        if (headers_sent() === false) {
            header('Location: ' . $url, true, ($permanent === true) ? 301 : 302);
        }

        exit();
    }
}

/**
 * Хеширование пароля
 *
 * @param $pswd
 * @return false|string
 */
function _hpswd($pswd)
{
    return hash("sha256", $pswd);
}