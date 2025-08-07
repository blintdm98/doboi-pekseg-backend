<?php

namespace App\Helpers;

use App\Enums\OrderStatuses;

class GeneralHelper
{
    public static function displayPrice(float|int $price, int $decimals = 2)
    {
        return number_format($price, $decimals) . ' ' . __('common.currency');
    }

    public static function getStatusColors(): array
    {
        return [
            OrderStatuses::PENDING->value => 'bg-yellow-100 dark:bg-yellow-100 text-yellow-800 dark:text-yellow-800',
            OrderStatuses::COMPLETED->value => 'bg-green-100 dark:bg-green-100 text-green-800 dark:text-green-800',
            OrderStatuses::PARTIAL->value => 'bg-orange-100 dark:bg-orange-100 text-orange-800 dark:text-orange-800',
            OrderStatuses::CANCELED->value => 'bg-red-100 dark:bg-red-100 text-red-800 dark:text-red-800',
        ];
    }
}