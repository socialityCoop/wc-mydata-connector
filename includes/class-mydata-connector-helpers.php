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
				case 'METHOD_7':
					$tax_classes_mapped[$key] = PaymentMethod::METHOD_7;
					break;
				case 'METHOD_6':
					$tax_classes_mapped[$key] = PaymentMethod::METHOD_6;
					break;
				default:
					$tax_classes_mapped[$key] = PaymentMethod::METHOD_3;
					break;
			}
		}

    	//Send payment method
		if($payment_methods[$wc_payment_method]){
			return $payment_methods[$wc_payment_method];
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

}