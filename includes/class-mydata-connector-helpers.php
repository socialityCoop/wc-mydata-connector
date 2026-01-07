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
use Firebed\AadeMyData\Enums\IncomeClassificationType;
use Firebed\AadeMyData\Enums\IncomeClassificationCategory;

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

		//Disable SSL check for dev env
		if($env == "dev"){
			MyDataRequest::verifyClient(false);
		}
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
			'6%' =>'VAT_3',
			'17%' =>'VAT_4',
			'9%' =>'VAT_5',
			'4%' =>'VAT_6',
			'0%' =>'VAT_7'
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
				case 'VAT_4':
				$tax_classes_mapped[$key] = VatCategory::VAT_4;
				break;
				case 'VAT_5':
				$tax_classes_mapped[$key] = VatCategory::VAT_5;
				break;
				case 'VAT_6':
				$tax_classes_mapped[$key] = VatCategory::VAT_6;
				break;
				case 'VAT_7':
				$tax_classes_mapped[$key] = VatCategory::VAT_7;
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
			return InvoiceType::TYPE_11_1;
		}

	}

	/**
	 * Asset function to map IncomeClassification
	 * 
	 * @param $orderItem object
	 * @since  1.0.0
	 * @return array with IncomeClassificationType and IncomeClassificationCategory. See more here https://docs.invoicemaker.gr/appendix/income-classifications and here https://www.aade.gr/sites/default/files/2022-01/syndiasm_charaktitistik_v1.0.4.xls
	 */
	public static function mydata_connector_map_income_classification($orderItem)
	{	

		$income_classification = array();

		/* Categories */

		//Available Categories
		$income_classification_categories = array(
			'physical' => 'category1_2', //For products
			'virtual' => 'category1_3', //For services
			'default' => 'category1_1' //Default
		);

		//This filter can be used to enrich invoice category mapping
		$income_classification_categories = apply_filters('mydata_connector_income_classification_categories', $income_classification_categories);

		//Map to invoice maker library
		$income_classification_categories_mapped = array();
		foreach ($income_classification_categories as $key => $value) {
			switch ($value) {
				case 'category1_1':
				$income_classification_categories_mapped[$key] = IncomeClassificationCategory::CATEGORY_1_1;
				break;
				case 'category1_2':
				$income_classification_categories_mapped[$key] = IncomeClassificationCategory::CATEGORY_1_2;
				break;
				case 'category1_3':
				$income_classification_categories_mapped[$key] = IncomeClassificationCategory::CATEGORY_1_3;
				break;
			}
		}

		//Set category
		$stored_options = get_option( 'mydata_connector_options');
		//Check invoice type
		if(isset($stored_options['invoice_type'])&&$stored_options['invoice_type']=='TYPE_11_2'){
			//Set for services by default in that case (nothing else is allowed by myData)
			$income_classification['category'] = IncomeClassificationCategory::CATEGORY_1_3;
		}else{
			//Check product type
			$orderProduct = wc_get_product($orderItem->get_product_id());
			$virtual = $orderProduct->is_virtual();
			if($virtual==true){
				$income_classification['category'] = $income_classification_categories_mapped['virtual']; 
			}elseif($virtual==false) {
				$income_classification['category'] = $income_classification_categories_mapped['physical']; 
			}else{
				$income_classification['category'] = $income_classification_categories_mapped['default']; 
			}
		}
		
		/* Types */

		//Available Types
		$income_classification_types = array(
			'default' => 'E3_561_003' //Default
		);

		//This filter can be used to enrich invoice type mapping
		$income_classification_types = apply_filters('mydata_connector_income_classification_types', $income_classification_types);

		//Map to invoice maker library
		$income_classification_types_mapped = array();
		foreach ($income_classification_types as $key => $value) {
			switch ($value) {
				case 'E3_561_003':
				$income_classification_types_mapped[$key] = IncomeClassificationType::E3_561_003;
				break;
				case 'E3_561_004':
				$income_classification_types_mapped[$key] = IncomeClassificationType::E3_561_004;
				break;
				case 'E3_561_005':
				$income_classification_types_mapped[$key] = IncomeClassificationType::E3_561_005;
				break;
				case 'E3_561_006':
				$income_classification_types_mapped[$key] = IncomeClassificationType::E3_561_006;
				break;
				case 'E3_561_007':
				$income_classification_types_mapped[$key] = IncomeClassificationType::E3_561_007;
				break;
			}
		}

		//Set type
		$income_classification['type'] =  $income_classification_types_mapped['default'];

		return $income_classification;
	}

}