<?php

session_start();
error_reporting(0);
require_once __DIR__ . '/vendor/autoload.php';
$config = include(__DIR__ . '/phinx.php');
define("ROOT_PATH", __DIR__);

//Отлавливаем все эксеппшены, так-же можно логировать в будущем
try {
    //Initial request
    $request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();

    //DI контейнер
    $container = new Pimple\Container();

    //общий когфиг
    $defaultEnv = $config['environments']['default_environment'];
    $container['config'] = $config['environments'][$defaultEnv];

    //Request data
    $container['request'] = $request;

    //Sessions
    $container['session'] = $container->factory(function ($c) {
        $session = new \App\Classes\Session();
        return $session;
    });

    //БД
    $dbConfig = $config['environments'][$defaultEnv];
    $container['db'] = function () use (&$dbConfig) {
        $adapter = $dbConfig['adapter'] ?? 'mysql';
        $host = $dbConfig['host'] ?? 'localhost';
        $database = $dbConfig['name'] ?? 'mysql';
        $user = $dbConfig['user'] ?? 'root';
        $password = $dbConfig['pass'] ?? '';
        $port = $dbConfig['port'] ?? '3306';
        $dsn = "$adapter:host=$host;port=$port;dbname=$database;user=$user;password=$password";

        $dbh = new PDO($dsn);
        if ($dbh)
            $log = "Connected to the <strong>$database</strong> database successfully!";

        return $dbh;
    };

    //Инициализируем вход
    (new App\Router($container))->init();

} catch (\App\Exceptions\LibraryException $e) {
    echo $e->getMessage();
} catch (PDOException $e) {
    $arResult['error']['message'] = "Ошибка обработки данных";
    echo(json_encode($arResult, JSON_UNESCAPED_UNICODE));
} catch (\Exception $e) {
    echo $e->getMessage();
}