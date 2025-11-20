<?php

namespace SLONline\App\GraphQL\Resolvers;

use GraphQL\Type\Definition\ResolveInfo;
use InvalidArgumentException;
use SLONline\App\GraphQL\CaptchaBotID;
use SLONline\App\GraphQL\Schemas\Enums\FamilyProductSelectionProductTypeSchema;
use SLONline\App\Model\SavedCart;
use SLONline\Commerce\Model\Order;
use SLONline\Elefont\Model\Font;
use SLONline\Elefont\Model\FontFamily;
use SLONline\Elefont\Model\FontFamilyPackage;


/**
 * Read Cart Resolver
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2023, SLONline, s.r.o.
 */
class CartResolver
{
    public static function resolveReadCart($obj, array $args, array $context, ResolveInfo $info): Order
    {
        $order = Order::create();
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
                    throw new InvalidArgumentException('Unknown product type ' . $selection['productType']);
            }

            if (!$product) {
                continue;
            }

            $order->OrderItems()->add($product->crateOrderItem($selection['licenses']));
        }

        if ($args['discountCode']) {
            $order->applyDiscountCode($args['discountCode']);
        }

        return $order;
    }

    public static function resolveSaveCart($obj, array $args, array $context, ResolveInfo $info): string
    {
        if (!CaptchaBotID::singleton()->validateToken($args['captchaToken'])) {
            throw new InvalidArgumentException('Invalid Captcha token', 110);
        }

        //only for validation
        static::resolveReadCart($obj, $args, $context, $info);

        $savedCart = SavedCart::create();
        $savedCart->CartData = ($args);
        $savedCart->write();

        return $savedCart->Hash;
    }

    public static function resolveLoadCart($obj, array $args, array $context, ResolveInfo $info): array
    {
        $savedCart = SavedCart::get()->filter('Hash', $args['hash'])->first();
        if (!$savedCart) {
            throw new InvalidArgumentException('Saved cart not found', 404);
        }

        return array_merge([
            'downloadUrl' => $savedCart->downloadUrl(),
            'discountCode' => null,
            'familyProductSelections' => []
        ], $savedCart->dbObject('CartData')->getValue() ?? []);
    }
}
