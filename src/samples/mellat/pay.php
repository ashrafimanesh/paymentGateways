<?php

require_once __DIR__.'/../loader.php';

$currentUrl = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$currentFileName=basename(__FILE__, '.php');;
$callbackUrl=str_replace($currentFileName,'userCallback',$currentUrl);
$amount=1000;

$orderId=rand(100000,99999999);

require_once __DIR__.'/../gateway.php';


$payRequest=new \Ashrafi\PaymentGateways\Requests\PayRequest($amount,$callbackUrl,$orderId);
$payRequest->setCallbackUrl((str_replace(trim(end(explode('/',(isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"))),'callback.php',(isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]")));

$payResponse=new \Ashrafi\PaymentGateways\Responses\Response($payRequest);

$gateway->mellat->pay($payRequest,$payResponse);

var_dump($payResponse);
