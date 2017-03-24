<?php

require_once __DIR__.'/loader.php';

$currentUrl = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$currentFileName=basename(__FILE__, '.php');;
$callbackUrl=str_replace($currentFileName,'userCallback',$currentUrl);
$amount=1000;
$orderId="a".uniqid();


$model=new \Ashrafi\PaymentGateways\Gateways\Saman\Model();
$payRequest=new \Ashrafi\PaymentGateways\PayRequest($amount,$callbackUrl,$orderId);
var_dump($model->pay($payRequest));