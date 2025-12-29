<?php

namespace SLONline\App\Cli\Command;

use PDO;
use SilverStripe\Assets\Folder;
use SilverStripe\Assets\Storage\AssetStore;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Environment;
use SilverStripe\Dev\Debug;
use SilverStripe\ORM\Connect\MySQLDatabase;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DB;
use SilverStripe\ORM\Queries\SQLUpdate;
use SilverStripe\PolyExecution\PolyCommand;
use SilverStripe\PolyExecution\PolyOutput;
use SilverStripe\Security\Member;
use SLONline\AddressManagement\Extensions\MemberExtension;
use SLONline\AddressManagement\Model\Country;
use SLONline\AddressManagement\Model\State;
use SLONline\Commerce\Model\Discounts\DiscountCode;
use SLONline\Commerce\Model\Discounts\DiscountModifier;
use SLONline\Commerce\Model\Order;
use SLONline\Commerce\Model\OrderItem;
use SLONline\Commerce\Model\OrderStatus;
use SLONline\Commerce\Model\PDF\PDFInvoice;
use SLONline\Commerce\Model\ProductOrderItem;
use SLONline\Elefont\Model\Commerce\FontFamilyOrderItem;
use SLONline\Elefont\Model\Commerce\FontFamilyPackageOrderItem;
use SLONline\Elefont\Model\Commerce\FontOrderItem;
use SLONline\Elefont\Model\Font;
use SLONline\Elefont\Model\FontFamily;
use SLONline\Elefont\Model\Licenses\Coefficient;
use SLONline\Elefont\Model\Licenses\License;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Import from Old Site Sake Command
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
#[AsCommand(name: 'process:importoldsite', description: '<fg=blue>Import data from old site</>', hidden: false)]
class ImportFromOldSite extends PolyCommand
{
    use Configurable;

    protected static string $commandName = 'process:importoldsite';

    protected string $title = 'Import Old Site Data';

    protected static string $description = 'Import data from old site';

    // fixed typo in property name for clarity — config key used remains 'old_database'
    private static string $old_database = '';
    private static string $old_base_path = '';
    private PDO $oldSiteDBConnection;

    public function getOptions(): array
    {
        return [
            new InputOption('enable', null, InputOption::VALUE_REQUIRED, 'Enable the command by passing "true"'),
        ];
    }

