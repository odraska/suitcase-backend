<?php

namespace SLONline\App\GraphQL\Schemas\Mutations;

use SilverStripe\GraphQL\Schema\Field\Mutation;
use SilverStripe\GraphQL\Schema\Schema;
use SLONline\App\GraphQL\PartialSchemaUpdater;
use SLONline\App\GraphQL\Resolvers\NewsletterResolver;

/**
 * Newsletter Mutation GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class Newsletter implements PartialSchemaUpdater
{
    /**
     * @inheritDoc
     */
    public static function updateSchema(Schema $schema): void
    {
        $schema->addMutation(Mutation::create('newsletterSubscribe')
            ->setDescription('Subscribe to the newsletter')
            ->setType('Boolean', true)
            ->addArg('captchaToken', 'String!')
            ->addArg('email', 'String!')
            ->setResolver([NewsletterResolver::class, 'resolveNewsletterSubscribe'])
        );

        $schema->addMutation(Mutation::create('newsletterUnsubscribe')
            ->setDescription('Unsubscribe from the newsletter')
            ->setType('Boolean', true)
            ->addArg('captchaToken', 'String!')
            ->addArg('email', 'String!')
            ->setResolver([NewsletterResolver::class, 'resolveNewsletterUnsubscribe'])
        );
    }
}
