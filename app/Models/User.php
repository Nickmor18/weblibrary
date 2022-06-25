<?php

namespace App\Models;

use Symfony\Component\HttpFoundation\Request;

class User extends Model
{
    protected $table = "users";
    protected static array $uniqueFields = ['email'];

    public function login($email, $password)
    {
        $sql = "SELECT * FROM users WHERE email = '" . $email . "' 
               AND password = '" . $password . "'";
        $query = $this->db->query($sql);
        $result = $query->fetchObject();

        return $result ?? null;
    }

    public function getError() : string {
        return $this->error;
    }

    public static function getUnique(){
        return self::$uniqueFields;
    }

}