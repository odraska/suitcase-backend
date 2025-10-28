<?php

namespace SLONline\App\GraphQL\Schemas\Enums;

use SilverStripe\GraphQL\Schema\Schema;
use SilverStripe\GraphQL\Schema\Type\Enum;
use SLONline\App\GraphQL\PartialSchemaUpdater;
use SLONline\Payment\OfflinePayments\BankTransferPayment;
use SLONline\Payment\StripeApplePayPayment;
use SLONline\Payment\StripeGooglePayPayment;
use SLONline\Payment\StripePayment;

/**
 * Payment Method Enum GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class PaymentMethodSchema implements PartialSchemaUpdater
{
    public static function updateSchema(Schema $schema): void
    {
        $schema->addEnum(Enum::create('PaymentMethod', [
            StripePayment::config()->get('code'),
            BankTransferPayment::config()->get('code'),
            StripeApplePayPayment::config()->get('code'),
            StripeGooglePayPayment::config()->get('code'),
        ]));
    }
}
