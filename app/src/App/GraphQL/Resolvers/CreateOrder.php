<?php

namespace SLONline\App\GraphQL\Resolvers;

use GraphQL\Type\Definition\ResolveInfo;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\FieldType\DBMoney;
use SilverStripe\ORM\GroupedList;
use SilverStripe\Security\Member;
use SilverStripe\SiteConfig\SiteConfig;
use SLONline\AddressManagement\Extensions\MemberExtension;
use SLONline\AddressManagement\Module\Country;
use SLONline\AddressManagement\Module\State;
use SLONline\Adotbelow\PriceConfig\PriceConfig;
use SLONline\Commerce\Extensions\Payment;
use SLONline\Commerce\Model\discounts\VolumeDiscountModifier;
use SLONline\Commerce\Model\Order;
use SLONline\Elefont\Model\Commerce\FontFamilyOrderItem;
use SLONline\Elefont\Model\Commerce\FontOrderItem;
use SLONline\Elefont\Model\Font;
use SLONline\Elefont\Model\FontFamily;
use SLONline\Elefont\Model\Licenses\Coefficient;
use SLONline\Elefont\Model\Licenses\License;
use SLONline\Payment\OfflinePayments\BankTransferPayment;
use SLONline\Payment\StripePayment;


/**
 * Create Order Resolver
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2023, SLONline, s.r.o.
 */
class CreateOrder
{
    public static function resolve($obj, array $args, array $context, ResolveInfo $info)
    {
        $productSelection = $args['productSelection'];
        $country = null;
        $zeroVAT = false;

        /** @var License $license */
        $license = License::get_by_id($productSelection['licenseID']);
        /** @var Coefficient $coefficient */
        $coefficient = $license?->Coefficients()->filter(['ID' => $productSelection['coefficientID']])->first();

        $order = Order::create();
        $order->setField('Email', $args['user']['email']);
        $order->setField('InvoiceFirstName', $args['user']['firstName']);
        $order->setField('InvoiceSurname', $args['user']['surname']);
        if (isset($args['user']['organisation'])) {
            $order->setField('InvoiceOrganisation', $args['user']['organisation']);
        }
        if (isset($args['user']['street'])) {
            $order->setField('InvoiceStreet', $args['user']['street']);
        }
        if (isset($args['user']['street2'])) {
            $order->setField('InvoiceStreet2', $args['user']['street2']);
        }
        if (isset($args['user']['city'])) {
            $order->setField('InvoiceCity', $args['user']['city']);
        }
        if (isset($args['user']['zip'])) {
            $order->setField('InvoiceZIP', $args['user']['zip']);
        }
        if (isset($args['user']['country'])) {
            $order->InvoiceCountryID = 0;
            $country = DataList::create(Country::class)->filter(['Code' => $args['user']['country']])->first();
            if ($country && $country->exists()) {
                $order->InvoiceCountryID = $country->ID;
            }
        }
        if (isset($args['user']['state'])) {
            $order->InvoiceStateID = 0;
            $state = DataList::create(State::class)->filter(['Code' => $args['user']['state']])->first();
            if ($state && $state->exists()) {
                $order->InvoiceStateID = $state->ID;
            }
        }
        if (isset($args['user']['phone'])) {
            $order->setField('InvoicePhone', $args['user']['phone']);
        }
        if (isset($args['user']['companyID'])) {
            $order->setField('CompanyID', $args['user']['companyID']);
        }
        if (isset($args['user']['taxID'])) {
            $order->setField('TaxID', $args['user']['taxID']);
        }
        if (isset($args['user']['vatID'])) {
            $order->setField('VATID', $args['user']['vatID']);
            if (isset($args['user']['country'])) {
                $response = MemberExtension::checkVAT($args['user']['country'], $args['user']['vatID']);
                $zeroVAT = $response['status'] == 1;
            }
        }
        if (isset($args['user']['id'])) {
            $member = Member::get_by_id($args['user']['id']);
            $order->MemberID = $member?->ID ?? 0;
        }

        $order->write();

        $defaultCurrency = SiteConfig::get_one(SiteConfig::class)->DefaultCurrency;

        if (count($productSelection['fontIDs']) > 0) {
            $minPrice = 0;
            $maxPrice = 0;
            $totalWeights = DataList::create(Font::class)->filter(['ID' => $productSelection['fontIDs']])->count();
            foreach (GroupedList::create(DataList::create(Font::class)
                ->filter(['ID' => $productSelection['fontIDs']])
                ->sort(['FontFamily.FamilyName' => 'ASC', 'FontName' => 'ASC']))->groupBy('FontFamilyID') as $familyID => $items) {
                $family = FontFamily::get_by_id($familyID);
                if (!$family) {
                    continue;
                }
                // add as family
                if ($family->Fonts()->count() == $items->count()) {
                    $order->OrderItems()->add(FontFamilyOrderItem::create([
                        'Title' => $family->FamilyName,
                        'UnitPrice' => DBMoney::create()->setAmount($family->FontPrice->Amount * $items->count())->setCurrency($defaultCurrency),
                        'TotalPrice' => DBMoney::create()->setAmount($family->FontPrice->Amount * $items->count() * $coefficient->Coefficient)->setCurrency($defaultCurrency),
                        'PercentageVAT' => $family->percentageVAT($country, $zeroVAT),
                        'Quantity' => $coefficient->Coefficient,
                        'FontFamilyID' => $family->ID,
                        'License' => $license,
                        'Coefficient' => $coefficient,
                    ]));
                } else {
                    foreach ($items as $font) {
                        $order->OrderItems()->add(FontOrderItem::create([
                            'Title' => $family->Title . ' ' . $font->Title,
                            'UnitPrice' => DBMoney::create()->setAmount($family->FontPrice->Amount)->setCurrency($defaultCurrency),
                            'TotalPrice' => DBMoney::create()->setAmount($family->FontPrice->Amount * $coefficient->Coefficient)->setCurrency($defaultCurrency),
                            'PercentageVAT' => $family->percentageVAT($country, $zeroVAT),
                            'Quantity' => $coefficient->Coefficient,
                            'FontID' => $font->ID,
                            'License' => $license,
                            'Coefficient' => $coefficient,
                        ]));
                    }
                }
                if ($minPrice == 0) {
                    $minPrice = $family->FontPrice->Amount;
                } else {
                    $minPrice = min($minPrice, $family->FontPrice->Amount);
                }
                $maxPrice = max($maxPrice, $family->FontPrice->Amount);
            }

            $priceConfig = PriceConfig::current_price_config();
            //calculate volume discount
            $volumeDiscount = $priceConfig->getFontDiscount($totalWeights, $coefficient->Coefficient)->getAmount();
            if ($volumeDiscount > 0) {
                $order->OrderItems()->add(VolumeDiscountModifier::create([
                    'Title' => 'Volume Discount',
                    'UnitPrice' => DBMoney::create()->setAmount(-$volumeDiscount)->setCurrency($defaultCurrency),
                    'TotalPrice' => DBMoney::create()->setAmount(-$volumeDiscount)->setCurrency($defaultCurrency),
                    'Quantity' => 1,
                ]));
            }
        }

        $paymentClass = match ($args['paymentMethod']) {
            'creditCard', 'paypal' => StripePayment::class,
            'bankTransfer' => BankTransferPayment::class,
            default => null,
        };

        if ($paymentClass) {
            Payment::createOrderPayment($paymentClass, $order);
        }

        return $order;
    }
}
