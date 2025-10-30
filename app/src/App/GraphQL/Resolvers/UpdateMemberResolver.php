<?php

namespace SLONline\App\GraphQL\Resolvers;

use GraphQL\Type\Definition\ResolveInfo;
use InvalidArgumentException;
use SilverStripe\Control\Email\Email;
use SilverStripe\ORM\DataList;
use SilverStripe\Security\Member;
use SilverStripe\Security\Security;
use SLONline\AddressManagement\Extensions\MemberExtension;
use SLONline\AddressManagement\Model\MemberAddress;


/**
 * Update Member Resolver
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2023, SLONline, s.r.o.
 */
class UpdateMemberResolver
{
    public static function resolve($obj, array $args, array $context, ResolveInfo $info): ?Member
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

        if (array_key_exists('password', $args) && !empty($args['password'])) {
            $validationResult = $member->changePassword($args['password']);
            if (!$validationResult->isValid()) {
                foreach ($validationResult->getMessages() as $message) {
                    throw new InvalidArgumentException($message['message'], 103);
                }
            }
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

        if (\array_key_exists('city', $args)) {
            $member->City = $args['city'];
        }

        if (\array_key_exists('zip', $args)) {
            $member->ZIP = $args['zip'];
        }

        if (\array_key_exists('phone', $args)) {
            $member->Phone = $args['phone'];
        }

        if (\array_key_exists('vatID', $args)) {
            $member->VATID = $args['vatID'];
        }

        if (\array_key_exists('countryID', $args)) {
            $member->CountryID = (int)$args['countryID'];
        }

        if (\array_key_exists('stateID', $args)) {
            $member->StateID = (int)$args['stateID'];

        }

        if ($member->isChanged()) {
            $member->write();
        }

        $licenseAddressesIDs = [0];
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
            $licenseAddressesIDs[] = $licenseAddress->ID;
        }
        // Remove licence addresses not in the input
        $member->licenseAddresses()
            ->exclude('ID', $licenseAddressesIDs)
            ->each(function (MemberAddress $item) {
                $item->delete();
            });

        /** @todo add newsletter */
        /*if (isset($args['newsletter'])) {
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
        }*/

        return Member::get_by_id($member->ID);
    }
}
