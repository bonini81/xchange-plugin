==  Xchange Payment Gateway  ==

Plugin URI: https://xchange.la/docs/
Version: 1.0.0
Contributor: Msc. Andrés Domínguez Bonini
License: GPL2
Tested until Wordpress 4.9.8 and Woocommerce 3.4.4, should be compatible with most themes and plugins since it uses mainly the Wordpress and Woocommerce API to do what it needs to be done. 

 == Description ==

 This plugin integrates Woocommerce with Xchange Payment Facilitator button, which you can use as a Payment gateway for all major credit cards and even paypal accounts. Xchange has its headquarters in Quito, Ecuador and is expanding its services in Latin America. It has the lowest fees in the Ecuadorian Market, for more information you can ckeckout the official website. https://xchange.la/sobre-xchange/ 

 = Minimum Requirements =

* PHP version 5.2.4 or greater (PHP 5.6 or greater is recommended)
* MySQL version 5.0 or greater (MySQL 5.6 or greater is recommended)
* Wordpress version 4.9.3 onwards
* WooCommerce version 3.4.0 onwards


== Installation ==

1. Install via FTP. Go to your domain's root folder and go to wp-content/plugins/ and upload the xchange folder on this route. Later go to your domain.com/wp-content/ And go to the Inactive Plugins area and press activate plugin. By the way woocommerce has to be ACTIVATED before this plugin in order for it to work. 

2. If the plugin folder is zipped, go to wp-admin area go to Plugins-> Add New -> Upload Plugin-> Select File and press Install now

==  Plugin Configuration ==

Once installed, go to Woocommerce -> Settings Pick Checkout Tab and once on that tab go to Payment gateways and click on Xchange, once on Xchange, click on Enable Xchange, and put your Xchange account data. Also, we put the default wordpress, checkout requirred fields as mandatory before the Xchange Modal pop ups like, #billing_email" if you add your custom requirred fields, please add them on the Form Validation Fields area in Xchange tab, on this format: Ex: #billing_email", "#my_custom_field", "#my_custom_field2" always use the #id css format. If requirred fields are not filled then Xchange Modal is not going to PopUp. So check this detail before implementing.

==  Security Tips ==

Before using xchange, enable the sandbox option, to test the gateway. Only the paranoid survive as Andy Grove, CEO of Intel once said. So test before implementing, here is some sandbox credit card numbers and data to test, https://xchange.la/docs/ Once you see that the order has been marked on the checkout page as processed (not really its, sandbox), disable sandbox.  By the way, on sandbox, for security reasons, payments and are not processed in xchange and the order is not recorded in woocommerce. If you wish to test, that order processing please contact us to guide you on how to do that on the code. For security reasons, sandbox testing is limited. 
Also remember once the plugin is deleted all xchange data will be erased.

==  More information ==

For more information or doubts contact at Xchange.la or (593) 25 107 752, soporte@xchange.la 








