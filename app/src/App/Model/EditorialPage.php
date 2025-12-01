<?php

namespace SLONline\App\Model;

use Page;
use SilverStripe\ORM\HasManyList;

/**
 * Editorial Page Data Object
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 *
 * @method HasManyList|ArticleCategory Categories
 */
class EditorialPage extends Page
{
    private static string $table_name = 'EditorialPage';

    private static string $singular_name = 'Editorial';
    private static string $plural_name = 'Editorials';

    private static array $allowed_children = [ArticlePage::class];

    private static array $has_many = [
        'Categories' => ArticleCategory::class,
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName('Content');

        return $fields;
    }
}
