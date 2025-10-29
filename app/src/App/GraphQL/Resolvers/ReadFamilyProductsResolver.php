<?php

namespace SLONline\App\GraphQL\Resolvers;

use GraphQL\Type\Definition\ResolveInfo;
use SilverStripe\Model\ArrayData;
use SilverStripe\Model\List\ArrayList;
use SilverStripe\ORM\DataList;
use SLONline\Elefont\Model\FontFamilyPackage;
use SLONline\Elefont\Model\FontFamilyPage;

/**
 * Read Family Products Query GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class ReadFamilyProductsResolver
{
    public static function resolve($obj, array $args, array $context, ResolveInfo $info)
    {
        /** @var \SLONline\Elefont\Model\FontFamilyPage $fontFamilyPage */
        $fontFamilyPage = DataList::create(FontFamilyPage::class)
            ->filter([
                'ID' => $args['familyPageId'],
            ])
            ->first();

        $family = $fontFamilyPage?->FontFamilyNull();

        $list = ArrayList::create();
        if ($family) {
            $list->push(ArrayData::create([
                '__typename' => 'FamilyProduct',
                'id' => $family->ID,
                'title' => $family->Title,
                'fontFamily' => $family,
                'fonts' => $family->Fonts(),
                'price' => $family->FontPrice
            ]));

            foreach ($family->FamilyPackages() as $item) {
                $list->push($item);
            }

            foreach ($family->Fonts() as $font) {
                $list->push(ArrayData::create([
                    '__typename' => 'FontProduct',
                    'id' => $font->ID,
                    'title' => $family->Title . ' ' . $font->Title,
                    'fontFamily' => $family,
                    'font' => $font,
                    'price' => $family->FontPrice
                ]));
            }
        }
        return $list;
    }

    public static function resolveFamilyProductResultType($object): string
    {
        if ($object instanceof ArrayData && $object->getField('__typename')) {
            return $object->getField('__typename');
        }

        if ($object instanceof FontFamilyPackage) {
            return 'FamilyPackageProduct';
        }

        return 'FontProduct';
    }
}
