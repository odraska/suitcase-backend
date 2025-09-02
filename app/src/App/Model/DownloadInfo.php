<?php

namespace SLONline\App\Model;

use SilverStripe\Assets\File;
use SilverStripe\Assets\Folder;
use SilverStripe\Assets\Storage\AssetStore;
use SilverStripe\Control\Director;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\ManyManyList;
use SilverStripe\Security\Member;
use SLONline\Commerce\Model\Order;
use SLONline\Elefont\Model\FontFamilyPage;
use ZipArchive;

/**
 * Download Info Data Object
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2024, SLONline, s.r.o.
 *
 * @property string Status
 * @property string Type
 * @property string Hash
 * @property int MemberID
 * @property int FileID
 * @property int OrderID
 *
 * @method Member Member()
 * @method File File()
 * @method Order Order()
 * @method ManyManyList|FontFamilyPage FontFamilyPages()
 */
class DownloadInfo extends DataObject
{
    const string STATUS_PENDING = 'Pending';
    const string STATUS_GENERATING = 'Generating';
    const string STATUS_COMPLETED = 'Completed';
    const string STATUS_FAILED = 'Failed';

    const string TYPE_BASIC_TRIAL = 'BasicTrial';
    const string TYPE_FULL_TRIAL = 'FullTrial';

    const string TYPE_FULL = 'Full';
    const string TYPE_DESKTOP = 'Desktop';
    const string TYPE_WEBFONT = 'Webfont';

    const string FOLDER_NAME = 'DownloadInfo';

    private static string $table_name = 'DownloadInfo';

    private static string $singular_name = 'Download Info';

    private static string $plural_name = 'Download Info';

    private static array $db = [
        'Status' => 'Enum("' .
            self::STATUS_PENDING . ',' .
            self::STATUS_GENERATING . ',' .
            self::STATUS_COMPLETED . ',' .
            self::STATUS_FAILED . '", "' . self::STATUS_PENDING . '")',
        'Type' => 'Enum("' .
            self::TYPE_BASIC_TRIAL . ',' .
            self::TYPE_FULL_TRIAL . ',' .
            self::TYPE_FULL . ',' .
            self::TYPE_DESKTOP . ',' .
            self::TYPE_WEBFONT . ',","")',
        'Hash' => 'Varchar(1024)',
    ];

    private static array $has_one = [
        'Member' => Member::class,
        'File' => File::class,
        'Order' => Order::class,
    ];

    private static array $many_many = [
        'FontFamilyPages' => FontFamilyPage::class,
    ];

    protected function onBeforeWrite(): void
    {
        parent::onBeforeWrite();

        if (!$this->Hash) {
            $this->Hash = $this->calculateHash($this->Type,
                $this->MemberID,
                $this->OrderID,
                $this->FontFamilyPages()->getIDList(),
            );
        }
    }

    public function calculateHash(string $type, int $memberID = 0, int $orderID = 0, array $fontFamilyPagesIDs = []): string
    {
        sort($fontFamilyPagesIDs);
        return sha1($type . '-' . $memberID . '-' . $orderID . '-' . implode(',', $fontFamilyPagesIDs));
    }

    protected function onAfterWrite(): void
    {
        parent::onAfterWrite();

        if ($this->Status == self::STATUS_PENDING) {
            $this->runBackgroundProcess();
        }
    }

    private function runBackgroundProcess()
    {
        $command = PHP_BINDIR . '/php -f ' . Director::baseFolder() . '/vendor/silverstripe/framework/bin/sake process:downloadinfo ' . $this->ID . ' > /dev/null 2>&1 &';
        exec($command);
    }

    public function process(): void
    {
        if ($this->Status == self::STATUS_GENERATING) {
            //return;
        }

        if ($this->LastEdited && strtotime($this->LastEdited) >= strtotime('-24hours') &&
            $this->Status == self::STATUS_PENDING &&
            $this->File()->exists() &&
            strtotime($this->File()->LastEdited) >= strtotime('-24hours')) {
            $this->Status = self::STATUS_COMPLETED;
            $this->write();
            //return;
        }

        $this->Status = self::STATUS_GENERATING;
        $this->write();

        if ($this->FontFamilyPages()->count() > 0) {
            $this->Status = $this->fontFamilyPagesPackage() ? self::STATUS_COMPLETED : self::STATUS_FAILED;
        } else {
            $this->Status = self::STATUS_FAILED;
        }

        $this->write();
    }

    private function fontFamilyPagesPackage(): bool
    {
        if ($this->FontFamilyPages()->count() == 0) {
            return false;
        }

        $zipTmpFile = tempnam(sys_get_temp_dir(), 'FontsZip');
        $zipArchive = new ZipArchive();
        if ($zipArchive->open($zipTmpFile, ZipArchive::OVERWRITE) !== true) {
            unlink($zipTmpFile);
            return false;
        }

        /* @var FontFamilyPage $fontFamilyPage */
        foreach ($this->FontFamilyPages() as $fontFamilyPage) {
            $fontFamily = $fontFamilyPage->FontFamilyNull();
            foreach ($fontFamily?->Fonts() ?? [] as $font) {
                $file = null;
                if ($this->Type == self::TYPE_BASIC_TRIAL) {
                    $file = $font->BasicTrialFile();
                } elseif ($this->Type == self::TYPE_FULL_TRIAL) {
                    $file = $font->FullTrialFile();
                }

                if ($file && $file->exists()) {
                    $file_info = pathinfo($file->getFilename());
                    $zipArchive->addFromString($file_info['basename'], $file->getString());
                }
            }
        }

        $zipArchive->close();

        if (\file_exists($zipTmpFile)) {
            $folder = Folder::find_or_make(self::FOLDER_NAME);

            $file = $this->File();
            $file->setFromLocalFile($zipTmpFile, $folder->getFilename() . 'fonts-' . sha1($this->ID . '-' . implode(',', $this->FontFamilyPages()->sort('ID', 'ASC')->getIDList()) . ':' . $zipTmpFile) . '.zip',
                null, null, [
                    'conflict' => AssetStore::CONFLICT_OVERWRITE,
                    'visibility' => AssetStore::VISIBILITY_PUBLIC
                ]);

            $file->ParentID = $folder->ID;
            $file->write();
            $this->FileID = $file->ID;
            $this->write();
            $this->File()->publishSingle();

            // remove temp file
            unlink($zipTmpFile);
            return true;
        }

        return false;
    }
}
