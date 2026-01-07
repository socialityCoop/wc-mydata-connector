# MyData Connector for WooCommerce
A plugin that integrates PDF Invoices &amp; Packing Slips for WooCommerce with myData greek tax system.

## Usage

This plugin can be used to provide retail receipts (B2C) 11_1 and 11_2 through the MyData greek tax system. Receipt are created in your eshop and can be automatically transmitted to AADE through MyData API. No third-party API is needed and no charges are applied. Each receipt gets a ΜΑΡΚ number and QR code as legally required. Receipts are created through the PDF Invoices &amp; Packing Slips for WooCommerce and are available in the WC orders Panel. They can be also be sent automatically to clients.

No invoices (B2B) can be transmitted, since AADE demands a third party provider to be involved.

The plugin is based on the PHP library [Invoice Maker](https://docs.invoicemaker.gr/). Cudos for their amazing work!

## Requirements

To use this plugin you need [PDF Invoices &amp; Packing Slips](https://wordpress.org/plugins/woocommerce-pdf-invoices-packing-slips/) and thus [Woocommerce](https://woocommerce.com/download/).

## Instalation

Download this repository as a zip fild and upload it to your WordPress instalation as you would do with any other WP plugin.

You will need to configure myData Connector:

1. Go to Settings > MyData Connector
2. Provide the username and the key for myDATA API. The username is the one used for Taxis. You can create a key [here](https://www1.aade.gr/saadeapps2/bookkeeper-web/bookkeeper/#!/apiSubscription?mode=api). Visit the page and choose "Subscribe to myDATA REST API".
3. Choose the receipt type: ΑΛΠ or ΑΠΥ

You will also need to configure the PDF Invoices &amp; Packing Slips for WooCommerce plugin:

1. Go to Woocommerce > PDF Invoices
2. In tab "General" fill in the Store data. Don't forget the Shop VAT Number.
3. In tab "Documents" enable your receipt type in PDF format
4. Choose to attach the receipt on the Completed Order, to make sure you are already paid when you transmit a receipt.
5. Choose to display invoice number and invoice date on the receipt.
6. Add a Prefix. This will used as the receipt series. It's is essential and will be provided by your accountant.

You are ready to go! Start sending receipt to myData by enabling transmission in the plugin's settings.

## Extension

A number of filters have been established in order for you to extend this plugin. All fiters can be found in `includes/class-mydata-connector-helpers.php`.

### Payments methods

Use filter `mydata_connector_payment_methods` to map Payment Methods. Each payment method enabled in WooCommerce needs to be mapped to myData payment methods. 

We use the WC payment method ID as provided in the URL `yourwebiste.com/wp-admin/admin.php?page=wc-settings&tab=checkout&section=ID` and the payment methods type set by [invoicemaker.gr](ttps://docs.invoicemaker.gr/appendix/payment-methods) to set the mapping. 

Example: `'bacs' =>  'METHOD_6'`

### VAT

Use filter `mydata_connector_tax_classes`  to map VAT classes. We map VAT percentages provided by order items with VAT classes set by [invoicemaker.gr](https://docs.invoicemaker.gr/appendix/vat-categories)

Example: `'24%' => 'VAT_1'`

### Income classification

Use filter `mydata_connector_income_classification_categories` to map income categories. We use the income categories set by [invoicemaker.gr](https://docs.invoicemaker.gr/appendix/income-classifications). Specifically we use the following mapping according to product type:

`$income_classification_categories = array(
	'physical' => 'category1_2', //For products
	'virtual' => 'category1_3', //For services
	'default' => 'category1_1' //Default
);`

As for the income type we map all income as E3_561_003. To change this use filter `mydata_connector_income_classification_types`. Check the [official AADE documentation](https://www.aade.gr/sites/default/files/2022-01/syndiasm_charaktitistik_v1.0.4.xls) for more.

## Contribute

You can check the open issues and contribute accordingly or open up new ones.

## Support

If you need support to intall this plugin or need some kind of modification contact as at contact@sociality.gr. Support is provided as a paid service. 

