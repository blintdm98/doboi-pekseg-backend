<?php

namespace App\Helpers;

class GeneralHelper
{
    public static function displayPrice(float|int $price, int $decimals = 2)
    {
        return number_format($price, $decimals) . ' ' . __('common.currency');
    }
}