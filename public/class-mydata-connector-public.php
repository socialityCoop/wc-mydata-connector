<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://sociality.gr
 * @since      1.0.0
 *
 * @package    Mydata_Connector
 * @subpackage Mydata_Connector/public
 */


/**
 * Load depedencies
 *
 * */

//Basic
use Firebed\AadeMyData\Http\MyDataRequest;
use Firebed\AadeMyData\Http\SendInvoices;
use Firebed\AadeMyData\Exceptions\MyDataException;
use Firebed\AadeMyData\Models\Response;
//Invoice
use Firebed\AadeMyData\Models\Invoice;
use Firebed\AadeMyData\Models\InvoicesDoc;
use Firebed\AadeMyData\Models\Issuer;
use Firebed\AadeMyData\Models\InvoiceHeader;
use Firebed\AadeMyData\Enums\InvoiceType;
use Firebed\AadeMyData\Models\PaymentMethodDetail;
use Firebed\AadeMyData\Enums\PaymentMethod;
use Firebed\AadeMyData\Models\InvoiceDetails;
use Firebed\AadeMyData\Enums\VatCategory;
use Firebed\AadeMyData\Enums\IncomeClassificationType;
use Firebed\AadeMyData\Enums\IncomeClassificationCategory;
//QR code
use chillerlan\QRCode\QRCode;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Mydata_Connector
 * @subpackage Mydata_Connector/public
 * @author     Sociality Coop <contact@sociality.gr>
 */
