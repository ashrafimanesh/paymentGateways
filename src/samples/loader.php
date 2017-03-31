<?php

require_once __DIR__.'/../../vendor/autoload.php';

if(!function_exists('env')){
    function env($variable,$default=null){
        switch($variable){
            case 'payment_gateway_saman_mid':
                return '';
            case 'payment_gateway_proxy_enable':
                return true;
            case 'payment_gateway_proxy_type':
                return 'urlProxy';
            case 'payment_gateway_proxy_curlProxyAddress':
                return 'http://ashrafimanesh.ir/proxy/curl.php';
            case 'payment_gateway_proxy_soapProxyAddress':
                return 'http://ashrafimanesh.ir/proxy/soap.php';
            case 'payment_gateway_mellat_username':
                return '';
            case 'payment_gateway_mellat_terminal_id':
                return '';
            case 'payment_gateway_mellat_password':
                return '';
        }
        return $default;
    }
}
