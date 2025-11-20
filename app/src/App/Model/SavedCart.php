<?php

namespace SLONline\App\Model;

use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use SilverStripe\Control\Director;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\View\SSViewer;
use SLONline\App\GraphQL\Schemas\Enums\FamilyProductSelectionProductTypeSchema;
use SLONline\Commerce\Model\Config;
use SLONline\Commerce\Model\Order;
use SLONline\Elefont\Model\Font;
use SLONline\Elefont\Model\FontFamily;
use SLONline\Elefont\Model\FontFamilyPackage;
use SLONline\ORM\FieldType\DBJSONText;

/**
 * Save Cart Data Object
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 *
 * @property string $Hash
 * @property DBJSONText|string $CartData
 */
class SavedCart extends DataObject
{
    private static string $table_name = 'SavedCarts';
    private static string $singular_name = 'Saved cart';
    private static string $plural_name = 'Saved carts';
    private static array $db = [
        'Hash' => 'Varchar(64)',
        'CartData' => DBJSONText::class,
    ];

    private static array $indexes = [
        'Hash' => true,
    ];

    private static string $creator = '';// set in _config

    private static string $author = '';// set in _config

    private static string $title = '';// set in _config

    private static array $logo = [
        'url' => null,
        'posX' => null,
        'posY' => null,
        'width' => null,
        'height' => null
    ];

    private static string $footer_template = 'SLONline/App/SavedCartFooter';
    private static string $body_template = 'SLONline/App/SavedCartBody';

    private static array $font_dirs = [];
    private static array $font_data = [];

    private static string $default_font = '';

    public function getTitle(): string
    {
        return "Saved Cart #{$this->ID} ({$this->Hash})";
    }

    protected function onBeforeWrite(): void
    {
        parent::onBeforeWrite();
        if (empty($this->Hash)) {
            $this->Hash = bin2hex(random_bytes(32));
        }
    }

    public function downloadUrl()
    {
        return Director::absoluteURL('/savedcart/download/' . $this->Hash);
    }

    public function generatePDF(): ?string
    {
        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];
        foreach (self::config()->get('font_dirs') ?? [] as $dir) {
            $fontDirs[] = Director::baseFolder() . '/' . $dir;
        }

        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $pdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => self::config()->get('format'),
            'orientation' => self::config()->get('orientation'),
            'fontDir' => $fontDirs,
            'fontdata' => $fontData + (self::config()->get('font_data') ?? []),
            'default_font' => self::config()->get('default_font'),
            'tempDir' => sys_get_temp_dir()
        ]);

        $pdf->setCreator(self::config()->get('creator'));
        $pdf->setAuthor(self::config()->get('author'));
        $pdf->setTitle(self::config()->get('title'));

        $config = Config::inst();

        $order = $this->getOrder();
        $toPayMoney = $order->totalPriceWithVAT();
        $data = [
            'invoiceNumber' => $order->Invoice()->DocumentNO,
            'Organisation' => $config->Organisation ?? '',
            'Street' => $config->Street ?? '',
            'ZIP' => $config->ZIP ?? '',
            'City' => $config->City ?? '',
            'Country' => ($config->CountryID > 0 && is_object($config->Country())) ? $config->Country() : '',
            'CompanyID' => $config->CompanyID ?? '',
            'TaxID' => $config->TaxID ?? '',
            'VATID' => $config->VATID ?? '',
            'Payment' => $order->Payment()?->getTitle() ?? '',
            'IBAN' => $config->IBAN ?? '',
            'SWIFT' => $config->SWIFT ?? '',
            'BankName' => $config->BankName ?? '',
            'BankAccount' => $config->BankAccount ?? '',
            'BankCode' => $config->BankCode ?? '',
            'BankAddress' => $config->BankAddress ?? '',
            'toPayNice' => $toPayMoney->Nice(),
            'Order' => $order
        ];

        $logo = self::config()->get('logo');
        if (is_array($logo) && isset($logo['url'])) {
            $url = $logo['url'];
            $data['logo'] = DBHTMLText::create()
                ->setValue('<img src="' . $url . '" height="' . $logo['height'] . '" width="auto"/>');
        }

        $template = SSViewer::create(self::config()->get('body_template'));
        if ($template instanceof SSViewer) {
            $pdf->writeHTML($template->process($data));
        }

        $data = [
            'Organisation' => $config->Organisation ?? '',
            'Street' => $config->Street ?? '',
            'ZIP' => $config->ZIP ?? '',
            'City' => $config->City ?? '',
            'Email' => $config->Email ?? '',
            'Email_Title' => _t('Invoice.EMAIL', 'e-mail: '),
            'Phone' => $config->Phone ?? '',
            'Phone_Title' => _t('Invoice.PHONE', 'Phone: '),

            'ICO_Title' => _t('Invoice.ICO', 'Company ID: '),
            'ICO' => $config->CompanyID ?? '',

            'DIC_Title' => _t('Invoice.DIC', 'TAXID: '),
            'DIC' => $config->TaxID ?? '',

            'ICDPH_Title' => _t('Invoice.VATNUMBER', 'VAT Number: '),
            'ICDPH' => $config->VATID ?? '',

            'IBAN' => $config->IBAN ?? '',
            'SWIFT' => $config->SWIFT ?? ''
        ];

        $template = SSViewer::create(self::config()->get('footer_template'));
        if ($template instanceof SSViewer) {
            $pdf->SetHTMLFooter($template->process($data));
        }

        return $pdf->Output('', Destination::STRING_RETURN);
    }

    private function getOrder()
    {
        $args = json_decode($this->CartData, true);

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
        $order->Created = $this->Created;

        return $order;
    }
}
