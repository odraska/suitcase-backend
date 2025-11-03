<?php

namespace SLONline\App\Model;

use Page;
use SilverStripe\Assets\Image;
use SilverStripe\ORM\ManyManyList;

/**
 * Article Page Data Object
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 *
 * @property string Annotation
 * @property int CoverImageID
 * @property int CategoryID
 * @method Image CoverImage()
 * @method ArticleCategory Category()
 * @method ManyManyList|Author Authors()
 */
class ArticlePage extends Page
{
    private static string $table_name = 'ArticlePage';

    private static string $singular_name = 'Article';
    private static string $plural_name = 'Articles';

    private static bool $can_be_root = false;
    private static array $allowed_children = ['none'];

    private static array $db = [
        'Annotation' => 'HTMLText',
    ];

    private static array $has_one = [
        'CoverImage' => Image::class,
        'Category' => ArticleCategory::class,
    ];

    private static array $many_many = [
        'Authors' => Author::class,
    ];
}
