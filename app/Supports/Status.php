<?php

namespace App\Supports;

class Status {
    const DRAFT = [
        'value' => 1,
        'label' => 'DRAFT',
        'color' => 'grey',
    ];    
    const SUBMITTED = [
        'value' => 2,
        'label' => 'PENDING APPROVAL',
        'color' => 'blue',
    ];    
    const APPROVED = [
        'value' => 3,
        'label' => 'APPROVED',
        'color' => 'green',
    ];
    const REJECTED = [
        'value' => 4,
        'label' => 'REJECTED',
        'color' => 'red',
    ];
    const HOLD = [
        'value' => 5,
        'label' => 'HOLD',
        'color' => 'yellow',
    ];
    const COMPLETED = [
        'value' => 6,
        'label' => 'COMPLETED',
        'color' => 'light-blue'
    ];
    const SUMMITTED_TO_SUPPLIER = [
        'value' => 7,
        'label' => 'SUMMITTED TO SUPPLIER',
        'color' => 'blue',
    ];
    const CONFIRMED_ORDER = [
        'value' => 8,
        'label' => 'CONFIRMED ORDER',
        'color' => 'green',
    ];
    const ON_DELIVERY = [
        'value' => 9,
        'label' => 'ON DELIVERY',
        'color' => 'blue',
    ];
    const PENDING = [
        'value' => 10,
        'label' => 'PENDING',
        'color' => 'blue',
    ];
    const UNPAID = [
        'value' => 1,
        'label' => 'UNPAID',
        'color' => 'red',
    ];
    const PARTIAL = [
        'value' => 2,
        'label' => 'PARTIAL',
        'color' => 'yellow',
    ];
    const PAID = [
        'value' => 3,
        'label' => 'PAID',
        'color' => 'green',
    ];

    public static function getLabel($value) {
        $status = [
            self::DRAFT,
            self::SUBMITTED,
            self::APPROVED,
            self::REJECTED,
            self::HOLD,
            self::COMPLETED,
        ];
        foreach ($status as $s) {
            if ($s['value'] == $value) {
                return $s['label'];
            }
        }
        return null;
    }

    public static function getValue($label) {
        $status = [
            self::DRAFT,
            self::SUBMITTED,
            self::APPROVED,
            self::REJECTED,
            self::HOLD,
            self::COMPLETED,
            self::SUMMITTED_TO_SUPPLIER,
            self::CONFIRMED_ORDER,
            self::ON_DELIVERY,
        ];
        foreach ($status as $s) {
            if ($s['label'] == $label) {
                return $s['value'];
            }
        }
        return null;
    }

    public static function getColor($value) {
        $status = [
            self::DRAFT,
            self::SUBMITTED,
            self::APPROVED,
            self::REJECTED,
            self::HOLD,
            self::COMPLETED,
            self::SUMMITTED_TO_SUPPLIER,
            self::CONFIRMED_ORDER,
            self::ON_DELIVERY,
        ];
        foreach ($status as $s) {
            if ($s['value'] == $value) {
                return $s['color'];
            }
        }
        return null;
    }

    public static function getAll() {
        return [
            self::DRAFT,
            self::SUBMITTED,
            self::APPROVED,
            self::REJECTED,
            self::HOLD,
            self::COMPLETED,
            self::SUMMITTED_TO_SUPPLIER,
            self::CONFIRMED_ORDER,
            self::ON_DELIVERY,
            self::PENDING,
        ];
    }
    public static function getPaymentStatus() {
        return [
            self::UNPAID,
            self::PARTIAL,
            self::PAID,
        ];
    }

}