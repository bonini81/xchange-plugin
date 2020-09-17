<?php
/*
 * Plugin Name: WooCommerce Xchange Gateway
 * Plugin URI: https://xchange.la/docs/
 * Description: Take credit card payments on your store with Xchange.la
 * Version: 1.0.0
 * Author: Msc. Andrés Domínguez Bonini
 * Author URI: pugle.net
 * License: GPL2
 */



 if ( ! defined( 'ABSPATH' ) ) {
	exit; /* Exit if accessed directly. Security measure to avoid direct access to plugin under Wordpress Security Guidelines*/
}

/*
Content Index

Part 1. WP admin area set up and creation of the main class WC_Xchange_Gateway extends WC_Payment_Gateway 

Part 2. Process Payment

Part 3. External Script Area

*/

/*********** PART 1. wp-admin Area Scripts and start of the WC_Payment_Gateway which is the Woocommerce API Gateway Plugin Creation Class *************************/

 
 /*
 * This action hook registers our PHP class as a WooCommerce payment gateway
 */
 

add_filter( 'woocommerce_payment_gateways', 'xchange_add_gateway_class' );
function xchange_add_gateway_class( $gateways ) {
	$gateways[] = 'WC_Xchange_Gateway'; // your class name is here
	return $gateways;
}
 
/*
 * The class itself, please note that it is inside plugins_loaded action hook
 */
