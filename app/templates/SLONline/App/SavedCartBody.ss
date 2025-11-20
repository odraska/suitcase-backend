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
        width: 88%;
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
                <td class="header1 border"><%t Quote.Title 'Quote' %></td>
                <td class="header1 border"></td>
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
                <td class="header1"><%t Invoice.DateOfIssue 'Date of issue' %>:</td>
                <td class="header2">$Order.Created.format('dd/MM/y')</td>
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

            <% if $Order.InvoiceCountry.VATCode == 'CZ' %>
                <tr>
                    <td class="items-row-product"><%t Invoice.InfoCZKPrice 'Price in CZK' %>
                        (<%t Invoice.ConversionRate 'Conversion rate' %>
                        : {$Order.totalPriceWithVAT.getCurrencyRate('CZK', $Order.Invoice.DueDate)})
                    </td>
                    <td class="items-row-price">{$Order.totalPriceWithVAT.getInCurrency('CZK', $Order.Invoice.DueDate).Nice}</td>
                </tr>
            <% end_if %>
            </tfoot>
        </table>
    </div>
</div>
