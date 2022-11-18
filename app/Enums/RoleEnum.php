<?php

namespace App\Enums;

class RoleEnum
{

    const Admin = 1;

    const Customer = 2;

    public static function getData()
    {
        return [
            'Admin' => self::Admin,
            'Customer' => self::Customer
        ];
    }

    public static function getRandomData()
    {
        $data = array_values(self::getData());
        $rand = count($data) - 1;
        return $data[rand(0, $rand)];
    }
}
