<?php

/**
 * Helper functions 
 *
 * @link       https://sociality.gr
 * @since      1.0.0
 *
 * @package    Mydata_Connector
 * @subpackage Mydata_Connector/includes
 */

//Add depedencies
use Firebed\AadeMyData\Http\MyDataRequest;
use Firebed\AadeMyData\Enums\PaymentMethod;
use Firebed\AadeMyData\Enums\VatCategory;
use Firebed\AadeMyData\Enums\InvoiceType;

/**
 * Used by main public class as helper function
 *
 *
 * @since      1.0.0
 * @package    Mydata_Connector
 * @subpackage Mydata_Connector/includes
 * @author     Sociality Coop <contact@sociality.gr>
 */
class Mydata_Connector_Helper {

	/**
	 * Asset function to set up communication with myData API.
	 *
	 * @since    1.0.0
	 */
	public static function mydata_connector_init_communication() {

		$stored_options = get_option( 'mydata_connector_options');

		//Check the environment
		if(isset($stored_options['mydata_dev_mode'])&&$stored_options['mydata_dev_mode']==1){
			$env = "dev"; 
			$user_id = $stored_options['mydata_dev_user'];
			$subscription_key = $stored_options['mydata_dev_key'];
		}else{
			$env = "prod"; 
			$user_id = $stored_options['mydata_prod_user'];
			$subscription_key = $stored_options['mydata_prod_key'];
		}

		MyDataRequest::init($user_id, $subscription_key, $env);
	}

	/**
	 * Asset function to map payment methods
	 *
	 * @param string $wc_payment_method based on WC
	 * @since    1.0.0
	 * @return PaymentMethod type. See more here https://docs.invoicemaker.gr/appendix/payment-methods
	 */
	public static function mydata_connector_map_payment_method($wc_payment_method)
	{

		//Payment method mapping
		$payment_methods = array(
			'vivawallet_native' => 'METHOD_7',
			'paypal' => 'METHOD_7',
			'bacs' =>  'METHOD_6',
			'bank_transfer_1' =>  'METHOD_6',
			'bank_transfer_2' => 'METHOD_6',
			'bank_transfer_3' =>  'METHOD_6' ,
			'bank_transfer_3' =>  'METHOD_6'
		);

		//This filter can be used to enrich payment method mapping
		$payment_methods = apply_filters('mydata_connector_payment_methods', $payment_methods);

		//Map to invoice maker library
		$payment_methods_mapped = array();
		foreach ($payment_methods as $key => $value) {
			switch ($value) {
				case 'METHOD_1':
				$payment_methods_mapped[$key] = PaymentMethod::METHOD_1;
				break;
				case 'METHOD_2':
				$payment_methods_mapped[$key] = PaymentMethod::METHOD_2;
				break;
				case 'METHOD_3':
				$payment_methods_mapped[$key] = PaymentMethod::METHOD_3;
				break;
				case 'METHOD_4':
				$payment_methods_mapped[$key] = PaymentMethod::METHOD_4;
				break;
				case 'METHOD_5':
				$payment_methods_mapped[$key] = PaymentMethod::METHOD_5;
				break;
				case 'METHOD_6':
				$payment_methods_mapped[$key] = PaymentMethod::METHOD_6;
				break;
				case 'METHOD_7':
				$payment_methods_mapped[$key] = PaymentMethod::METHOD_7;
				break;
				case 'METHOD_8':
				$payment_methods_mapped[$key] = PaymentMethod::METHOD_8;
				break;
				default:
				$payment_methods_mapped[$key] = PaymentMethod::METHOD_3;
				break;
			}
		}

    	//Send payment method
		if($payment_methods_mapped[$wc_payment_method]){
			return $payment_methods_mapped[$wc_payment_method];
		}else{
        	//Fallback
			return PaymentMethod::METHOD_3;
		}
	}

	/**
	 * Asset function to map vat category
	 * 
	 * @param string $tax_percent based on WC
	 * @since    1.0.0
	 * @return VatCategory type. See more here https://docs.invoicemaker.gr/appendix/vat-categories
	 */
	public static function mydata_connector_map_vat_category($tax_percent)
	{	

		//Tax mapping 
		$tax_classes = array(
			'24%' => 'VAT_1',
			'13%'=> 'VAT_2',
			'6%' =>'VAT_3'
		);

		//This filter can be used to enrich tax classes mapping
		$tax_classes = apply_filters('mydata_connector_tax_classes', $tax_classes);

		//Map to invoice maker library
		$tax_classes_mapped = array();
		foreach ($tax_classes as $key => $value) {
			switch ($value) {
				case 'VAT_1':
				$tax_classes_mapped[$key] = VatCategory::VAT_1;
				break;
				case 'VAT_2':
				$tax_classes_mapped[$key] = VatCategory::VAT_2;
				break;
				case 'VAT_3':
				$tax_classes_mapped[$key] = VatCategory::VAT_3;
				break;
				default:
				$tax_classes_mapped[$key] = VatCategory::VAT_1;
				break;
			}
		}

    	//Send payment method
		if($tax_classes_mapped[$tax_percent]){
			return $tax_classes_mapped[$tax_percent];
		}else{
        	//Fallback
			return VatCategory::VAT_1;
		}
	}


	/**
	 * Asset function to map invoice type
	 * 
	 * @since    1.0.0
	 * @return array with Label and InvoiceType. See more here https://docs.invoicemaker.gr/appendix/invoice-types
	 */
	public static function mydata_connector_map_invoice_type()
	{	

		//Available Types
		$invoice_types = array(
			'TYPE_11_1' => array(
				'label' => __('Retail receipt','mydata-connector'),
			),
			'TYPE_11_2' => array(
				'label' =>__('Receipt of rendered services','mydata-connector'),
			),
			'TYPE_11_3' => array(
				'label' => __('Simplified Invoice','mydata-connector'),
			),
			'TYPE_11_4' => array(
				'label' => __('Retail Credit Note','mydata-connector'),
			)
		);

		//This filter can be used to enrich invoice type mapping
		$invoice_types = apply_filters('mydata_connector_invoice_types', $invoice_types);

		//Map to invoice maker library
		foreach ($invoice_types as $key => $value) {
			switch ($key) {
				case 'TYPE_11_1':
				$invoice_types[$key]['object'] = InvoiceType::TYPE_11_1;
				break;
				case 'TYPE_11_2':
				$invoice_types[$key]['object'] = InvoiceType::TYPE_11_2;
				break;
				case 'TYPE_11_3':
				$invoice_types[$key]['object'] = InvoiceType::TYPE_11_3;
				break;
				case 'TYPE_11_4':
				$invoice_types[$key]['object'] = InvoiceType::TYPE_11_4;
				break;
				default:
				$tax_classes_mapped[$key]['object'] = InvoiceType::TYPE_11_2;
				break;
			}
		}

		//Find invoice type in options
		$stored_options = get_option( 'mydata_connector_options');
		if(isset($stored_options['invoice_type'])){
			return $invoice_types[$stored_options['invoice_type']];
		}else{
			//Fallback
			return InvoiceType::TYPE_11_2;
		}

	}

}