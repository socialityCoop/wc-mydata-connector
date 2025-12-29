# MyData Connector for WooCommerce
A plugin that integrates PDF Invoices &amp; Packing Slips for WooCommerce with myData greek tax system

## Usage

This plugin can be used to provide retail receipts (B2C types: 11_1 and 11_2) through the MyData greek tax system. Receipt are created in your eshop and automatically transmitted to AADE through MyData API. No third-party API is needed and it is free of charge. Each receipt gets a ΜΑΡΚ number and QR code as demanded by law. Receipts are created through the PDF Invoices &amp; Packing Slips for WooCommerce as normal and are available in the WC orders Panel. They can be also be sent automatically to clients.

No invoices (B2B) can be transmitted because AADE demands a third party provider to be involved.

The plugin is based on the PHP library [Invoice Maker](https://docs.invoicemaker.gr/). Many congrats to their amazing work!

## Requirements

To use this plugin you need [PDF Invoices &amp; Packing Slips](https://wordpress.org/plugins/woocommerce-pdf-invoices-packing-slips/) and thus [Woocommerce](https://woocommerce.com/download/).

## Instalation

Download this repository as a zip fild and upload to your WordPress instalation as you would do with any other plugin.

Then you will need choose the setting of myData Connector:

1. Go to Settings > MyData Connector
2. Provide the username and key for myDATA API. You can find the key [here](https://www1.aade.gr/saadeapps2/bookkeeper-web/bookkeeper/#!/apiSubscription?mode=api). Choose "Subscribe to myDATA REST API".
3. Choose receipt type

You will also need to make sure you make the appropriate setting in PDF Invoices &amp; Packing Slips for WooCommerce plugin:

1. Go to Woocommerce > PDF Invoices
2. In tab General fill in the Store data. Especially the Shop VAT Number.
3. In tab Documents enable your receipt type in PDF format
4. Choose to attach it in the Completed Order, to make sure you are paid when you transmit a receipt.
5. Choose to Display invoice number and invoice date
6. Add a Prefix. This will be the receipt series. This will be provided by your accountant.

You are ready to go by enabling transmission in the plugin's settings.

## Configuration

A number of filters have been established in order for you to configure this plugin.

### Payments methods

Use filter `mydata_connector_payment_methods` to map Payment Methods. Each payment method enabled in WC needs to be mapped to myData payment methos. We use the WC payment method ID as provided in the URL `yourwebiste.com/wp-admin/admin.php?page=wc-settings&tab=checkout&section=ID` and the payment methods type set by [invoicemaker.gr](ttps://docs.invoicemaker.gr/appendix/payment-methods). 

Example: `'bacs' =>  'METHOD_6'`






