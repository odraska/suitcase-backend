<?php

namespace SLONline\App\GraphQL\Schemas\Types;

use SilverStripe\GraphQL\Schema\Exception\SchemaBuilderException;
use SilverStripe\GraphQL\Schema\Schema;
use SilverStripe\GraphQL\Schema\Type\Type;
use SLONline\App\GraphQL\PartialSchemaUpdater;

/**
 * Footer Button Type GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class FooterButton implements PartialSchemaUpdater
{
    /**
     * @throws SchemaBuilderException
     */
    public static function updateSchema(Schema $schema): void
    {
        $schema->addType(Type::create('FooterButton')
            ->setDescription('Represents a button in the footer of a page.')
            ->addField('borderColor', ['type' => 'String', 'description' => 'The border color of the button.'])
            ->addField('backgroundColor', ['type' => 'String', 'description' => 'The background color of the button.'])
            ->addField('textColor', ['type' => 'String', 'description' => 'The text color of the button.'])
            ->addField('borderRadius', ['type' => 'String', 'description' => 'The border radius of the button.'])
        );
    }
}
