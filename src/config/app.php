<?php
/**
 * Created by PhpStorm.
 * User: ashrafimanesh
 * Date: 3/24/17
 * Time: 10:33 AM
 */

return [
    'systemCallbackUrl'=>(str_replace(trim(end(explode('/',(isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"))),'callback.php',(isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"))
    ,'logPath'=>__DIR__.'/../logs'
    ,'logDateFormat'=>'Y-m-d'
    ,'proxy'=>[
        'enable'=>true,
        'type'=>'urlProxy',//'proxy|urlProxy'
        'curlProxyAddress'=>'http://ashrafimanesh.ir/proxy/curl.php',//http://proxy url
        'soapProxyAddress'=>'http://ashrafimanesh.ir/proxy/soap.php',//http://proxy url
    ]
];