class Mydata_Connector_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * This the main function that sends invoices to myData greek tax authorities
	 * 
	 * @param string $type invoice type as provided by woocommerce-pdf-invoices-packing-slips
	 * @param order object $order order as provided by woocommerce-pdf-invoices-packing-slips
	 * @since    1.0.0
	 */
	public function mydata_connector_send_invoice($type, $order) {

		//Get options
		$stored_options = get_option( 'mydata_connector_options');

		//Check if actively transmitting		
		if(isset($stored_options['mydata_active_transmittion'])&&$stored_options['mydata_active_transmittion']==1){
			//Continue
		}else{
			return;
		}

		//Check if the invoice has been already sent
		$transmitted = get_post_meta($order->get_id(), 'mydata_gr_transmitted', true);
		if ($transmitted) {
			return;
		}

		//Get data from wcpdf
		$wcpdfInvoice = wcpdf_get_invoice((array) $order->get_id(), true);

		//Invoice number
		$invoice_number = $wcpdfInvoice->get_number();
		$plain_invoice_number = $invoice_number->get_plain();
		$invoice_series = str_replace($plain_invoice_number, "", $invoice_number);

		//Check invoice number limit		
		$invoice_number_limit = $stored_options['mydata_invoice_limit'];
		if(isset($invoice_number_limit)&&$invoice_number_limit>$plain_invoice_number){
			return;
		}

		//Initiate connection
		Mydata_Connector_Helper::mydata_connector_init_communication();

		//Vat number
		$vat_number = $wcpdfInvoice->get_shop_vat_number(); 

		//Create invoice

		//Set issuer
		$issuer = new Issuer();
		$issuer->setVatNumber($vat_number);
		$issuer->setCountry('GR');
		$issuer->setBranch(0);

		//Header
		$header = new InvoiceHeader();
		$header->setSeries($invoice_series);
		$header->setAa($plain_invoice_number);
		$header->setIssueDate(date('Y-m-d'));
		$mydata_invoice_type = Mydata_Connector_Helper::mydata_connector_map_invoice_type();
		$header->setInvoiceType($mydata_invoice_type['object']);
		$header->setCurrency('EUR');

		//Payment method
		$payment = new PaymentMethodDetail();
		$payment_method = Mydata_Connector_Helper::mydata_connector_map_payment_method($order->get_payment_method());
		$payment->setType($payment_method);
		$payment->setAmount($order->get_total());
		$payment->setPaymentMethodInfo($order->get_payment_method_title());

		//Get order items
		$orderItems = $order->get_items('tax');
		//This filter can be used to alterate order items
		$orderItems = apply_filters('mydata_connector_order_items',$orderItems,$plain_invoice_number);

		//Add products in invoice
		$rows = array();
		$i = 1;
		foreach ($orderItems as $item_id => $orderItem) {

			//Get product values
			$netValue = number_format((float)$orderItem['total'], 2, '.', '');
			$vat = number_format((float)$orderItem['total_tax'], 2, '.', '');

			//Get taxe rate
			$tax_rate_id  = $orderItem->get_rate_id(); 
			$tax_percent = WC_Tax::get_rate_percent( $tax_rate_id );
			$vat_category = Mydata_Connector_Helper::mydata_connector_map_vat_category($tax_percent);

			//Add to invoice
			$row = new InvoiceDetails();
			$row->setLineNumber($i);
			$row->setNetValue($netValue);
			$row->setVatCategory($vat_category);
			$row->setVatAmount($vat);
			$row->addIncomeClassification(
				IncomeClassificationType::E3_561_003,
				IncomeClassificationCategory::CATEGORY_1_3,
				$netValue
			);
			array_push($rows, $row);
			$i++;
		}

		//Store invoice data
		$invoice = new Invoice();
		$invoice->setIssuer($issuer);
		$invoice->setInvoiceHeader($header);
		$invoice->addPaymentMethod($payment);
		foreach ($rows as $row) {
			$invoice->addInvoiceDetails($row);
		}
		$invoice->summarizeInvoice();

		//Send invoice
		$request = new SendInvoices();
		try {
			$response = $request->handle($invoice);
			$response = $response[0];

			//Check status
			if ($response->getStatusCode() === 'Success') {

				//Get and store data
				$uid = $response->getInvoiceUid();
				$qrUrl = $response->getQrUrl();
				$mark = $response->getInvoiceMark();
				update_post_meta($order->get_id(), 'mydata_gr_uid', $uid);
				update_post_meta($order->get_id(), 'mydata_gr_qr', $qrUrl);
				update_post_meta($order->get_id(), 'mydata_gr_mark', $mark);

				//Set as transmitted
				update_post_meta($order->get_id(), 'mydata_gr_transmitted', true);
			} else {

				//Show invoice errors
				foreach ($response->getErrors() as $error) {
					echo $error->getCode() . ': ' . $error->getMessage() . '<br>';
				}
				exit();
			}
		} catch (MyDataException $e) {

			//Show connection error
			echo "Communication error: " . $e->getMessage();
			exit();
		}

	}

	/**
	 * Add MARK to PDF Invoice
	 * 
	 * @param string $type invoice type as provided by woocommerce-pdf-invoices-packing-slips
	 * @param order object $order order as provided by woocommerce-pdf-invoices-packing-slips
	 * @since    1.0.0
	 */
	public  function mydata_connector_add_mark($type,$order){
		$mark = get_post_meta($order->get_id(), 'mydata_gr_mark', true);
		if($mark){
			echo '<tr class="mark"><th>';
			_e( 'MAΡK:', 'woocommerce-pdf-invoices-packing-slips' );
			echo '</th><td>'.$mark.'</td></tr>';
		}

	}

	/**
	 * Add QR to PDF Invoice
	 * 
	 * @param string $type invoice type as provided by woocommerce-pdf-invoices-packing-slips
	 * @param order object $order order as provided by woocommerce-pdf-invoices-packing-slips
	 * @since    1.0.0
	 */
	public function mydata_connector_add_qr($type,$order){
		$qr = get_post_meta($order->get_id(), 'mydata_gr_qr', true);
		if($qr){
			echo '<img width="100" height="100" src="'.(new QRCode)->render( $qr).'" alt="QR Code" />';
		}
	}

	/**
	 * Change PDF Invoice Title to Receipt
	 * 
	 * @param string $type invoice type as provided by woocommerce-pdf-invoices-packing-slips
	 * @param order object $order order as provided by woocommerce-pdf-invoices-packing-slips
	 * @since    1.0.0
	 */
	public  function mydata_connector_change_title( $title, $document ) {
		if ( 'invoice' === $document->get_type() ) {
			$mydata_invoice_type = Mydata_Connector_Helper::mydata_connector_map_invoice_type();
			$title = $mydata_invoice_type['label'];
		}
		return $title;
	}

}
