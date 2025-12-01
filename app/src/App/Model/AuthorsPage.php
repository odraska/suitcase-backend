<?php

namespace SLONline\App\Model;

use Page;

/**
 * Authors Page Data Object
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class AuthorsPage extends Page
{
    private static string $table_name = 'AuthorsPage';

    private static string $singular_name = 'Authors Page';
    private static string $plural_name = 'Authors Pages';

    private static array $allowed_children = ['none'];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName('Content');

        return $fields;
    }
}
