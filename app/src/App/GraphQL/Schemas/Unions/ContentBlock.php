<?php

namespace SLONline\App\GraphQL\Schemas\Unions;

use SilverStripe\GraphQL\Schema\Exception\SchemaBuilderException;
use SilverStripe\GraphQL\Schema\Schema;
use SilverStripe\GraphQL\Schema\Type\UnionType;
use SLONline\App\GraphQL\PartialSchemaUpdater;
use SLONline\App\GraphQL\Resolvers\ContentBlockResolver;

/**
 * Content Block Type GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2026, SLONline, s.r.o.
 */
class ContentBlock implements PartialSchemaUpdater
{

    /**
     * @inheritDoc
     * @throws SchemaBuilderException
     */
    public static function updateSchema(Schema $schema): void
    {
        $schema->addUnion(UnionType::create('ContentBlock', [
            'types' => [
                'CodeContentBlock',
                'HeadingContentBlock',
                'ListContentBlock',
                'ParagraphContentBlock',
                'QuoteContentBlock',
            ],
            'typeResolver' => [ContentBlockResolver::class, 'resolveType'],
        ]));
    }
}
