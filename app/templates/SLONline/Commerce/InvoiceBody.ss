<style>
    .border {
        border-bottom: 1px solid black;
    }

    .items {
        padding: 10px 0;
    }

    .items-row-product {
        padding: 7px;
        text-align: left;
        vertical-align: top;
        font-size: 11px;
        width: 92%;
    }

    .items-row-price {
        padding: 7px;
        text-align: right;
        vertical-align: top;
        font-size: 11px;
    }

    .header1 {
        width: 50%;
        font-size: 11px;
        font-weight: bold;
        text-align: left;
        padding: 10px 0;
    }

    .header2 {
        font-size: 11px;
        font-weight: normal;
        text-align: left;
        padding: 10px 0;
    }
</style>
<div class="border">
    <div style="float: left; width: 50%">
        <table style="width: 100%; margin:0" cellpadding="0" cellspacing="0">
            <tr>
                <td class="header1 border"><%t Invoice.InvoiceNo 'Invoice No.' %>:</td>
                <td class="header1 border">$invoiceNumber</td>
            </tr>
            <tr>
                <td class="header1 border"><%t Invoice.Supplier 'Supplier' %>:</td>
                <td class="header2 border">
                    {$Organisation}<br>
                    {$Street}<br>
                    {$ZIP} {$City}<br>
                    {$Country.Title}
                </td>
            </tr>
            <tr>
                <td class="header1 border"><%t Invoice.VariableSymbol 'Variable symbol' %>:</td>
                <td class="header2 border">{$Order.VariableSymbol}</td>
            </tr>
            <tr>
                <td class="header1 border"><%t Invoice.IBAN 'IBAN' %>:</td>
                <td class="header2 border">{$IBAN}</td>
            </tr>
            <tr>
                <td class="header1 border"><%t Invoice.SWIFT 'SWIFT' %>:</td>
                <td class="header2 border">{$SWIFT}</td>
            </tr>
            <tr>
                <td class="header1 border"><%t Invoice.BankAccount 'Bank account' %>:</td>
                <td class="header2 border">{$BankAccount}/{$BankCode}</td>
            </tr>
            <tr>
                <td class="header1 border"><%t Invoice.Bank 'Bank' %>:</td>
                <td class="header2 border">
                    {$BankName}, {$BankAddress}
                </td>
            </tr>
            <tr>
                <td class="header1 border"><%t Invoice.CompanyID 'ID No.' %>:</td>
                <td class="header2 border">{$CompanyID}</td>
            </tr>
            <tr>
                <td class="header1 border"><%t Invoice.VATID 'VAT No.' %>:</td>
                <td class="header2 border">{$VATID}</td>
            </tr>
        </table>
        <table style="width: 100%; margin:0" cellpadding="0" cellspacing="0">
            <tr>
                <td class="header1 border"><%t Invoice.BillingAddress 'Billing address' %>:</td>
                <td class="header2 border">
                    <% if $Order.InvoiceOrganisation %>
                        {$Order.InvoiceOrganisation}<br>
                    <% else %>
                        {$Order.InvoiceFirstName} {$Order.InvoiceSurname}<br>
                    <% end_if %>
                    {$Order.InvoiceStreet}<br>
                    <% if $Order.InvoiceStreet2 %>
                        {$Order.InvoiceStreet2}<br>
                    <% end_if %>
                    {$Order.InvoiceZIP}, {$Order.InvoiceCity}
                    <% if $Order.InvoiceCountryID %>
                        <br>{$Order.InvoiceCountry.Title}
                    <% end_if %>
                </td>
            </tr>
            <% if $Order.LicenseFirstName || $Order.LicenseSurname || $Order.LicenseOrganisation %>
                <tr>
                    <td class="header1 border"><%t Invoice.LicenseAddress 'Licence address' %>:</td>
                    <td class="header2 border">
                        <% if $Order.LicenseOrganisation %>
                            {$Order.LicenseOrganisation}<br>
                        <% else %>
                            {$Order.LicenseFirstName} {$Order.LicenseSurname}<br>
                        <% end_if %>
                        {$Order.LicenseStreet}<br>
                        <% if $Order.LicenseStreet2 %>
                            {$Order.LicenseStreet2}<br>
                        <% end_if %>
                        {$Order.LicenseZIP}, {$Order.LicenseCity}
                        <% if $Order.LicenseCountryID %>
                            <br>{$Order.LicenseCountry.Title}
                        <% end_if %>
                    </td>
                </tr>
            <% end_if %>
            <% if $Order.CompanyID %>
                <tr>
                    <td class="header1 border"><%t Invoice.CompanyID 'ID No.' %>:</td>
                    <td class="header2 border">{$Order.CompanyID}</td>
                </tr>
            <% end_if %>
            <tr>
                <td class="header1 border"><%t Invoice.VATID 'VAT No.' %>:</td>
                <td class="header2 border"><% if $Order.ValidVATID %>{$Order.VATID}<% else %>$Order.TaxID<% end_if %></td>
            </tr>
            <tr>
                <td class="header1 border"><%t Invoice.DateOfIssue 'Date of issue' %>:</td>
                <td class="header2 border">$Order.Created.format('dd/MM/y')</td>
            </tr>
            <tr>
                <td class="header1"><%t Invoice.DueDate 'Due date' %>:</td>
                <td class="header2">$Order.Invoice.DueDate.format('dd/MM/y')</td>
            </tr>
        </table>
    </div>
    <div style="float: right; width: 50%; text-align: right;">
        $logo
    </div>
