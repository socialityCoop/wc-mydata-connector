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
		$payment_methods = array(
			'vivawallet_native' => PaymentMethod::METHOD_7,
			'paypal' => PaymentMethod::METHOD_7,
			'bacs' =>  PaymentMethod::METHOD_6,
			'bank_transfer_1' =>  PaymentMethod::METHOD_6,
			'bank_transfer_2' =>  PaymentMethod::METHOD_6,
			'bank_transfer_3' =>  PaymentMethod::METHOD_6,
			'bank_transfer_3' =>  PaymentMethod::METHOD_6,
		);

		//This filter can be used to enrich payment method mapping
		$payment_methods = apply_filters('mydata_connector_payment_methods', $payment_methods);

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
	 * @param string $wc_vat_classbased on WC
	 * @since    1.0.0
	 * @return VatCategory type. See more here https://docs.invoicemaker.gr/appendix/vat-categories
	 */
	public static function mydata_connector_map_vat_category($wc_vat_class)
	{
		$tax_classes = array(
			'standard' => VatCategory::VAT_1,
			'reduced-rate'=> VatCategory::VAT_2,
			'reduced' => VatCategory::VAT_2,
			'zero-rate' =>VatCategory::VAT_3,
			'zero' =>VatCategory::VAT_3
		);

		//This filter can be used to enrich tax classes mapping
		$tax_classes= apply_filters('mydata_connector_tax_classes', $tax_classes);

    	//Send payment method
		if($tax_classes[$wc_vat_class]){
			return $tax_classes[$wc_vat_class];
		}else{
        	//Fallback
			return VatCategory::VAT_1;
		}
	}

}