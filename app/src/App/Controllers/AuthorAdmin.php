<?php

namespace SLONline\App\Controllers;

use SilverStripe\Admin\ModelAdmin;
use SLONline\App\Model\Author;

/**
 * Author Admin
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class AuthorAdmin extends ModelAdmin
{
    private static string $menu_title = 'Authors';

    private static string $url_segment = 'authors';

    private static int $menu_priority = 1;

    private static array $managed_models = [
        Author::class
    ];
    private static array $allowed_actions = [

    ];

    public $showImportForm = false;
}
