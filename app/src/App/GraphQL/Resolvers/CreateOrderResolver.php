<?php

namespace SLONline\App\GraphQL\Resolvers;

use GraphQL\Type\Definition\ResolveInfo;
use SilverStripe\Security\Member;
use SilverStripe\Security\Security;
use SLONline\AddressManagement\Extensions\MemberExtension;
use SLONline\App\GraphQL\Schemas\Enums\FamilyProductSelectionProductTypeSchema;
use SLONline\Commerce\Model\Order;
use SLONline\Elefont\Model\Font;
use SLONline\Elefont\Model\FontFamily;
use SLONline\Elefont\Model\FontFamilyPackage;


/**
 * Create Order Resolver
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class CreateOrderResolver
{
    public static function resolve($obj, array $args, array $context, ResolveInfo $info)
    {
        /** @var Member|MemberExtension $member */
        $member = Security::getCurrentUser();

        $order = Order::create();
        if ($member && $member->exists()) {
            $order->MemberID = $member->ID;
            $order->setField('Email', $member->Email);
            $order->setField('InvoiceFirstName', $member->FirstName);
            $order->setField('InvoiceSurname', $member->Surname);
            $order->setField('InvoiceOrganisation', $member->Organisation);
            $order->setField('InvoiceStreet', $member->Street);
            $order->setField('InvoiceStreet2', $member->Street2);
            $order->setField('InvoiceCity', $member->City);
            $order->setField('InvoiceZIP', $member->ZIP);
            $order->setField('InvoiceCountryID', $member->CountryID);
            $order->setField('InvoiceStateID', $member->StateID);
        }
        $order->write();

        foreach ($args['familyProductSelections'] as $selection) {
            $product = null;
            switch ($selection['productType']) {
                case FamilyProductSelectionProductTypeSchema::FamilyProduct:
                    $product = FontFamily::get()->byID($selection['productID']);
                    break;
                case FamilyProductSelectionProductTypeSchema::FamilyPackageProduct:
                    $product = FontFamilyPackage::get()->byID($selection['productID']);
                    break;
                case FamilyProductSelectionProductTypeSchema::FontProduct:
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