</div>

<div class="border items">
    <div style="float: left; width: 163px;" class="header1"><%t Invoice.Items 'Items' %>:</div>
    <div style="float: left; width: auto;">
        <table style="width: 100%;" cellpadding="0" cellspacing="0">
            <tbody>
            <% loop $Order.Items %>
                <tr>
                    <td class="items-row-product">
                        <b>{$Title}</b><br>
                        $QuantityNice
                    </td>
                    <td class="items-row-price">{$totalWithVAT.Nice}</td>
                </tr>
            <% end_loop %>
            </tbody>
        </table>
    </div>
</div>
<div class="items">
    <div style="float: left; width: 163px;">&nbsp;</div>
    <div style="float: left; width: auto;">
        <table style="width: 100%;" cellpadding="0" cellspacing="0">
            <tfoot>
            <% if $Order.discountPriceWithVAT.Amount != 0 %>
                <tr>
                    <td class="items-row-product"><b><%t Invoice.Discount 'Discount' %></b></td>
                    <td class="items-row-price">{$Order.discountPriceWithVAT.Nice}</td>
                </tr>
            <% end_if %>
            <tr>
                <td class="items-row-product border"><b><%t Invoice.Total 'Total' %></b></td>
                <td class="items-row-price border">{$Order.totalPriceWithVAT.Nice}</td>
            </tr>
            <% if $Order.Currency != 'EUR' %>
                <tr>
                    <td class="items-row-product"><%t Invoice.InfoPrice 'Price in ' %>$Order.Currency
                        (<%t Invoice.ConversionRate 'Conversion rate' %>: {$Order.totalPriceWithVAT.getCurrencyRate($Order.Currency, $Order.Invoice.DueDate)})
                    </td>
                    <td class="items-row-price">{$Order.totalPriceWithVAT.getInCurrency($Order.Currency, $Order.Invoice.DueDate).Nice}</td>
                </tr>
            <% end_if %>
            <% if $Order.isPaid %>
                <tr>
                    <td class="items-row-product"
                        colspan="2"><%t Invoice.PaidInFull 'Paid in full. Thanks for your order.' %></td>
                </tr>
            <% end_if %>
            </tfoot>
        </table>
    </div>
</div>
