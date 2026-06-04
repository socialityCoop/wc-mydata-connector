<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://sociality.gr
 * @since      1.0.0
 *
 * @package    Mydata_Connector
 * @subpackage Mydata_Connector/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Mydata_Connector
 * @subpackage Mydata_Connector/admin
 * @author     Sociality Coop <contact@sociality.gr>
 */
class Mydata_Connector_Admin {

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
	 * The plugin options
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The options saved for this plugin.
	 */

	private $options;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Add plugin options page
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_options_page()
	{
		add_options_page(
			__('MyData Connector Settings','mydata-connector'), 
			'MyData Connector', 
			'manage_options', 
			'mydata-connector-admin', 
			array( $this, 'create_plugin_options_page' )
		);
	}

	/**
	 * Create the content of the plugin options page
	 *
	 * @since    1.0.0
	 */
	public function create_plugin_options_page()
	{
		$this->options = get_option( 'mydata_connector_options');

		?>
		<div class="wrap">
			<h1><?php _e('MyData Connector Settings','mydata-connector') ; ?></h1>
			<form method="post" action="options.php">
				<?php
                // This prints out all hidden setting fields
				settings_fields( 'mydata_connector_option_group' );
				do_settings_sections( 'mydata-connector-admin' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Init options page and it's settings
	 *
	 * @since    1.0.0
	 */
	public function init_plugin_options_page()
	{        

		register_setting(
			'mydata_connector_option_group', 
			'mydata_connector_options', 
			array( $this, 'sanitize' ) 
		);

		add_settings_section(
			'mydata-connector-section', 
			'', 
			array( $this, 'print_section_info' ), 
			'mydata-connector-admin' 
		);  

		//Production settings

		add_settings_field(
			'mydata_active_transmittion', 
			__('Actively transmitting','mydata-connector'), 
			array( $this, 'active_transmittion_callback' ), 
			'mydata-connector-admin', 
			'mydata-connector-section'
		); 

		add_settings_field(
			'mydata_invoice_type', 
			__('Receipt Type','mydata-connector'), 
			array( $this, 'invoice_type_callback' ), 
			'mydata-connector-admin', 
			'mydata-connector-section'
		); 


		add_settings_field(
			'mydata_prod_user', 
			__('MyData User ID','mydata-connector'), 
			array( $this, 'prod_user_callback' ), 
			'mydata-connector-admin', 
			'mydata-connector-section'
		);   

		add_settings_field(
			'mydata_prod_key', 
			__('MyData Key','mydata-connector'), 
			array( $this, 'prod_key_callback' ), 
			'mydata-connector-admin', 
			'mydata-connector-section'
		);     

		add_settings_field(
			'mydata_invoice_limit', 
			__('Invoice Limit','mydata-connector'), 
			array( $this, 'invoice_limit_callback' ), 
			'mydata-connector-admin', 
			'mydata-connector-section'
		);     


		//Development settings

		add_settings_field(
			'mydata_dev_mode', 
			__('Development Mode','mydata-connector'), 
			array( $this, 'dev_mode_callback' ), 
			'mydata-connector-admin', 
			'mydata-connector-section'
		); 

		add_settings_field(
			'mydata_dev_user', 
			__('MyData Dev User ID','mydata-connector'), 
			array( $this, 'dev_user_callback' ), 
			'mydata-connector-admin', 
			'mydata-connector-section'
		);   

		add_settings_field(
			'mydata_dev_key', 
			__('MyData Dev Key','mydata-connector'), 
			array( $this, 'dev_key_callback' ), 
			'mydata-connector-admin', 
			'mydata-connector-section'
		);   

	}

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     * @since    1.0.0
     * @return array of sanitized $input
     */
    public function sanitize( $input )
    {
    	
    	$new_input = array();

    	if(isset($input)&is_array($input)){
    		foreach ($input as $key => $value) {
    			$new_input[$key] = sanitize_text_field( $value );
    		}
    	}    	

    	return $new_input ;
    }


  	/**
     * Display text description
     *
     * @since    1.0.0
     */
  	public function print_section_info()
  	{
  		_e('Enter your MyData settings below:','mydata-connector');
  	}

    /**
     * Print options fields
     *
     * @since    1.0.0
     */

    public function active_transmittion_callback()
    { ?>
    	<input type="checkbox" id="mydata_active_transmittion" name="mydata_connector_options[mydata_active_transmittion]" value="1"
    	<?php isset( $this->options['mydata_active_transmittion'] ) ? checked('1',$this->options['mydata_active_transmittion']) : ''; ?>
    	/> 
    	<?php
    }

    public function invoice_type_callback()
    { 

    	$select_value = isset($this->options['invoice_type']) ? $this->options['invoice_type'] : '';
    	$choices = array(
    		'TYPE_11_1' => __('Retail receipt','mydata-connector'),
    		'TYPE_11_2' => __('Receipt of rendered services','mydata-connector'),
    	); ?>

    	<select name="mydata_connector_options[invoice_type]">
    		<?php 
    		foreach ($choices as $value => $label) {
    			$selected = selected($select_value, $value, false);
    			echo "<option value='" . esc_attr($value) . "' $selected>" . esc_html($label) . "</option>";
    		} ?> 
    	</select>
    	<?php
    }


    public function prod_user_callback()
    {
    	printf(
    		'<input type="text" id="mydata_prod_user" name="mydata_connector_options[mydata_prod_user]" value="%s" />',
    		isset( $this->options['mydata_prod_user'] ) ? esc_attr( $this->options['mydata_prod_user']) : ''
    	); ?>
    	<p class="description"><?php echo __('Here you use your myData (Taxis) username','mydata-connector'); ?></p>
    	<?php
    }

    public function prod_key_callback()
    {
    	printf(
    		'<input type="text" id="mydata_prod_key" name="mydata_connector_options[mydata_prod_key]" value="%s" />',
    		isset( $this->options['mydata_prod_key'] ) ? esc_attr( $this->options['mydata_prod_key']) : ''
    	); ?>
    	<p class="description"><?php echo __('You can get your myData Subcription Key','mydata-connector'); ?> <a href="https://www1.aade.gr/saadeapps2/bookkeeper-web/bookkeeper/#!/apiSubscription?mode=api" target="_blank"><?php _e('here','mydata-connector') ; ?></a></p>
    	<?php
    }

    public function invoice_limit_callback()
    {
    	printf(
    		'<input type="text" id="mydata_invoice_limit" name="mydata_connector_options[mydata_invoice_limit]" value="%s" />',
    		isset( $this->options['mydata_invoice_limit'] ) ? esc_attr( $this->options['mydata_invoice_limit']) : ''
    	); ?>
    	<p class="description"><?php echo __('If you want you can a set invoice number from which the myData transmission will start.','mydata-connector'); ?></p>
    	<?php
    }


    public function dev_mode_callback()
    { ?>
    	<input type="checkbox" id="mydata_dev_mode" name="mydata_connector_options[mydata_dev_mode]" value="1"
    	<?php isset( $this->options['mydata_dev_mode'] ) ? checked('1',$this->options['mydata_dev_mode']) : ''; ?>
    	/> 
    	<p class="description"><?php echo __('You can get development credentials','mydata-connector'); ?> <a href="https://mydata-dev-register.azurewebsites.net/" target="_blank"><?php _e('here','mydata-connector') ; ?></a></p>
    	<?php
    }


    public function dev_user_callback()
    {	
    	printf(
    		'<input type="text" id="mydata_dev_user" name="mydata_connector_options[mydata_dev_user]" value="%s" />',
    		isset( $this->options['mydata_dev_user'] ) ? esc_attr( $this->options['mydata_dev_user']) : ''
    	); 
    }

    public function dev_key_callback()
    {
    	printf(
    		'<input type="text" id="mydata_dev_key" name="mydata_connector_options[mydata_dev_key]" value="%s" />',
    		isset( $this->options['mydata_dev_key'] ) ? esc_attr( $this->options['mydata_dev_key']) : ''
    	);
    }

}
