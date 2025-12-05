<?php

namespace SLONline\App\GraphQL\Resolvers;

use GraphQL\Type\Definition\ResolveInfo;
use SilverStripe\Model\ArrayData;
use SilverStripe\Model\List\ArrayList;
use SilverStripe\ORM\DataList;
use SLONline\Elefont\Model\FontFamily;
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
        $list = ArrayList::create();

        /** @var \SLONline\Elefont\Model\FontFamilyPage $fontFamilyPage */
        $fontFamilyPage = DataList::create(FontFamilyPage::class)
            ->filter([
                'ID' => $args['familyPageId'],
            ])
            ->first();

        if (!$fontFamilyPage) {
            return $list;
        }

        if ($fontFamilyPage->FontFamilies()->count() > 0) {
            $families = $fontFamilyPage->FontFamilies();
        } else {
            $families = DataList::create(FontFamily::class)
                ->filter([
                    'ID' => $fontFamilyPage->ListDefaultFont()->FontFamily()->ID
                ]);
        }

        $familyProducts = ArrayList::create();
        $fontProducts = ArrayList::create();
        foreach ($families as $family) {
            foreach ($family->FamilyPackages()->filter(['Status' => true]) as $item) {
                if ($list->find('ID', $item->ID)) {
                    continue;
                }
                $list->push($item);
            }

            $familyProducts->push(ArrayData::create([
                '__typename' => 'FamilyProduct',
                'id' => $family->ID,
                'title' => $family->Title,
                'fontFamily' => $family,
                'fonts' => $family->Fonts(),
                'price' => $family->FontPrice
            ]));

            foreach ($family->Fonts()->filter(['Status' => true]) as $font) {
                $fontProducts->push(ArrayData::create([
                    '__typename' => 'FontProduct',
                    'id' => $font->ID,
                    'title' => $family->Title . ' ' . $font->Title,
                    'fontFamily' => $family,
                    'font' => $font,
                    'price' => $font->getPrice()
                ]));
            }
        }

        $list->merge($familyProducts);
        $list->merge($fontProducts);

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
