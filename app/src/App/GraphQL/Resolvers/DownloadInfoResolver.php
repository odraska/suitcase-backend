<?php

namespace SLONline\App\GraphQL\Resolvers;

use GraphQL\Type\Definition\ResolveInfo;
use SilverStripe\ORM\DataList;
use SilverStripe\Security\Member;
use SilverStripe\Security\Security;
use SLONline\AddressManagement\Extensions\MemberExtension;
use SLONline\App\Model\DownloadInfo;
use Stripe\Exception\InvalidArgumentException;

/**
 * Download Info Resolver
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class DownloadInfoResolver
{
    public static function resolveReadDownloadInfo($obj, array $args, array $context, ResolveInfo $info)
    {
        return DataList::create(DownloadInfo::class)
            ->filter([
                'ID' => $args['id'],
                'Hash' => $args['hash'],
            ])
            ->first();
    }

    public static function resolveCreateDownloadInfo($obj, array $args, array $context, ResolveInfo $info)
    {
        /** @var Member|MemberExtension $member */
        $member = Security::getCurrentUser();
        if ($args['type'] == DownloadInfo::TYPE_FULL_TRIAL && (!$member || !$member->exists())) {
            throw new InvalidArgumentException('Not logged in', 101);
        }

        if (in_array($args['type'], [DownloadInfo::TYPE_BASIC_TRIAL, DownloadInfo::TYPE_FULL_TRIAL]) &&
            !empty($args['fontFamilyPageIDs']) && count($args['fontFamilyPageIDs']) > 0) {
            $downloadInfo = DataList::create(DownloadInfo::class)->filter([
                'Hash' => DownloadInfo::singleton()->calculateHash(
                    $args['type'],
                    $member?->ID ?? 0,
                    0,
                    $args['fontFamilyPageIDs'] ?? [],
                ),
                'Type' => $args['type'],
            ])->first();

            if (!$downloadInfo || !$downloadInfo->exists()) {
                $downloadInfo = DownloadInfo::create();
                $downloadInfo->Type = $args['type'];
                $downloadInfo->MemberID = $member?->ID ?? 0;
                $downloadInfo->OrderID = 0;
                $downloadInfo->FontFamilyPages()->setByIDList($args['fontFamilyPageIDs']);
            }

            $downloadInfo->Status = DownloadInfo::STATUS_PENDING;
            $downloadInfo->write();

            return $downloadInfo;
        }

        return null;
    }
}