add_action( 'plugins_loaded', 'xchange_init_gateway_class' );
function xchange_init_gateway_class() {


 	class WC_Xchange_Gateway extends WC_Payment_Gateway {

 
 		/**
 		 * Class constructor
 		 */
		public function __construct() {

			$this->id = 'xchange'; // payment gateway plugin ID
			$this->icon = 'https://cdn.xchange.la/img/logo_cms.png'; // URL of the icon that will be displayed on checkout page near your gateway name
			$this->has_fields = true; // in case you need a custom credit card form
	     	// Supports the default credit card form
		  	$this->supports = array('default_credit_card_form');
			$this->method_title = 'Xchange';
			$this->method_description = 'Description of Xchange'; // will be displayed on the options page
		 	//$this->method_adb_username_email = 'Xchange Username';

			// gateways can support subscriptions, refunds, saved payment methods, this case Products

			//OJO!!!! SEE if we can open to ALL comment by ADB. Product just testing man. 
			$this->supports = array(
				'products'
			);

			// Supports the default credit card form
			$this->supports = array('default_credit_card_form');

	        // This basically defines your settings which are then loaded with init_settings()
	        $this->init_form_fields();
				// After init_settings() is called, you can get the settings and load them into variables, e.g:
	        // $this->title = $this->get_option( 'title' );
	        $this->init_settings();

	        // Turn these settings into variables we can use
	        foreach ($this->settings as $setting_key => $value) {
	            $this->$setting_key = $value;
	        }
			
			$this->title = $this->get_option( 'title' );
			$this->description = $this->get_option( 'description' );
			$this->enabled = $this->get_option( 'enabled' );
			$this->sandbox = $this->get_option( 'sandbox' );
			$this->validation = $this->get_option( 'validation' );

			// Turn these settings into variables we can use
	        foreach ($this->settings as $setting_key => $value) {
	            $this->$setting_key = $value;
	        }

			// Turn these settings into variables we can use
			/*foreach ( $this->settings as $setting_key => $value ) {
				$this->$setting_key = $value;
			}*/
			///End

			// This action hook saves the settings
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
	 	

			// We need custom JavaScript to obtain a token
			//add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ) );
		 
			// You can also register a webhook here
			// add_action( 'woocommerce_api_{webhook name}', array( $this, 'webhook' ) );

	 	}  // End Constructor
 
		/**
		 * Admin Settings on the wp-admin/woocommerce/settings/xchange
		 */
	 	public function init_form_fields(){
	 
			$this->form_fields = array(
				'enabled' => array(
					'title'       => 'Enable/Disable',
					'label'       => 'Enable Xchange',
					'type'        => 'checkbox',
					'description' => '',
					'default'     => 'no'
				),

				'sandbox' => array(
					'title'       => 'Xchange Sandbox',
					'label'       => 'Enable SandBox',
					'type'        => 'checkbox',
					'description' => 'Xchange sandbox can be used to test payments. Recommended before using it in production environment.',
					'default'     => 'yes'
				),


				'title' => array(
					'title'       => 'Title',
					'type'        => 'text',
					'description' => 'This controls the title which the user sees during checkout.',
					'default'     => 'Xchange',
					'desc_tip'    => true,
				),
				'description' => array(
					'title'       => 'Description',
					'type'        => 'textarea',
					'description' => 'This controls the description which the user sees during checkout.',
					'default'     =>  'Pay with your credit card via our super-cool payment gateway.' ,
				),

				'validation' => array(
					'title'       => 'Form Validation Fields',
					'type'        => 'textarea',
					'description' => 'Required fields to be validated on Checkout Form before Xchange pops up (if not filled Xchange Modal wont pop up), leave your custom fields, between "", Ex: "#billing_email" , "#my_custom_field"',
					'default'     =>	
						'"#billing_first_name",
		            	"#billing_last_name",
		            	"#billing_country",
		            	"#billing_address_1",
		            	"#billing_city",
		            	"#billing_state", 
		            	"#billing_postcode", 
		            	"#billing_phone", 
		            	"#billing_email"',
				),
				
				'adb_username_email' => array(
					'title'       => 'Username Email',
					'type'        => 'text',
					'description' => 'Your Xchange Email',
				),
			
				'adb_username' => array(
					'title'       => 'Username',
					'type'        => 'text',
					'description'     => 'Your Xchange Username'

				),

	            'password' => array(
	                'title' => __('Password', 'xchange-gate'),
	                'type' => 'password',
	                'desc_tip' => __('Your Xchange Pasword', 'xchange-gate'),
	            )
			);
		}



/*********** PART 2. Process Payment functions from API and custom AJAX function to process the payment via JS not PHP as usually natively done  *************************/



		// Process Payment Woocommerce API - Funcion para Procesar pedido de Wordpress / Woocommerce
 
		public function process_payment( $order_id ) {

			if (isset($_POST['id_transaccion'])) {


				if ($_POST['id_transaccion'] != "XCHANGE_PAYBOX") {
					$post = [
					    'token' => $_POST["token"],
					    'fecha' => $_POST["fecha"],
					];

					$ch = curl_init('https://xchange.paybox.la/ajax/wordpress.php');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

					$response = json_decode(curl_exec($ch), true);
					curl_close($ch);

					if ($response["status"] == "succeeded") {

						global $woocommerce;
						$customer_order = new WC_Order($order_id);

						// Payment successful
						$customer_order->add_order_note(__('Xchange complete payment.', 'cwoa-authorizenet-aim'));
															 
						// paid order marked
						$customer_order->payment_complete();

						// this is important part for empty cart
						$woocommerce->cart->empty_cart();

						// Redirect to thank you page
						return array(
							'result'   => 'success',
							'redirect' => $this->get_return_url( $customer_order ),
						);
					} else {
						wc_add_notice( __('El pago es invalido !', 'woothemes'), 'error' );
						return;		
					}
				} else {
					wc_add_notice( __('Pago de prueba procesado exitosamente !', 'woothemes'), 'success' );
					return;	
				}
			} else {
				//transiction fail
				wc_add_notice( __('Procesando Pedido', 'woothemes'), 'success' );
				return;
			}
		}

		// Validate form fields
		public function validate_fields() {
			return true;
		}

		// To add the Xchange Button on the Xchange payment box
 		public function payment_fields() {

			//Grab description data as this is erased when payment fields enabled
			$adb_xchange_settings2 = get_option( 'woocommerce_xchange_settings' );
			$adb_xchange_description_admin_area = $adb_xchange_settings2['description'];

			do_action('woocommerce_credit_card_form_start'); 
		  	echo  '<p>' . $adb_xchange_description_admin_area . '</p><br/><div id="ButtonPaybox"></div>';
			do_action('woocommerce_credit_card_form_end');   

		} // End Add custom button

	}  //End of Class WC_Xchange_Gateway extends WC_Payment_Gateway
} // End of function xchange_init_gateway_class


/*********** Included file PART 3. External Script Area out of the main WC_PAYMENT_GATEWAY CLASS  *************************/
include 'helpers.php';