    public function run(InputInterface $input, PolyOutput $output): int
    {
        Environment::increaseTimeLimitTo();
        Environment::setMemoryLimitMax(-1);
        Environment::increaseMemoryLimitTo(-1);
        if (!$input->getOption('enable')) {
            $output->writeln('<error>Command is disabled. To enable, pass the --enable=true option.</error>');
            return Command::INVALID;
        }

        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s',
            DB::getConfig()['server'],
            3306,
            self::config()->get('old_database'),
            MySQLDatabase::config()->get('charset'));
        try {
            $this->oldSiteDBConnection = new PDO($dsn, DB::getConfig()['username'], DB::getConfig()['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            $output->writeln('<info>Connected to old site database.</info>');
        } catch (\Throwable $e) {
            $output->writeln('<error>Failed to connect to old site DB: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }

        $this->generateMissingFullFontNames($output);
        $this->mapFamilies($output);

        // call import routines with output so progress can be shown
        $this->importLicenses($output);
        $this->importMembers($output);
        $this->importDiscounts($output);
        $this->importOrders($output);


        return Command::SUCCESS;
    }

    protected function generateMissingFullFontNames(OutputInterface $output): void
    {
        DataList::create(Font::class)
            ->filter('FullFontName', [null, ''])
            ->each(function (Font $font) use ($output) {
                $font->populateFullFontName($font->FontFamily()->FamilyName)->write();
                $output->writeln("<info>Generated FullFontName for Font ID {$font->ID}: {$font->FullFontName}</info>");
            });
    }

    protected function mapFamilies(OutputInterface $output): void
    {
        // count total rows first
        $total = (int)$this->oldSiteDBConnection
            ->query('SELECT COUNT(*) FROM `FontFamily` WHERE `FamilyName` IS NOT NULL')
            ->fetchColumn();

        if ($total === 0) {
            $output->writeln('<info>No families to import.</info>');
            return;
        }

        $output->writeln("<info>Importing Families: {$total} to process</info>");

        $query = $this->oldSiteDBConnection->prepare('SELECT * FROM `FontFamily` WHERE `FamilyName` IS NOT NULL');
        $query->execute();

        $progress = new ProgressBar($output, $total);
        $progress->setFormat(' %current%/%max% [%bar%] %percent:3s%% — %elapsed:6s%');
        $progress->start();

        $missingFamilies = [];

        $processed = 0;
        while ($row = $query->fetch()) {
            $family = DataList::create(FontFamily::class)
                ->filterAny([
                    'OldID' => $row['ID'],
                    'FamilyName' => $row['FamilyName'],
                ])
                ->first();
            if (!$family) {
                $missingFamilies[] = $row['FamilyName'];
                continue;
            }
            $family->OldID = $row['ID'];
            $family->write();

            $fontsQuery = $this->oldSiteDBConnection->prepare('SELECT * FROM `Font` WHERE `FontFamilyID` = ' . $row['ID']);
            $fontsQuery->execute();
            while ($fontRow = $fontsQuery->fetch()) {
                $oldFullFontName = $row['FamilyName'] . ' ' . trim(str_replace($row['FamilyName'], '', $fontRow['FontName']));
                $font = DataList::create(Font::class)
                    ->filter([
                        'OldID' => $fontRow['ID']
                    ])
                    ->first();
                $font = null;
                if (!$font) {
                    $font = DataList::create(Font::class)
                        ->filter([
                            'FontFamilyID' => $family->ID,
                            'FontName' => $fontRow['FontName'],
                        ])
                        ->first();
                }
                if (!$font) {
                    $font = DataList::create(Font::class)
                        ->filter([
                            'FullFontName:not' => null,
                            'FullFontName' => $oldFullFontName,
                        ])
                        ->first();
                }

                if (!$font) {
                    if (str_starts_with($fontRow['FontName'], 'BC Minim ')) {
                        $font = DataList::create(Font::class)
                            ->filter([
                                'FontFamily.FamilyName' => 'BC Minim',
                                'FontName' => str_replace('Regular Italic', 'Italic',
                                    str_replace('  ', ' ',
                                        trim(str_replace('BC Minim', '', $fontRow['FontName'])))),
                            ])->first();
                        if (str_contains($fontRow['FontName'], 'Inktrap')) {
                            $font = DataList::create(Font::class)
                                ->filter([
                                    'FontFamily.FamilyName' => 'BC Minim Inktrap',
                                    'FontName' => str_replace('Regular Italic', 'Italic',
                                        str_replace('  ', ' ',
                                            trim(str_replace(['BC Minim', 'Inktrap'], '', $fontRow['FontName'])))),
                                ])->first();
                        }
                    } elseif (str_starts_with($row['FamilyName'], 'BC Exalt')) {
                        if ($fontRow['FontName'] == 'Dogmatic') {
                            $font = DataList::create(Font::class)
                                ->filter([
                                    'FontFamily.FamilyName' => 'BC Exalt Dogmatic',
                                    'FontName' => 'Regular',
                                ])->first();
                        }
                        if ($fontRow['FontName'] == 'Prosaic') {
                            $font = DataList::create(Font::class)
                                ->filter([
                                    'FontFamily.FamilyName' => 'BC Exalt Prosaic',
                                    'FontName' => 'Regular',
                                ])->first();
                        }
                        if ($fontRow['FontName'] == 'Lyric') {
                            $font = DataList::create(Font::class)
                                ->filter([
                                    'FontFamily.FamilyName' => 'BC Exalt Lyric',
                                    'FontName' => 'Regular',
                                ])->first();
                        }
                    } elseif (str_starts_with($row['FamilyName'], 'BC Vafle')) {
                        if ($fontRow['FontName'] == 'Small Caps') {
                            $font = DataList::create(Font::class)
                                ->filter([
                                    'FontFamily.FamilyName' => 'BC Vafle',
                                    'FontName' => 'SmallCaps',
                                ])->first();
                        }
                    }
                }

                if (!$font) {
                    $missingFamilies[] = $row['FamilyName'] . ' - Style: ' . $fontRow['FontName'] . ' oldID: ' . $fontRow['ID'];
                    continue;
                }
                $font->OldID = $fontRow['ID'];
                $font->write();
            }

            $processed++;
            $progress->advance();
        }
        $progress->finish();
        $output->writeln('');
        $output->writeln("<info>Imported {$processed} families.</info>");

        if (count($missingFamilies)) {
            $output->writeln("<error>Missing:</error>");
            foreach ($missingFamilies as $familyName) {
                $output->writeln("<error> - " . $familyName . "</error>");
            }

            $output->writeln('');
            $output->writeln("<error>Import can not continue!</error>");
            exit;
        }
    }

    protected function importLicenses(OutputInterface $output): void
    {
        $total = (int)$this->oldSiteDBConnection
            ->query('SELECT COUNT(*) FROM `Licence`')
            ->fetchColumn();

        if ($total === 0) {
            $output->writeln('<info>No licenses to import.</info>');
            return;
        }

        $output->writeln("<info>Importing licenses: {$total} to process</info>");

        $query = $this->oldSiteDBConnection->prepare('SELECT * FROM `Licence`');
        $query->execute();

        $progress = new ProgressBar($output, $total);
        $progress->setFormat(' %current%/%max% [%bar%] %percent:3s%% — %elapsed:6s%');
        $progress->start();

        $processed = 0;
        while ($row = $query->fetch()) {
            /** @var License $license */
            $license = DataList::create(License::class)
                ->filterAny([
                    'OldID' => $row['ID'],
                    'Title' => $row['Name'],
                ])
                ->first();
            if (!$license) {
                $license = License::create();
            }
            $license->OldID = $row['ID'];
            $license->Title = $row['Name'];
            $license->Active = $row['Active'];

            $license->write();

            $oldCoefficientsQuery = null;
            $oldSecondCoefficientsQuery = null;
            switch ($row['ClassName']) {
                case 'PrintLicence':
                    $oldCoefficientsQuery = $this->oldSiteDBConnection->prepare('SELECT * FROM `PrintLicenceCoefficient` ORDER BY `SortOrder` ASC');
                    break;
                case 'WebLicence':
                    $oldCoefficientsQuery = $this->oldSiteDBConnection->prepare('SELECT * FROM `WebLicenceCoefficient` ORDER BY `SortOrder` ASC');
                    $oldSecondCoefficientsQuery = $this->oldSiteDBConnection->prepare('SELECT * FROM `WebPageViewLicenceCoefficient` ORDER BY `SortOrder` ASC');
                    break;
                case 'PrintWebLicence':
                    $oldCoefficientsQuery = $this->oldSiteDBConnection->prepare('SELECT * FROM `PrintWebLicenceCoefficient` ORDER BY `SortOrder` ASC');
                    $oldSecondCoefficientsQuery = $this->oldSiteDBConnection->prepare('SELECT * FROM `PrintWebPageViewLicenceCoefficient` ORDER BY `SortOrder` ASC');
                    break;
                case 'EbookLicence':
                    $oldCoefficientsQuery = $this->oldSiteDBConnection->prepare('SELECT * FROM `EbookLicenceCoefficient` ORDER BY `SortOrder` ASC');
                    break;
                case 'MobileLicence':
                    $oldCoefficientsQuery = $this->oldSiteDBConnection->prepare('SELECT * FROM `MobileLicenceCoefficient` ORDER BY `SortOrder` ASC');
                    break;
            }

            if ($oldCoefficientsQuery) {
                $oldCoefficientsQuery->execute();
                while ($oldCoefficientRow = $oldCoefficientsQuery->fetch()) {
                    /** @var Coefficient $licenseCoefficient */
                    $licenseCoefficient = $license->FirstParameterCoefficients()->filterAny([
                        'OldID' => $oldCoefficientRow['ID'],
                        'Coefficient' => $oldCoefficientRow['Coefficient'],
                    ])->first();
                    if (!$licenseCoefficient) {
                        $licenseCoefficient = Coefficient::create();
                    }
                    $licenseCoefficient->OldID = $oldCoefficientRow['ID'];
                    $licenseCoefficient->Quantity = $oldCoefficientRow['Quantity'];
                    $licenseCoefficient->Coefficient = $oldCoefficientRow['Coefficient'];
                    $licenseCoefficient->IsUnlimited = $oldCoefficientRow['IsUnlimited'];
                    $licenseCoefficient->UnlimitedTitle = $oldCoefficientRow['UnlimitedTitle'];
                    $licenseCoefficient->SortOrder = $oldCoefficientRow['SortOrder'];
                    $license->FirstParameterCoefficients()->add($licenseCoefficient);
                    $licenseCoefficient->write();
                }
            }

            if ($oldSecondCoefficientsQuery) {
                $oldSecondCoefficientsQuery->execute();
                while ($oldCoefficientRow = $oldSecondCoefficientsQuery->fetch()) {
                    /** @var Coefficient $licenseCoefficient */
                    $licenseCoefficient = $license->SecondParameterCoefficients()->filterAny([
                        'OldID' => $oldCoefficientRow['ID'],
                        'Coefficient' => $oldCoefficientRow['Coefficient'],
                    ])->first();
                    if (!$licenseCoefficient) {
                        $licenseCoefficient = Coefficient::create();
                    }
                    $licenseCoefficient->OldID = $oldCoefficientRow['ID'];
                    $licenseCoefficient->Quantity = $oldCoefficientRow['Quantity'];
                    $licenseCoefficient->Coefficient = $oldCoefficientRow['Coefficient'];
                    $licenseCoefficient->IsUnlimited = $oldCoefficientRow['IsUnlimited'];
                    $licenseCoefficient->UnlimitedTitle = $oldCoefficientRow['UnlimitedTitle'];
                    $licenseCoefficient->SortOrder = $oldCoefficientRow['SortOrder'];
                    $license->SecondParameterCoefficients()->add($licenseCoefficient);
                    $licenseCoefficient->write();

                }
            }

            $processed++;
            $progress->advance();
        }

        $progress->finish();
        $output->writeln('');
        $output->writeln("<info>Imported {$processed} licenses.</info>");
    }

    protected function importMembers(OutputInterface $output): void
    {
        // count total rows first
        $total = (int)$this->oldSiteDBConnection
            ->query('SELECT COUNT(*) FROM Member WHERE isDeleted = 0')
            ->fetchColumn();

        if ($total === 0) {
            $output->writeln('<info>No members to import.</info>');
            return;
        }

        $output->writeln("<info>Importing Members: {$total} to process</info>");

        $query = $this->oldSiteDBConnection->prepare('SELECT * FROM Member WHERE isDeleted = 0');
        $query->execute();

        $progress = new ProgressBar($output, $total);
        $progress->setFormat(' %current%/%max% [%bar%] %percent:3s%% — %elapsed:6s%');
        $progress->start();

        $processed = 0;
        while ($row = $query->fetch()) {
            /* @var Member|\SLONline\App\Extensions\Member|\SLONline\Commerce\Extensions\Member|MemberExtension $member */
            $member = Member::get()->filter('Email', $row['Email'])->first();
            if (!$member) {
                $member = Member::create();
                $member->Email = $row['Email'];
                $member->FirstName = $row['FirstName'];
                $member->Surname = $row['Surname'];
            }
            $member->Locale = $row['Locale'];
            $member->Organisation = $row['Organisation'];
            $member->Street = $row['Street'];
            $member->Street2 = $row['Street2'];
            $member->City = $row['City'];
            $member->ZIP = $row['ZIP'];
            $member->Phone = $row['Phone'];
            $member->CompanyID = $row['CompanyID'];
            $member->TaxID = $row['TaxID'];
            $member->VATID = $row['VATID'];
            $member->ValidVATID = $row['ValidVATID'];
            $member->OldID = $row['ID'];

            $member->CountryID = $this->findCountry($row['CountryID'] ?? 0)?->ID ?? 0;
            $member->StateID = $this->findState($row['StateID'] ?? 0, $member->CountryID)?->ID ?? 0;
            $member->write();

            SQLUpdate::create(DataObject::getSchema()->tableName(Member::class))
                ->addAssignments([
                    'Created' => $row['Created'],
                    'LastEdited' => $row['LastEdited'],
                    'Password' => $row['Password'],
                    'Salt' => $row['Salt'],
                    'PasswordEncryption' => $row['PasswordEncryption'],
                ])
                ->addWhere(['ID = ?' => $member->ID])
                ->execute();

            $processed++;
            $progress->advance();
        }

        $progress->finish();
        $output->writeln('');
        $output->writeln("<info>Imported {$processed} members.</info>");
    }

    protected function importOrders(OutputInterface $output): void
    {
        $total = (int)$this->oldSiteDBConnection
            ->query('SELECT COUNT(*) FROM `Order`')
            ->fetchColumn();

        if ($total === 0) {
            $output->writeln('<info>No orders to import.</info>');
            return;
        }

        $output->writeln("<info>Importing Orders: {$total} to process</info>");

        $query = $this->oldSiteDBConnection->prepare('SELECT * FROM `Order`');
        $query->execute();

        $progress = new ProgressBar($output, $total);
        $progress->setFormat(' %current%/%max% [%bar%] %percent:3s%% — %elapsed:6s%');
        $progress->start();

        $importedOrders = [];
        $numberOfIncorrectOrders = 0;
        $processed = 0;
        while ($row = $query->fetch()) {
            /** @var Order|\SLONline\Elefont\Extensions\Order $order */
            $order = DataList::create(Order::class)
                ->filter('OldID', $row['ID'])->first();
            if (!$order) {
                $order = Order::create();
                $order->OldID = $row['ID'];
            }
            $order->MemberID = DataList::create(Member::class)->filter('OldID', $row['UserID'])->first()?->ID ?? 0;
            $order->Email = $row['Email'];
            $order->InvoiceFirstName = $row['InvoiceFirstName'];
            $order->InvoiceSurname = $row['InvoiceSurname'];
            $order->InvoiceOrganisation = $row['InvoiceOrganisation'];
            $order->InvoiceStreet = $row['InvoiceStreet'];
            $order->InvoiceStreet2 = $row['InvoiceStreet2'];
            $order->InvoiceCity = $row['InvoiceCity'];
            $order->InvoiceZIP = $row['InvoiceZIP'];
            $order->InvoicePhone = $row['InvoicePhone'];

            $order->InvoiceCountryID = $this->findCountry($row['InvoiceCountryID'] ?? 0)?->ID ?? 0;
            $order->InvoiceStateID = $this->findState($row['InvoiceStateID'] ?? 0, $order->InvoiceCountryID)?->ID ?? 0;

            $order->CompanyID = $row['CompanyID'];
            $order->TaxID = $row['TaxID'];
            $order->VATID = $row['VATID'];
            $order->ValidVATID = $row['ValidVATID'];
            $order->Locale = $row['Locale'];
            $order->Currency = $row['Currency'];
            $order->VariableSymbol = $row['VariableSymbol'];
            $order->Note = $row['Note'];

            if (!empty($row['LicenceAddressID'])) {
                $oldLicenseAddress = $this->getOldAddress($row['LicenceAddressID'] ?? 0);
                if ($oldLicenseAddress) {
                    $order->LicenseFirstName = $oldLicenseAddress['FirstName'];
                    $order->LicenseSurname = $oldLicenseAddress['Surname'];
                    $order->LicenseOrganisation = $oldLicenseAddress['Organisation'];
                    $order->LicenseStreet = $oldLicenseAddress['Street'];
                    $order->LicenseStreet2 = $oldLicenseAddress['Street2'];
                    $order->LicenseCity = $oldLicenseAddress['City'];
                    $order->LicenseZIP = $oldLicenseAddress['ZIP'];
                    $order->LicensePhone = $oldLicenseAddress['Phone'];

                    $order->LicenseCountryID = $this->findCountry($oldLicenseAddress['CountryID'] ?? 0)?->ID ?? 0;
                    $order->LicenseStateID = $this->findState($oldLicenseAddress['StateID'] ?? 0, $order->LicenseCountryID)?->ID ?? 0;
                }
            }

            $lastStatus = $this->oldSiteDBConnection
                ->query('SELECT * FROM `Order_OrderStatuses` LEFT JOIN `OrderStatus` ON `OrderStatus`.`ID` = `Order_OrderStatuses`.`OrderStatusID` WHERE `OrderID` = ' . $order->OldID . ' ORDER BY `StatusCreated` DESC LIMIT 1')
                ->fetch();
            $orderStatus = DataList::create(OrderStatus::class)->filter(['Title' => $lastStatus['Title']])->first();
            if ($orderStatus && $orderStatus->exists()) {
                $order->StatusID = $orderStatus->ID;
            }
            $order->write();
            if ($orderStatus && $orderStatus->exists()) {
                $order->OrderStatuses()->add($orderStatus, ['StatusCreated' => $lastStatus['StatusCreated']]);
            }
            if (!empty($row['InvoiceID'])) {
                $oldInvoice = $this->oldSiteDBConnection
                    ->query('SELECT * FROM `PDFInvoice` LEFT JOIN `File` ON `PDFInvoice`.`ID` = `File`.`ID` WHERE `PDFInvoice`.`ID` = ' . $row['InvoiceID'])
                    ->fetch();

                $invoice = $order->Invoice();
                $folder = Folder::find_or_make(PDFInvoice::config()->get('folder_base'));
                $invoice
                    ->setField('DocumentNO', $oldInvoice['DocumentNO'])
                    ->setField('DueDate', $oldInvoice['DueDate'])
                    ->setField('DeliveryDate', $oldInvoice['DeliveryDate']);

                $content = file_get_contents(self::config()->get('old_base_path') . '/' . $oldInvoice['Filename']);

                $invoice->setFromString($content,
                    'Invoice' . $invoice->DocumentNO . '.pdf', null, null,
                    ['conflict' => AssetStore::CONFLICT_OVERWRITE]);
                $invoice
                    ->setField('Name', $oldInvoice['Name'])
                    ->setField('Title', 'Invoice' . $oldInvoice['DocumentNO']);
                $invoice->ParentID = $folder->ID;
                $invoice->write();
                $invoice->publishSingle();
                $order->InvoiceID = $invoice->ID;
                $order->write();
            }

            SQLUpdate::create(DataObject::getSchema()->tableName(Order::class))
                ->addAssignments([
                    'Created' => $row['Created'],
                    'LastEdited' => $row['LastEdited'],
                ])
                ->addWhere(['ID = ?' => $order->ID])
                ->execute();


            $this->importOrderItems($order);

            $importedOrders[$order->ID] = '<info>Order ID: ' . $order->ID . ' OldID: ' . $order->OldID . ' - Total with VAT: ' . $order->totalPriceWithVAT()->getAmount() . '</info>';
            $oldPayment = $this->oldSiteDBConnection
                ->query('SELECT * FROM `Payment` WHERE `OrderID` = ' . $order->OldID . ' ORDER BY `Created` DESC')
                ->fetch();
            /*if ($oldPayment && round($oldPayment['AmountAmount'], 3) != round($order->totalPriceWithVAT()->getAmount(), 3)) {
                $importedOrders[$order->ID] = '<error>Order ID: ' . $order->ID . ' OldID: ' . $order->OldID . ' - Total: ' . $order->totalPriceWithVAT()->getAmount() . ' vs Payment Amount: ' . ($oldPayment['AmountAmount'] ?? 'N/A') . '</error>';
                $numberOfIncorrectOrders++;
            } else {*/
                $queryItems = $this->oldSiteDBConnection->prepare('SELECT * FROM `OrderAttribute` WHERE `OrderID` = ' . $order->OldID);
                $queryItems->execute();
                $totalAmount = 0;
                while ($rowItem = $queryItems->fetch()) {
                    if ($rowItem['ClassName'] == 'DiscountModifier') {
                        $rowItem['TotalAmount'] = -$rowItem['TotalAmount'];
                    }
                    $totalAmount += $rowItem['TotalAmount'];
                }
                if (round($totalAmount, 3) != round($order->totalPrice()->getAmount(), 3)) {
                    $importedOrders[$order->ID] = '<error>Order ID: ' . $order->ID . ' OldID: ' . $order->OldID . ' - Total without VAT: ' . $order->totalPriceWithVAT()->getAmount() . ' vs OrderAttributes Total Amount: ' . $totalAmount . '</error>';
                    $numberOfIncorrectOrders++;
                }
            //}

            $processed++;
            $progress->advance();
            if ($processed == 100) {
                //break;
            }
        }

        $progress->finish();
        $output->writeln('');
        $output->writeln("<info>Imported {$processed} orders.</info>");

        if (count($importedOrders)) {
            $output->writeln("<error>Orders:</error>");
            foreach ($importedOrders as $info) {
                $output->writeln("<error> - " . $info . "</error>");
            }
        }

        $output->writeln("<info>Incorrect {$numberOfIncorrectOrders} vs " . count($importedOrders) . " orders.</info>");
    }

    protected function importOrderItems(Order $order): void
    {
        $query = $this->oldSiteDBConnection->prepare('SELECT
            `OrderAttribute`.*,
            `SerializedLicense`,
            `LicenseClassName`,
            `LicenseID`,
            `SerializedLicence`,
            `LicenceClassName`,
            `LicenceID`,
            `PageViews`,
            `UserLicenceFileID`,
            `Users`,
            `SerializedRelatedObject`
        FROM `OrderAttribute`
         LEFT JOIN `TypeOrderItem` ON `TypeOrderItem`.`ID` = `OrderAttribute`.`ID`
         LEFT JOIN `OrderModifier` ON `OrderModifier`.`ID` = `OrderAttribute`.`ID`
         WHERE `OrderID` = ' . $order->OldID);
        $query->execute();
        while ($row = $query->fetch()) {
            //Debug::dump($row);
            //UpgradeFamilyOrderItem
            //UpgradeFontOrderItem

            $orderItem = null;
            if ($row['ClassName'] == 'FontOrderItem' || $row['ClassName'] == 'UpgradeFontOrderItem') {
                /** @var Font $font */
                $font = $this->findFamilyStyleByOldProductID($row['ProductID']);
                if ($font) {
                    $license = DataList::create(License::class)
                        ->filter('OldID', $row['LicenceID'])
                        ->first();

                    /** @var FontOrderItem $orderItem */
                    $orderItem = DataList::create(FontOrderItem::class)
                        ->filter('OldID', $row['ID'])->first();
                    if (!$orderItem) {
                        $orderItem = $font->crateOrderItem([
                            [
                                'id' => $license->ID,
                                'firstParameterCoefficientID' => $license->FirstParameterCoefficients()->filter(['Quantity' => $row['Quantity']])->first()?->ID,
                                'secondParameterCoefficientID' => $license->SecondParameterCoefficients()->filter(['Quantity' => $row['PageViews'] ?? null])->first()?->ID,
                            ]
                        ]);
                        $orderItem->OldID = $row['ID'];
                    }
                    $orderItem->OrderID = $order->ID;
                    $orderItem->setQuantityForTotal($row['QuantityForTotal']);
                    $orderItem->UnitPrice->setAmount($row['UnitPriceAmount'])->setCurrency($row['UnitPriceCurrency']);
                    $orderItem->TotalPrice->setAmount($row['TotalAmount'])->setCurrency($row['TotalCurrency']);
                    if ($row['ClassName'] == 'UpgradeFontOrderItem') {
                        $orderItem->setQuantityForTotal($row['TotalAmount'] / ($row['UnitPriceAmount'] ?? 1));
                    }
                    $orderItem->write();

                    //echo($orderItem->debug());
                    //exit;
                }
            } elseif ($row['ClassName'] == 'FontFamilyOrderItem' || $row['ClassName'] == 'UpgradeFamilyOrderItem') {
                $family = $this->findFamilyByOldProductID($row['ProductID']);
                if ($family) {
                    $license = DataList::create(License::class)
                        ->filter('OldID', $row['LicenceID'])
                        ->first();
                    /** @var FontFamilyOrderItem|FontFamilyPackageOrderItem $orderItem */
                    $orderItem = DataList::create(ProductOrderItem::class)
                        ->filter('OldID', $row['ID'])
                        ->first();
                    if (!$orderItem) {
                        $orderItem = $family->crateOrderItem([
                            [
                                'id' => $license->ID,
                                'firstParameterCoefficientID' => $license->FirstParameterCoefficients()->filter(['Quantity' => $row['Quantity']])->first()?->ID,
                                'secondParameterCoefficientID' => $license->SecondParameterCoefficients()->filter(['Quantity' => $row['PageViews'] ?? null])->first()?->ID,
                            ]
                        ]);
                        $orderItem->OldID = $row['ID'];
                    }
                    $orderItem->OrderID = $order->ID;
                    $orderItem->setQuantityForTotal($row['QuantityForTotal']);
                    $orderItem->UnitPrice->setAmount($row['UnitPriceAmount'])->setCurrency($row['UnitPriceCurrency']);
                    $orderItem->TotalPrice->setAmount($row['TotalAmount'])->setCurrency($row['TotalCurrency']);
                    if ($row['ClassName'] == 'UpgradeFamilyOrderItem') {
                        $orderItem->setQuantityForTotal($row['TotalAmount'] / ($row['UnitPriceAmount'] ?? 1));
                    }
                    $orderItem->write();

                    //echo($orderItem->debug());
                    //exit;
                }
            } elseif ($row['ClassName'] == 'DiscountModifier') {
                //Debug::dump($row);exit;
                $discountCode = DataList::create(DiscountCode::class)
                    ->filter('OldID', $row['DiscountCodeID'])
                    ->first();
                /** @var DiscountModifier $orderItem */
                $orderItem = DataList::create(DiscountModifier::class)
                    ->filter('OldID', $row['ID'])->first();
                if (!$orderItem) {
                    $orderItem = $discountCode->createOrderModifier();
                    $orderItem->OldID = $row['ID'];
                }
                $orderItem->OrderID = $order->ID;
                $orderItem->setQuantityForTotal($row['QuantityForTotal']);
                $orderItem->UnitPrice->setAmount(-$row['UnitPriceAmount'])->setCurrency($row['UnitPriceCurrency']);
                $orderItem->TotalPrice->setAmount(-$row['TotalAmount'])->setCurrency($row['TotalCurrency']);

                $orderItem->write();
            }

            if ($orderItem && $orderItem->exists()) {
                SQLUpdate::create(DataObject::getSchema()->tableName(OrderItem::class))
                    ->addAssignments([
                        'Created' => $row['Created'],
                        'LastEdited' => $row['LastEdited'],
                    ])
                    ->addWhere(['ID = ?' => $orderItem->ID])
                    ->execute();

            }
        }
    }

    protected function importDiscounts(OutputInterface $output): void
    {
        $total = (int)$this->oldSiteDBConnection
            ->query('SELECT COUNT(*) FROM `DiscountCode`')
            ->fetchColumn();

        if ($total === 0) {
            $output->writeln('<info>No discount codes to import.</info>');
            return;
        }

        $output->writeln("<info>Importing Discount Codes: {$total} to process</info>");

        $query = $this->oldSiteDBConnection->prepare('SELECT * FROM `DiscountCode`');
        $query->execute();

        $progress = new ProgressBar($output, $total);
        $progress->setFormat(' %current%/%max% [%bar%] %percent:3s%% — %elapsed:6s%');
        $progress->start();

        $processed = 0;
        while ($row = $query->fetch()) {
            /** @var DiscountCode|\SLONline\Elefont\Extensions\DiscountCode $discountCode */
            $discountCode = DataList::create(DiscountCode::class)
                ->filter('OldID', $row['ID'])->first();
            if (!$discountCode) {
                $discountCode = DiscountCode::create();
                $discountCode->OldID = $row['ID'];
            }
            $discountCode->Code = $row['Code'];
            $discountCode->Name = $row['Name'];
            $discountCode->DiscountType = $row['DiscountType'];
            $discountCode->DiscountPercent = $row['DiscountPercent'];
            $discountCode->Amount->setAmount($row['AmountAmount']);
            $discountCode->Amount->setCurrency($row['AmountCurrency']);
            $discountCode->Active = $row['InUse'] == 'Active';
            $discountCode->ValidFrom = $row['WorksFrom'];
            $discountCode->ValidTo = $row['WorksTo'];
            $discountCode->UseOnce = $row['UseOneTime'];
            $discountCode->MinimalOrderSubtotalWithVAT->setAmount($row['MinimalOrderSubtotalWithVATAmount']);
            $discountCode->MinimalOrderSubtotalWithVAT->setCurrency($row['MinimalOrderSubtotalWithVATCurrency']);
            $discountCode->write();

            $forProductsQuery = $this->oldSiteDBConnection->prepare('SELECT `ProductID` FROM `DiscountCode_DiscountForProducts` WHERE `DiscountCodeID` = \'' . $row['ID'] . '\'');
            $forProductsQuery->execute();
            while ($forProductsRow = $forProductsQuery->fetch()) {
                $family = $this->findFamilyByOldProductID($forProductsRow['ProductID']);
                if ($family) {
                    $discountCode->DiscountForFamilies()->add($family);
                }

                $font = $this->findFamilyStyleByOldProductID($forProductsRow['ProductID']);
                if ($font) {
                    $discountCode->DiscountForFonts()->add($font);
                }
            }

            SQLUpdate::create(DataObject::getSchema()->tableName(DiscountCode::class))
                ->addAssignments([
                    'Created' => $row['Created'],
                    'LastEdited' => $row['LastEdited'],
                ])
                ->addWhere(['ID = ?' => $discountCode->ID])
                ->execute();

            $processed++;
            $progress->advance();
            //exit;
        }

        $progress->finish();
        $output->writeln('');
        $output->writeln("<info>Imported {$processed} discount codes.</info>");
    }

    protected function findCountry(int $oldID): ?Country
    {
        $oldCountry = $this->oldSiteDBConnection
            ->query('SELECT VATCode FROM FelixCountry WHERE ID = ' . $oldID)
            ->fetchColumn(0);
        if (!empty($oldCountry)) {
            $oldCountry = $oldCountry == 'EL' ? 'GR' : $oldCountry;
            return DataList::create(Country::class)
                ->filter('Code', $oldCountry)->first();
        }

        return null;
    }

    protected function findState(int $oldID, int $countryID): ?State
    {
        $oldState = $this->oldSiteDBConnection
            ->query('SELECT ShortCut FROM FelixState WHERE ID = ' . $oldID)
            ->fetchColumn(0);
        if (!empty($oldState)) {
            return DataList::create(State::class)
                ->filter('CountryID', $countryID)
                ->filter('Code', $oldState)->first();
        }

        return null;
    }

    protected function getOldAddress(int $oldID): ?array
    {
        return $this->oldSiteDBConnection
            ->query('SELECT * FROM FelixAddress WHERE ID = ' . $oldID)
            ->fetch();
    }

    protected function findFamilyByOldProductID(int $oldProductID): FontFamily|DataObject|null
    {
        // Novatica Cyrillic
        if ($oldProductID == 256) {
            $oldProductID = 197;
        }

        $result = $this->oldSiteDBConnection
            ->query('SELECT `FontFamilyID`, `Product`.`Name` FROM `FamilyProduct`
                LEFT JOIN `Product` ON `Product`.`ID` = `FamilyProduct`.`ID`
                WHERE `FamilyProduct`.`ID` = ' . $oldProductID)
            ->fetch();
        if (!empty($result['FontFamilyID'])) {
            if (str_ends_with(' family', $result['Name'])) {
                Debug::dump($result['Name']);
                exit;
            }


            $productFonts = $this->oldSiteDBConnection
                ->query('SELECT `FontID` FROM `FamilyProduct_Fonts` WHERE `FamilyProductID` = ' . $oldProductID)
                ->fetchAll(PDO::FETCH_COLUMN);

            $familyIDs = array_unique(DataList::create(Font::class)
                ->filter('OldID', $productFonts)
                ->column('FontFamilyID'));
            if (count($familyIDs) == 1) {
                return DataList::create(FontFamily::class)
                    ->filter('ID', array_shift($familyIDs))
                    ->first();
            } elseif(count($familyIDs) > 1) {
                Debug::dump('Family product ' . $oldProductID . ' has multiple familyIDs: ' . implode(', ', $familyIDs) . ' name: ' . $result['Name'] ?? '');
                exit;
            }

            return null;
        }

        return null;
    }

    protected function findFamilyStyleByOldProductID(int $oldProductID): Font|DataObject|null
    {
        $oldProductID = match ($oldProductID) {
            250 => 198, // Novatica Cyrillic Light
            251 => 199, // Novatica Cyrillic Light Italic
            252 => 200, // Novatica Cyrillic Regular
            253 => 201, // Novatica Cyrillic Regular Italic
            254 => 202, // Novatica Cyrillic Medium
            255 => 203, // Novatica Cyrillic Medium Italic
            257 => 204, // Novatica Cyrillic SemiBold
            259 => 205, // Novatica Cyrillic SemiBold Italic
            260 => 207, // Novatica Cyrillic Bold
            261 => 206, // Novatica Cyrillic Bold Italic

            default => $oldProductID,
        };

        $result = $this->oldSiteDBConnection
            ->query('SELECT `FontID` FROM `FontProduct` WHERE `ID` = ' . $oldProductID)
            ->fetch();
        if (!empty($result['FontID'])) {
            return DataList::create(Font::class)
                ->filter('OldID', $result['FontID'])
                ->first();
        }
        return null;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}
