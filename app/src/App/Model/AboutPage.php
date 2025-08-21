<?php

namespace SLONline\App\Model;

use Page;
use SilverStripe\ORM\DataList;

/**
 * About Page Data Object
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class AboutPage extends Page
{
    private static string $table = 'AboutPage';
    private static string $singular_name = 'About Page';
    private static string $plural_name = 'About Pages';

    private static array $allowed_children = ['none'];

    public function getTeamMembers(): DataList
    {
        return DataList::create(Author::class)->filter([
            'TeamMember' => true,
        ])->sort('Name', 'ASC');
    }
}
