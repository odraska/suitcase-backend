<?php

namespace SLONline\App\Controllers;

use SilverStripe\Admin\ModelAdmin;
use SLONline\App\Model\SavedCart;

/**
 * Saved Cart Admin
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class SavedCartAdmin extends ModelAdmin
{
    private static string $menu_title = 'Saved Carts';

    private static string $url_segment = 'saved-carts';

    private static int $menu_priority = -50;

    private static array $managed_models = [
        SavedCart::class
    ];
    private static array $allowed_actions = [

    ];

    public $showImportForm = false;
}
