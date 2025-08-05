<?php

namespace SLONline\App\GraphQL\Resolvers;

use GraphQL\Type\Definition\ResolveInfo;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\FieldType\DBMoney;
use SilverStripe\ORM\GroupedList;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\View\ArrayData;
use SLONline\AddressManagement\Extensions\MemberExtension;
use SLONline\AddressManagement\Module\Country;
use SLONline\Adotbelow\PriceConfig\PriceConfig;
use SLONline\Commerce\Model\discounts\VolumeDiscountModifier;
use SLONline\Commerce\Model\Order;
use SLONline\Elefont\Model\Commerce\FontFamilyOrderItem;
use SLONline\Elefont\Model\Commerce\FontOrderItem;
use SLONline\Elefont\Model\Font;
use SLONline\Elefont\Model\FontFamily;
use SLONline\Elefont\Model\Licenses\Coefficient;
use SLONline\Elefont\Model\Licenses\License;


/**
 * Cart Summary Resolver
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2023, SLONline, s.r.o.
 */
class CartSummaryResolver
{
    public static function resolve($obj, array $args, array $context, ResolveInfo $info)
    {
        $zeroVAT = false;
        $country = null;
        if (isset($args['country'])) {
            $country = DataList::create(Country::class)->filter(['Code' => $args['country']])->first();

            if (isset($args['vatID'])) {
                $response = MemberExtension::checkVAT($args['country'], $args['vatID']);
                $zeroVAT = $response['status'] == 1;
            }
        }
        if (!$country) {
            $zeroVAT = true;
        }

        /** @var License $license */
        $license = License::get_by_id($args['licenseID']);
        /** @var Coefficient $coefficient */
        $coefficient = $license?->Coefficients()->filter(['ID' => $args['coefficientID']])->first();

        $order = Order::create();
        $defaultCurrency = SiteConfig::get_one(SiteConfig::class)->DefaultCurrency;

        if (count($args['fontIDs']) > 0) {
            $totalWeights = DataList::create(Font::class)->filter(['ID' => $args['fontIDs']])->count();
            foreach (GroupedList::create(DataList::create(Font::class)
                ->filter(['ID' => $args['fontIDs']])
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
                        ]));
                    }
                }
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

        return ArrayData::create([
            'license' => $license,
            'coefficient' => $coefficient,
            'items' => $order->Items(),
            'subTotalPrice' => $order->subTotalPrice(),
            'volumeDiscount' => $order->discountPrice(),
            'totalPrice' => $order->totalPrice(),
            'vatPrice' => $order->totalVATPrice(),
            'totalPriceWithVAT' => $order->totalPriceWithVAT(),
        ]);
    }
}
