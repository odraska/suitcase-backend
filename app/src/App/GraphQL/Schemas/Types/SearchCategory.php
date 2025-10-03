<?php

namespace SLONline\App\GraphQL\Schemas\Types;

use SilverStripe\GraphQL\Schema\Schema;
use SilverStripe\GraphQL\Schema\Type\Type;
use SLONline\App\GraphQL\PartialSchemaUpdater;

/**
 * Search Category Type GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class SearchCategory implements PartialSchemaUpdater
{

    /**
     * @inheritDoc
     */
    public static function updateSchema(Schema $schema): void
    {
        $schema->addType(Type::create('SearchCategory')
            ->setDescription('Category of search')
            ->addField('category', [
                'type' => 'SearchCategoryEnum!'
            ])
            ->addField('results', [
                'type' => '[SearchResult!]!',
                'plugins' => [
                    'paginateList' => true,
                    'sort' => false,
                    'filter' => false
                ]
            ])
        );
    }
}
