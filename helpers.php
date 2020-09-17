<?php

/*********** PART 3. External Script Area out of the main WC_PAYMENT_GATEWAY CLASS  *************************/

// Grab Admin and Payment Data for Xchange Gateway

function myscript() {
	
 	global $woocommerce;
 		// Get product data, in this case product names
	$items = $woocommerce->cart->get_cart();
   	$product_names=array();
   
    foreach($items as $item => $values) { 
      $_product = $values['data']->post; 
      $product_names[]=$_product->post_title; 
    }   // End Product Names

	//To get Xchange user email
	$adb_xchange_settings = get_option( 'woocommerce_xchange_settings' );
	$adb_xchange_usermail = $adb_xchange_settings['adb_username_email'];
	$adb_xchange_username = $adb_xchange_settings['adb_username'];
	$adb_xchange_sandbox_state = $adb_xchange_settings['sandbox'];
			//validation fields array
	$adb_validation_fields_lista = array();
	$adb_validation_fields_lista = $adb_xchange_settings['validation'];
	$arrlength = count($product_names);
	$description = "";

	for($x = 0; $x < $arrlength; $x++) {
		
		$description .= $product_names[$x]; 

		if (($x + 1) < $arrlength) {
			$description .= ", ";
		} else {
			$description .= ".";
		}
	}

    if( wp_script_is( 'jquery', 'done' ) ) {
    ?>
  		<link type="text/css" href='https://cdn.xchange.la/css/preloader_api.css' rel="stylesheet">
  		<style type="text/css">
		div.payment_method_xchange::before {
			display: none !important;
		}
		.woocommerce-checkout #payment div.payment_box {
		    padding-top: 0px !important;
		}
  		</style>
		<script type="text/javascript">

     	var data = {    	    

            PayboxRemail: "<?php echo $adb_xchange_usermail; ?>",
 
 			PayboxSendmail: "",
        
            PayboxRename: "<?php echo $adb_xchange_username; ?>",

            PayboxSendname: "", 
 
            PayboxAmount: "<?php echo number_format($woocommerce->cart->total, 2) ?>",

            PayboxDescription: "<?php echo $description; ?>",

            PayboxProduction: <?php if ($adb_xchange_sandbox_state == "yes") {  echo "false"; } else { echo "true"; } ?>,
            PayboxRequired: [
           //Fields Requirred to be filled in, before Modal showing up, fetched from wp-admin
            	   <?php echo $adb_validation_fields_lista; ?>
            ]
        };

        var onAuthorize = function(response) {
        	//Custom Ajax function to process Payment via WC-AJAX

        	jQuery.ajax({
				url: 'xchange/?wc-ajax=checkout',
				type: "POST",
				data: jQuery(".woocommerce-checkout").serialize() + "&id_transaccion=" + response.id_transaccion + "&token=" + response.token + "&fecha=" + response.fecha,
				success: function(respuesta) {
					if (respuesta.result == "failure") {
						jQuery(".woocommerce-message").remove();
						jQuery(".woocommerce-NoticeGroup-checkout").append(respuesta.messages);
					} else if (respuesta.result == "success") {
						parent.location.href = respuesta.redirect;
					}
				},
				error: function() {
			        console.log("No se ha podido obtener la información");
			    }
			});
		}
		
		jQuery.getScript("https://xchange.paybox.la/paybox/index_Wordpress.js")
	  	.done(function(script, textStatus) {
	    	var object = {
				"data": data,
				"authorize": onAuthorize
			};
			Data.init(object);
	  	})
  		.fail(function(jqxhr, settings, exception) {
            
	   	});

 		</script>
    <?php
    }
}
add_action( 'wp_footer', 'myscript' );

