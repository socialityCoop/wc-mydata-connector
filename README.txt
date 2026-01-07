=== MyData Connector for WooCommerce ===
Contributors: sociality
Tags: woocommerce, mydata, receipts, pdf invoices, greece
Requires at least: 5.8
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Integrates PDF Invoices & Packing Slips for WooCommerce with the Greek AADE myDATA system for automatic retail receipt submission.

== Description ==

**MyData Connector for WooCommerce** allows WooCommerce stores to automatically transmit retail receipts (B2C) 11_1 and 11_2 to the Greek AADE **myDATA** platform.

The plugin works seamlessly with **PDF Invoices & Packing Slips for WooCommerce**, generating legally compliant receipts that include a **ΜΑΡΚ number** and **QR code**, exactly as required by Greek tax legislation.

No third-party providers are required and no extra charges apply. Receipts are created inside WooCommerce and transmitted directly to AADE using the official myDATA API.

⚠️ **Important:**  
Only **retail receipts (B2C – 11_1 and 11_2)** are supported.  
**Invoices (B2B)** cannot be transmitted, as AADE requires a certified third-party provider.

The plugin is based on the PHP library **Invoice Maker** (https://docs.invoicemaker.gr/).

== Features ==

* Automatic transmission of retail receipts to myDATA
* No third-party API or service fees
* Generates ΜΑΡΚ number and QR code
* Uses WooCommerce orders as source of truth
* Works on PDF Invoices & Packing Slips

== Requirements ==

* WooCommerce
* PDF Invoices & Packing Slips for WooCommerce  

== Installation ==

1. Download the plugin ZIP file.
2. Upload it via **Plugins → Add New → Upload Plugin**.
3. Activate **MyData Connector for WooCommerce**.

== Configuration ==

=== MyData Connector Settings ===

1. Go to **Settings → MyData Connector**
2. Enter your **myDATA API username and API key**
   * Username: your Taxisnet username
   * API key: create one here  
     https://www1.aade.gr/saadeapps2/bookkeeper-web/bookkeeper/#!/apiSubscription?mode=api  
     (Choose **Subscribe to myDATA REST API**)
3. Select receipt type: **ΑΛΠ** or **ΑΠΥ**
4. Enable automatic transmission

=== PDF Invoices & Packing Slips Settings ===

1. Go to **WooCommerce → PDF Invoices**
2. **General tab**
   * Fill in store details
   * Make sure the **VAT number** is correct
3. **Documents tab**
   * Enable your receipt document in PDF format
   * Enable attachment on **Completed Order** to make sure you are already paid when transimitting a receipt.
   * Enable invoice number and date display
   * Add a **Prefix** (this is your receipt series and must be provided by your accountant)

You are ready to go! Start sending receipt to myData by enabling transmission in the plugin's settings.

== Frequently Asked Questions ==

= Does this plugin support invoices (B2B)? =
No. AADE requires a certified third-party provider for B2B invoices.

= Are there any extra fees? =
No. The plugin communicates directly with AADE without intermediaries.

= When are receipts sent to myDATA? =
Receipts are transmitted after the order is marked as **Completed**, ensuring payment has been received.

== Extending the Plugin ==

The plugin includes several filters for customization.  
All filters are located in `includes/class-mydata-connector-helpers.php`. Check **our GitHub Repo** (https://github.com/socialityCoop/wc-mydata-connector) for futher documentation.

== Changelog ==

= 1.0.0 =
* Initial release

== Support ==

Support and custom modifications are provided as a **paid service**.

Contact: **contact@sociality.gr**

== Contribute ==

You are welcome to open issues or contribute via **our GitHub Repo** (https://github.com/socialityCoop/wc-mydata-connector).