<?php

namespace SLONline\App\GraphQL\Resolvers;

use GraphQL\Type\Definition\ResolveInfo;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\FieldType\DBMoney;
use SilverStripe\SiteConfig\SiteConfig;
use SLONline\Commerce\Model\discounts\VolumeDiscountModifier;
use SLONline\Commerce\Model\Order;
use SLONline\Elefont\Model\Commerce\FontFamilyOrderItem;
use SLONline\Elefont\Model\Commerce\FontOrderItem;
use SLONline\Elefont\Model\Font;
use SLONline\Elefont\Model\FontFamily;
use SLONline\Elefont\Model\FontFamilyPackage;
use SLONline\Elefont\Model\Licenses\Coefficient;
use SLONline\Elefont\Model\Licenses\License;


/**
 * Read Cart Resolver
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2023, SLONline, s.r.o.
 */
class ReadCartResolver
{
    public static function resolve($obj, array $args, array $context, ResolveInfo $info)
    {
        $order = Order::create();
        foreach ($args['familyProductSelections'] as $selection) {
            $product = null;
            switch ($selection['productType']) {
                case 'Family':
                    $product = FontFamily::get()->byID($selection['productID']);
                    break;
                case 'FamilyPackage':
                    $product = FontFamilyPackage::get()->byID($selection['productID']);
                    break;
                case 'Font':
                    $product = Font::get()->byID($selection['productID']);
                    break;
                default:
                    throw new \InvalidArgumentException('Unknown product type ' . $selection['productType']);
            }

            if (!$product) {
                continue;
            }

            $order->OrderItems()->add($product->crateOrderItem($selection['licenses']));
        }

        return $order;
    }
}
