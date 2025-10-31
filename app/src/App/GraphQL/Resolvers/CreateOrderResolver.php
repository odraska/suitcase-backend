<?php

namespace SLONline\App\GraphQL\Resolvers;

use GraphQL\Type\Definition\ResolveInfo;
use SilverStripe\Core\Validation\ValidationException;
use SilverStripe\Security\Member;
use SilverStripe\Security\Security;
use SLONline\AddressManagement\Extensions\MemberExtension;
use SLONline\App\GraphQL\Schemas\Enums\FamilyProductSelectionProductTypeSchema;
use SLONline\Commerce\Extensions\Payment;
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
    /**
     * @throws ValidationException
     */
    public static function resolve($obj, array $args, array $context, ResolveInfo $info): Order
    {
        /** @var Member|MemberExtension $member */
        $member = Security::getCurrentUser();

        $order = Order::create();
        if ($member && $member->exists()) {
            $order->MemberID = $member->ID;
        }

        if ($args['member']) {
            $order->setField('Email', $args['member']['email']);
            $order->setField('InvoiceFirstName', $args['member']['firstName']);
            $order->setField('InvoiceSurname', $args['member']['surname']);
            $order->setField('InvoiceOrganisation', $args['member']['organisation']??'');
            $order->setField('InvoiceStreet', $args['member']['street']);
            $order->setField('InvoiceStreet2', $args['member']['street2']??'');
            $order->setField('InvoiceCity', $args['member']['city']);
            $order->setField('InvoiceZIP', $args['member']['zip']);
            $order->setField('InvoiceCountryID', $args['member']['countryID']);
            $order->setField('InvoiceStateID', $args['member']['stateID']);
            $order->setField('InvoicePhone', $args['member']['phone']??'');
            $order->setField('CompanyID', $args['member']['companyID']??'');
            $order->setField('TaxID', $args['member']['taxID']??'');
            $order->setField('VATID', $args['member']['vatID']??'');
        }

        if ($args['licenseAddress']) {
            $order->setField('LicenseFirstName', $args['licenseAddress']['firstName']);
            $order->setField('LicenseSurname', $args['licenseAddress']['surname']);
            $order->setField('LicenseOrganisation', $args['licenseAddress']['organisation']??'');
            $order->setField('LicenseStreet', $args['licenseAddress']['street']);
            $order->setField('LicenseStreet2', $args['licenseAddress']['street2']??'');
            $order->setField('LicenseCity', $args['licenseAddress']['city']);
            $order->setField('LicenseZIP', $args['licenseAddress']['zip']);
            $order->setField('LicenseCountryID', $args['licenseAddress']['countryID']);
            $order->setField('LicenseStateID', $args['licenseAddress']['stateID']);
            $order->setField('LicensePhone', $args['licenseAddress']['phone']??'');
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

        if ($args['discountCode']) {
            $order->applyDiscountCode($args['discountCode']);
        }

        Payment::createOrderPayment($args['paymentMethod'], $order);

        return $order;
    }
}
