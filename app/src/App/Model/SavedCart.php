<?php

namespace SLONline\App\Model;

use SilverStripe\ORM\DataObject;
use SLONline\ORM\FieldType\DBJSONText;

/**
 * Save Cart Data Object
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 *
 * @property string $Hash
 * @property DBJSONText|string $CartData
 */
class SavedCart extends DataObject
{
    private static string $table_name = 'SavedCarts';
    private static string $singular_name = 'Saved cart';
    private static string $plural_name = 'Saved carts';
    private static array $db = [
        'Hash' => 'Varchar(64)',
        'CartData' => DBJSONText::class,
    ];

    private static array $indexes = [
        'Hash' => true,
    ];

    public function getTitle(): string
    {
        return "Saved Cart #{$this->ID} ({$this->Hash})";
    }

    protected function onBeforeWrite(): void
    {
        parent::onBeforeWrite();
        if (empty($this->Hash)) {
            $this->Hash = bin2hex(random_bytes(32));
        }
    }
}
