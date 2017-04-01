<?php
/**
 * Created by PhpStorm.
 * User: ashrafimanesh
 * Date: 3/24/17
 * Time: 10:33 AM
 */

return [
    'logPath'=>function_exists('env') ? env('payment_gateway_logPath',__DIR__.'/../logs') : __DIR__.'/../logs'
    ,'logDateFormat'=>function_exists('env') ? env('payment_gateway_logDateFormat','Y-m-d') : 'Y-m-d'
    ,'proxy'=>[
        'enable'=>function_exists('env') ? env('payment_gateway_proxy_enable',true) : true,
        'type'=>function_exists('env') ? env('payment_gateway_proxy_type','urlProxy') : 'urlProxy',//'proxy|urlProxy'
        'curlProxyAddress'=>function_exists('env') ? env('payment_gateway_proxy_curlProxyAddress','http://ashrafimanesh.ir/proxy/curl.php') : 'http://ashrafimanesh.ir/proxy/curl.php',//http://proxy url
        'soapProxyAddress'=>function_exists('env') ? env('payment_gateway_proxy_soapProxyAddress','http://ashrafimanesh.ir/proxy/soap.php') :  'http://ashrafimanesh.ir/proxy/soap.php',//http://proxy url
        'nusoapProxyAddress'=>function_exists('env') ? env('payment_gateway_proxy_nusoapProxyAddress','http://ashrafimanesh.ir/proxy/nusoap.php') :  'http://ashrafimanesh.ir/proxy/nusoap.php',//http://proxy url
    ]
    ,'gateways'=>[
        'mellat'=>[
            'userName'=>function_exists('env') ? env('payment_gateway_mellat_username') : '',
            'terminalId' => function_exists('env') ? env('payment_gateway_mellat_terminal_id') : '',
            'userPassword' => function_exists('env') ? env('payment_gateway_mellat_password') : ''
        ],
        'saman'=>['MID'=>function_exists('env') ? env('payment_gateway_saman_mid') : null],
        'perfectMoney'=>[]
    ]
];