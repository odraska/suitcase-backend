<?php

namespace SLONline\App\Model;

use Page;

/**
 * Projects Holder Page Data Object
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class ProjectsPage extends Page
{
    private static string $table_name = 'ProjectsPage';

    private static string $singular_name = 'Projects';
    private static string $plural_name = 'Projects';

    private static array $allowed_children = [ProjectPage::class];
}
