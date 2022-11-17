<?php

namespace App\Enums;

class ArticleStatusEnum
{

    const Live = 'live';

    const Draft = 'draft';

    public static function getListStatus()
    {
        return  [
            self::Live,
            self::Draft,
        ];
    }

    public static function getRandomValue()
    {
        $selection = self::getListStatus();
        $rand = rand(0, count($selection) - 1);
        return $selection[$rand];
    }
}
