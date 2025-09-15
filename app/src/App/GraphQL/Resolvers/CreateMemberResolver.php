<?php

namespace SLONline\App\GraphQL\Resolvers;

use GraphQL\Type\Definition\ResolveInfo;
use InvalidArgumentException;
use SilverStripe\ORM\DataList;
use SilverStripe\Security\Member;
use SLONline\AddressManagement\Extensions\MemberExtension;

/**
 * Create Member Resolver
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class CreateMemberResolver
{
    public static function resolve($obj, array $args, array $context, ResolveInfo $info): Member
    {
        /** @todo add reCaptcha / hCaptcha */
        /*if (\class_exists('SLONline\Google\GoogleCaptchaValidator')) {
            if (!GoogleCaptchaValidator::singleton()->validateToken($args['reCaptchaToken'])) {
                throw new InvalidArgumentException('Invalid reCaptcha token', 110);
            }
        }*/

        if (!\array_key_exists('password', $args) || empty($args['password'])) {
            throw new InvalidArgumentException('Missing password', 100);
        }

        $email = $args['email'];

        if (DataList::create(Member::class)->filter([
                'Email' => $email,
            ])->count() > 0) {
            throw new InvalidArgumentException('Email has an account', 102);
        }

        /* @var Member|MemberExtension $member */
        $member = Member::create();
        $member->Email = $args['email'];
        $member->Password = $args['password'];

        if (isset($args['firstName'])) {
            $member->FirstName = $args['firstName'];
        }
        if (isset($args['surname'])) {
            $member->Surname = $args['surname'];
        }
        if (isset($args['organisation'])) {
            $member->Organisation = $args['organisation'];
        }
        if (isset($args['street'])) {
            $member->Street = $args['street'];
        }
        if (isset($args['city'])) {
            $member->City = $args['city'];
        }
        if (isset($args['zip'])) {
            $member->ZIP = $args['zip'];
        }
        if (isset($args['countryID'])) {
            $member->CountryID = (int)$args['countryID'];
        }
        if (isset($args['stateID'])) {
            $member->StateID = (int)$args['stateID'];
        }
        if (isset($args['vatID'])) {
            $member->VATID = $args['vatID'];
        }

        $member->write();

        if (isset($args['newsletter'])) {
            if ($args['newsletter'] === true) {
                /** @todo add newsletter subscription */
                /*NewsletterSubscribeForm::create(Controller::curr(), 'NewsletterSubscribeForm')
                                       ->subscribe(['Email' => $member->Email]);*/
            }
        }

        return $member;
    }
}
