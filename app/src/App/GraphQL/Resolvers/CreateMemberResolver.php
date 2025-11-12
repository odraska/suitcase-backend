<?php

namespace SLONline\App\GraphQL\Resolvers;

use GraphQL\Type\Definition\ResolveInfo;
use InvalidArgumentException;
use SilverStripe\ORM\DataList;
use SilverStripe\Security\Member;
use SLONline\AddressManagement\Extensions\MemberExtension;
use SLONline\AddressManagement\Model\MemberAddress;
use SLONline\App\GraphQL\CaptchaBotID;

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
        if (!CaptchaBotID::singleton()->validateToken($args['captchaToken'])) {
            throw new InvalidArgumentException('Invalid Captcha token', 110);
        }

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

        foreach ($args['licenseAddresses'] ?? [] as $licenseAddressInput) {
            $licenseAddress = $member->licenseAddresses()->find('ID', $licenseAddressInput['id'] ?? 0);
            if (!$licenseAddress) {
                $licenseAddress = MemberAddress::create();
            }
            $licenseAddress->FirstName = $licenseAddressInput['firstName'] ?? '';
            $licenseAddress->Surname = $licenseAddressInput['surname'] ?? '';
            $licenseAddress->Email = $licenseAddressInput['email'] ?? '';
            $licenseAddress->Organisation = $licenseAddressInput['organisation'] ?? '';
            $licenseAddress->Street = $licenseAddressInput['street'] ?? '';
            $licenseAddress->Street2 = $licenseAddressInput['street2'] ?? '';
            $licenseAddress->City = $licenseAddressInput['city'] ?? '';
            $licenseAddress->ZIP = $licenseAddressInput['zip'] ?? '';
            $licenseAddress->Phone = $licenseAddressInput['phone'] ?? '';
            $licenseAddress->CompanyID = $licenseAddressInput['companyID'] ?? '';
            $licenseAddress->TaxID = $licenseAddressInput['taxID'] ?? '';
            $licenseAddress->VATID = $licenseAddressInput['vatID'] ?? '';
            if (isset($licenseAddressInput['countryID'])) {
                $licenseAddress->CountryID = (int)$licenseAddressInput['countryID'];
            }
            if (isset($licenseAddressInput['stateID'])) {
                $licenseAddress->StateID = (int)$licenseAddressInput['stateID'];
            }
            $licenseAddress->write();
            if (!$member->licenseAddresses()->byID($licenseAddress->ID)) {
                $member->licenseAddresses()->add($licenseAddress);
            }
        }

        return $member;
    }
}
