<?php

namespace SLONline\App\GraphQL\Resolvers;

use GraphQL\Type\Definition\ResolveInfo;
use InvalidArgumentException;
use SilverStripe\Control\Controller;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Security\IdentityStore;
use SilverStripe\Security\Member;
use SilverStripe\Security\Security;
use SLONline\AddressManagement\Extensions\MemberExtension;

/**
 * Delete Member Resolver
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2023, SLONline, s.r.o.
 */
class DeleteMemberResolver
{
    public static function resolve($obj, array $args, array $context, ResolveInfo $info)
    {
        /** @var Member|MemberExtension $member */
        $member = Security::getCurrentUser();
        if ( ! $member || ! $member->exists()) {
            throw new InvalidArgumentException('Not logged in', 101);
        }

        $member->delete();

        Injector::inst()->get(IdentityStore::class)->logOut(Controller::curr()->getRequest());
        return true;
    }
}
