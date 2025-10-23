<?php

namespace SLONline\App\GraphQL\Resolvers;

use GraphQL\Type\Definition\ResolveInfo;
use SilverStripe\ORM\DataList;
use SLONline\Commerce\Model\Discounts\DiscountCode;

/**
 * Validate Discount Code Resolver
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class ValidateDiscountCodeResolver
{
    public static function resolve($obj, array $args, array $context, ResolveInfo $info): bool
    {
        /** @var DiscountCode $discountCode */
        $discountCode = DataList::create(DiscountCode::class)
            ->filter(['Code' => $args['discountCode']])
            ->first();
        if ($discountCode && $discountCode->exists()) {
            return $discountCode->canUse()->valid;
        }

        return false;
    }
}
