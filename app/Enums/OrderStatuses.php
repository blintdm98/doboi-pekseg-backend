<?php

namespace App\Enums;

enum OrderStatuses: string
{
    case PENDING = 'pending';
    case PARTIAL = 'partial';
    case COMPLETED = 'completed';
    case CANCELED = 'canceled';
    case RETURNED = 'returned';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => __('common.status_pending'),
            self::PARTIAL => __('common.status_partial'),
            self::COMPLETED => __('common.status_completed'),
            self::CANCELED => __('common.status_canceled'),
            self::RETURNED => __('common.status_returned'),
        };
    }

    public static function toArray(): array
    {
        return array_map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label(),
        ], self::cases());
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::PARTIAL => 'orange',
            self::COMPLETED => 'green',
            self::CANCELED => 'red',
            self::RETURNED => 'purple',
        };
    }
}
