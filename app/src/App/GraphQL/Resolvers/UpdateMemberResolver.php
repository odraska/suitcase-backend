<?php

namespace SLONline\App\GraphQL\Resolvers;

use GraphQL\Type\Definition\ResolveInfo;
use InvalidArgumentException;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Email\Email;
use SilverStripe\ORM\DataList;
use SilverStripe\Security\Member;
use SilverStripe\Security\Security;
use SLONline\AddressManagement\Extensions\MemberExtension;
use SLONline\AddressManagement\Module\Country;
use SLONline\AddressManagement\Module\State;
use SLONline\Kamok\Newsletter\NewsletterConfirmationLog;
use SLONline\Kamok\Newsletter\NewsletterGroup;


/**
 * Update Member Resolver
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2023, SLONline, s.r.o.
 */
class UpdateMemberResolver
{
    public static function resolve($obj, array $args, array $context, ResolveInfo $info)
    {
        /** @var Member|MemberExtension $member */
        $member = Security::getCurrentUser();
        if (!$member || !$member->exists()) {
            throw new InvalidArgumentException('Not logged in', 101);
        }

        if (\array_key_exists('email', $args)) {
            $email = $args['email'];
            if (!Email::is_valid_address($email)) {
                throw new InvalidArgumentException('Invalid email', 103);
            }

            if (DataList::create(Member::class)->filter([
                    'ID:not' => $member->ID,
                    'Email' => $email,
                ])->count() > 0) {
                throw new InvalidArgumentException('Email has an account', 102);
            }

            $member->Email = $args['email'];
        }

        if (\array_key_exists('firstName', $args)) {
            $member->FirstName = $args['firstName'];
        }

        if (\array_key_exists('surname', $args)) {
            $member->Surname = $args['surname'];
        }

        if (\array_key_exists('organisation', $args)) {
            $member->Organisation = $args['organisation'];
        }

        if (\array_key_exists('street', $args)) {
            $member->Street = $args['street'];
        }

        if (\array_key_exists('street2', $args)) {
            $member->Street2 = $args['street2'];
        }

        if (\array_key_exists('city', $args)) {
            $member->City = $args['city'];
        }

        if (\array_key_exists('zip', $args)) {
            $member->ZIP = $args['zip'];
        }

        if (\array_key_exists('phone', $args)) {
            $member->Phone = $args['phone'];
        }

        if (\array_key_exists('companyID', $args)) {
            $member->CompanyID = $args['companyID'];
        }

        if (\array_key_exists('taxID', $args)) {
            $member->TaxID = $args['taxID'];
        }

        if (\array_key_exists('vatID', $args)) {
            $member->VATID = $args['vatID'];
        }

        if (\array_key_exists('country', $args)) {
            $member->CountryID = 0;
            $country = DataList::create(Country::class)->filter(['Code' => $args['country']])->first();
            if ($country && $country->exists()) {
                $member->CountryID = $country->ID;
            }
        }

        if (\array_key_exists('state', $args)) {
            $member->StateID = 0;
            $state = DataList::create(State::class)->filter(['Code' => $args['state']])->first();
            if ($state && $state->exists()) {
                $member->StateID = $state->ID;
            }
        }

        if ($member->isChanged()) {
            $member->write();
        }

        if (isset($args['newsletter'])) {
            if ($args['newsletter'] === true) {
                $confirmation_hash = md5(bin2hex(random_bytes(16)));
                $groups = NewsletterGroup::getNewsletterGroupsLeafs();
                $member->Groups()->addMany($groups);
                foreach ($groups as $group) {
                    $confirmation = NewsletterConfirmationLog::create();
                    $confirmation->ConfirmHash = $confirmation_hash;
                    $confirmation->MemberID = $member->ID;
                    $confirmation->NewsletterGroupID = $group;
                    $confirmation->ActionType = 'SignUp';
                    $confirmation->Status = 'Confirmed';
                    $confirmation->SubscribeIP = Controller::curr()->getRequest()->getIP();
                    $confirmation->write();
                }
            } else {
                $groups = NewsletterGroup::getNewsletterGroupsLeafs();
                foreach ($groups as $group) {
                    $member->Groups()->removeByID($group);
                }

                $confirmation = NewsletterConfirmationLog::create();
                $confirmation->MemberID = $member->ID;
                $confirmation->NewsletterGroupID = 0;
                $confirmation->ActionType = 'SignOut';
                $confirmation->write();

                DataList::create(NewsletterConfirmationLog::class)
                    ->filter(['MemberID' => $member->ID, 'ActionType' => 'SignUp'])
                    ->each(function (NewsletterConfirmationLog $item) {
                        $item->ExpirationDate = date('Y-m-d H:i:s');
                        $item->write();
                    });
            }
        }

        return Member::get_by_id($member->ID);
    }
}
