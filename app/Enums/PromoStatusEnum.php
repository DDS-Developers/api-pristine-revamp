<?php

namespace App\Enums;

class PromoStatusEnum
{

    const Publish = 'publish';

    const Unpublish = 'unpublish';

    const Draft = 'draft';

    public function getListStatus()
    {
        return  [
            self::Publish,
            self::Unpublish,
            self::Draft,
        ];
    }

    public function getRandomValue()
    {
        $selection = $this->getListStatus();
        $rand = rand(0, count($selection) - 1);
        return $selection[$rand];
    }
}
