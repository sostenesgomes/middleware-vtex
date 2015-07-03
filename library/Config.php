<?php

/**
 * Class que contém as configurações utilizadas na ferramenta
 *
 * @author Sóstenes gomes
 *
 * Class Config
 */

class Config {

    /**
     * Função que retorna uma ou mais configurações
     *
     * @author Sóstenes Gomes
     *
     * @param bool $key
     * @return array
     */
	public static function get($key=false){
		$conf = array(

            'title' => 'API de Integração ::: {your-store}.com :: Vtex',

            'sys_charset' => 'UTF-8',

            'render'  => realpath(dirname(__FILE__)),
            'library' => realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR),

            'url_base' => 'http://' . $_SERVER['HTTP_HOST'] . DIRECTORY_SEPARATOR,
            'image'    => 'http://' . $_SERVER['HTTP_HOST'] . DIRECTORY_SEPARATOR  . 'images' . DIRECTORY_SEPARATOR,
            'js'	   => 'http://' . $_SERVER['HTTP_HOST'] . DIRECTORY_SEPARATOR  . 'js'     . DIRECTORY_SEPARATOR,
            'css' 	   => 'http://' . $_SERVER['HTTP_HOST'] . DIRECTORY_SEPARATOR  . 'css'    . DIRECTORY_SEPARATOR,

            'model' 	 => realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'model')      . DIRECTORY_SEPARATOR,
            'view' 	 	 => realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'view')       . DIRECTORY_SEPARATOR,
            'controller' => realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'controller') . DIRECTORY_SEPARATOR,
            'layout' 	 => realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'layout')     . DIRECTORY_SEPARATOR,

            'development'   => false,
            'rewrite_url'   => true,

            'email' => array(
                'charset'   => 'UTF-8',
                'host'      => 'protocol.{your-store}.comr',
                'port'      => 'port-number',
                'auth'      => false,
                'username'  => NULL,
                'password'  => NULL,
                'from'      => 'you@{your-store}.com',
                'from_name' => '{your-store}.com ::: Vtex',
                'address'   => 'to@{your-store}.com'
            ),

            'vtex_params_proxy' => array(
                'development' => array(
                    'proxy_host' => 'yourproxyhost.{your-store}.com',
                    'proxy_port' => 'port-number'
                ),
                'production' => array(
                    'proxy_host' => 'yourproxyhost.{your-store}.com',
                    'proxy_port' => 'port-number'
                )
            ),

            'vtex_params_soap' => array(
                'login'         => 'your-login',
                'password'      => 'your-password',
                'url_wsdl'      => 'http://webservice-{your-store}.vtexcommerce.com.br/Service.svc?wsdl',
                'url_location'  => 'http://webservice-{your-store}.vtexcommerce.com.br/Service.svc'
            ),

            'vtex_params_rest' => array(
                'app_key'                           => 'your-api-key',
                'app_token'                         => 'your-api-token',
                'url_stock_get'                     => 'http://{your-store}.vtexcommercestable.com.br/api/logistics/pvt/inventory/itemsbalance',
                'url_stock_reservations'            => 'http://{your-store}.vtexcommercestable.com.br/api/logistics/pvt/inventory/reservations/@WAREHOUSEID@/@ITEMID@',
                'url_stock_update'                  => 'http://{your-store}.vtexcommercestable.com.br/api/logistics/pvt/inventory/warehouseitembalances',
                'url_price_update'                  => 'http://rnb.vtexcommercestable.com.br/api/pricing/pvt/price-sheet?an={your-store}',
                'url_order_get_by_status'           => 'http://oms.vtexcommerce.com.br/api/oms/pvt/orders/?an={your-store}&f_status=@STATUS@&per_page=20&page=@PAGE@&orderBy=creationDate,asc',
                'url_order_get_by_id'               => 'http://oms.vtexcommerce.com.br/api/oms/pvt/orders/@ORDERID@/?an={your-store}',
                'url_order_update_status'           => 'http://oms.vtexcommerce.com.br/api/oms/pvt/orders/@ORDERID@/start-handling/?an={your-store}',
                'url_order_cancel'                  => 'http://oms.vtexcommerce.com.br/api/oms/pvt/orders/@ORDERID@/cancel/?an={your-store}',
                'url_order_pcb'                     => 'http://{your-store}.vtexcommercestable.com.br/api/oms/pvt/orders/@ORDERID@/payments/@PAYMENTID@/payment-notification',
                'url_order_shipping_notification'   => 'http://oms.vtexcommerce.com.br/api/oms/pvt/orders/@ORDERID@/shipping-notification/?an={your-store}',
                'url_order_email_mapping'           => 'http://conversationtracker.vtex.com.br/api/pvt/emailMapping?an={your-store}&alias=@ALIAS@',
                'url_order_transaction_iteration'   => 'https://{your-store}.vtexpayments.com.br/api/pvt/interactions/transaction/@TRANSACTIONID@',
                'url_client_add'                    => 'http://{your-store}.vtexcommercestable.com.br/api/profile-system/pvt/profiles',
                'url_client_add_address'            => 'http://profilesystem.vtex.com.br/api/profile-system/pvt/profiles/@PROFILEID@/addresses?an={your-store}'
            ),

		);
		
		return $key ? $conf[$key] : $conf;

	}
}