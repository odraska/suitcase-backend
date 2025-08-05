<?php

namespace SLONline\App\GraphQL\Resolvers;

use GraphQL\Type\Definition\ResolveInfo;
use InvalidArgumentException;
use SilverStripe\Control\Controller;
use SilverStripe\ORM\DataList;
use SilverStripe\Security\Member;
use SLONline\Google\GoogleCaptchaValidator;
use SLONline\Kamok\Newsletter\Form\NewsletterSubscribeForm;

/**
 * Create Member Resolver
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2023, SLONline, s.r.o.
 */
class CreateMemberResolver
{
    public static function resolve($obj, array $args, array $context, ResolveInfo $info)
    {
        if (\class_exists('SLONline\Google\GoogleCaptchaValidator')) {
            if (!GoogleCaptchaValidator::singleton()->validateToken($args['reCaptchaToken'])) {
                throw new InvalidArgumentException('Invalid reCaptcha token', 110);
            }
        }

        if ( ! \array_key_exists('password', $args) || empty($args['password'])) {
            throw new InvalidArgumentException('Missing password', 100);
        }

        $email = $args['email'];

        if (DataList::create(Member::class)->filter([
                'Email' => $email,
            ])->count() > 0) {
            throw new InvalidArgumentException('Email has an account', 102);
        }

        $member            = Member::create();
        $member->Email     = $args['email'];
        $member->Password  = $args['password'];

        if (isset($args['firstName'])) {
            $member->FirstName = $args['firstName'];
        }
        if (isset($args['surname'])) {
            $member->Surname = $args['surname'];
        }

        $member->write();

        if (isset($args['newsletter'])) {
            if ($args['newsletter'] === true) {
                NewsletterSubscribeForm::create(Controller::curr(), 'NewsletterSubscribeForm')
                                       ->subscribe(['Email' => $member->Email]);
            }
        }

        return $member;
    }
}
