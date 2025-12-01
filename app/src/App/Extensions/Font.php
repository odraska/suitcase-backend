<?php

namespace SLONline\App\Extensions;

use SilverStripe\Assets\Storage\AssetStore;
use SilverStripe\Core\Extension;
use SilverStripe\Core\Path;
use SilverStripe\Forms\FieldList;
use SLONline\Elefont\Model\FontFile;
use SLONline\Elefont\Tools\FontTools;
use SLONline\Elefont\Tools\WebFontsWOFF2Converter;

/**
 * Font Data Object Application Extension
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 *
 * @property \SLONline\Elefont\Model\Font|Font $owner
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

    public function onBeforeWrite()
    {
        if ($this->owner->File()->exists() && ($this->owner->isChanged('FileID') ||
            !$this->owner->FullTrialFile()->exists() ||
            !$this->owner->BasicTrialFile()->exists())) {
            $this->owner->generateFullTrialFile();
            $this->owner->generateFullTrialWoff2File();
            $this->owner->generateBasicTrialFile();
            $this->owner->generateBasicTrialWoff2File();
        }
    }

    public function onAfterWrite()
    {
        if ($this->owner->FullTrialFile()->exists()) {
            $this->owner->FullTrialFile()->publishSingle();
        }
        if ($this->owner->BasicTrialFile()->exists()) {
            $this->owner->BasicTrialFile()->publishSingle();
        }
        if ($this->owner->FullTrialWoff2File()->exists()) {
            $this->owner->FullTrialWoff2File()->publishSingle();
        }
        if ($this->owner->BasicTrialWoff2File()->exists()) {
            $this->owner->BasicTrialWoff2File()->publishSingle();
        }
    }

    public function updateCMSFields(FieldList $fields)
    {
        $fileUploadField = $fields->fieldByName('Root.Main.FullTrialFile');
        $fileUploadField?->setFolderName(\SLONline\Elefont\Model\Font::folder());
        $fileUploadField?->getUpload()->setReplaceFile(true);

        $fileUploadField = $fields->fieldByName('Root.Main.FullTrialWoff2File');
        $fileUploadField?->setFolderName(\SLONline\Elefont\Model\Font::folder());
        $fileUploadField?->getUpload()->setReplaceFile(true);

        $fileUploadField = $fields->fieldByName('Root.Main.BasicTrialFile');
        $fileUploadField?->setFolderName(\SLONline\Elefont\Model\Font::folder());
        $fileUploadField?->getUpload()->setReplaceFile(true);

        $fileUploadField = $fields->fieldByName('Root.Main.BasicTrialWoff2File');
        $fileUploadField?->setFolderName(\SLONline\Elefont\Model\Font::folder());
        $fileUploadField?->getUpload()->setReplaceFile(true);
    }

    public function generateFullTrialWoff2File(): void
    {
        $woff2Converter = WebFontsWOFF2Converter::create()->setSourceFromFileObject($this->owner->FullTrialFile())->process();
        if ($woff2Converter->isSuccess()) {
            $fileInfo = \pathinfo($this->owner->FullTrialFile()->Filename);
            $woff2File = $this->owner->FullTrialWoff2File();
            $woff2File->setFromString($woff2Converter->getSource(),
                Path::join($fileInfo['dirname'], $fileInfo['filename'] . '.woff2'));
            $woff2File->write();
            $this->owner->FullTrialWoff2FileID = $woff2File->ID;
        }
    }

    public function generateBasicTrialWoff2File(): void
    {
        $woff2Converter = WebFontsWOFF2Converter::create()->setSourceFromFileObject($this->owner->BasicTrialFile())->process();
        if ($woff2Converter->isSuccess()) {
            $fileInfo = \pathinfo($this->owner->BasicTrialFile()->Filename);
            $woff2File = $this->owner->BasicTrialWoff2File();
            $woff2File->setFromString($woff2Converter->getSource(),
                Path::join($fileInfo['dirname'], $fileInfo['filename'] . '.woff2'));
            $woff2File->write();
            $this->owner->BasicTrialWoff2FileID = $woff2File->ID;
        }
    }

    public function generateFullTrialFile(): void
    {
        $fontData = FontTools::create()
            ->setSourceFromFileObject($this->owner->File())
            ->getFontInfo();

        $fontContent = FontTools::create()
            ->setSourceFromFileObject($this->owner->File())
            ->generateCustomisedFont($fontData['familyName'] . ' Trial', $fontData['styleName']);

        $fontFile = $this->owner->FullTrialFile();
        $fileInfo  = \pathinfo($this->owner->File()->getFilename());

        $fontFile->setFromString($fontContent, Path::join(\SLONline\Elefont\Model\Font::folder(),
            $fileInfo['filename'] . '_Trial.' . $fileInfo['extension']),
            null, null,
            ['conflict' => AssetStore::CONFLICT_OVERWRITE, 'visibility' => AssetStore::VISIBILITY_PUBLIC]);
        $fontFile->write();

        $this->owner->FullTrialFileID = $fontFile->ID;
    }

    public function generateBasicTrialFile(): void
    {
        $chars = array_merge(
            array_map(fn($v): string => chr($v),range(ord('a'), ord('z'))),
            array_map(fn($v): string => chr($v),range(ord('A'), ord('Z'))),
            array_map(fn($v): string => chr($v),range(ord('0'), ord('9'))),
            [' ', '.', ',', '-', '*']
        );

        $fontData = FontTools::create()
            ->setSourceFromFileObject($this->owner->File())
            ->getFontInfo();

        $fontContent = FontTools::create()
            ->setSourceFromFileObject($this->owner->File())
            ->generateCustomisedFont(
                $fontData['familyName'] . ' Basic Trial',
                $fontData['styleName'],
                $chars,
                [],
            );

        $fontFile = $this->owner->BasicTrialFile();
        $fileInfo  = \pathinfo($this->owner->File()->getFilename());

        $fontFile->setFromString($fontContent, Path::join(\SLONline\Elefont\Model\Font::folder(),
            $fileInfo['filename'] . '_BasicTrial.' . $fileInfo['extension']),
            null, null,
            ['conflict' => AssetStore::CONFLICT_OVERWRITE, 'visibility' => AssetStore::VISIBILITY_PUBLIC]);
        $fontFile->write();

        $this->owner->BasicTrialFileID = $fontFile->ID;
    }
}
