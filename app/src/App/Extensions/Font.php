<?php

namespace SLONline\App\Extensions;

use SilverStripe\Core\Extension;
use SLONline\Elefont\Model\FontFile;

/**
 * Font Data Object Application Extension
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 *
 * @property \SLONline\Elefont\Model\Font $owner
 *
 * @property int $FullTrialFileID
 * @property int $FullTrialWoff2FileID
 * @property int $BasicTrialFileID
 * @property int $BasicTrialWoff2FileID
 * @method  FontFile FullTrialFile
 * @method FontFile FullTrialWoff2File
 * @method FontFile BasicTrialFile
 * @method FontFile BasicTrialWoff2File
 */
class Font extends Extension
{
    private static array $has_one = [
        'FullTrialFile' => FontFile::class,
        'FullTrialWoff2File' => FontFile::class,
        'BasicTrialFile' => FontFile::class,
        'BasicTrialWoff2File' => FontFile::class
    ];

    private static array $owns = [
        'FullTrialFile',
        'FullTrialWoff2File',
        'BasicTrialFile',
        'BasicTrialWoff2File'
    ];

    private static array $cascade_deletes = [
        'FullTrialFile',
        'FullTrialWoff2File',
        'BasicTrialFile',
        'BasicTrialWoff2File'
    ];
}
