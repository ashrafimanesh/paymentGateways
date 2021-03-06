<?php

require_once __DIR__.'/../loader.php';

$pmConfig=new \Ashrafi\PaymentGateways\Gateways\PerfectMoney\PerfectMoneyConfig(
    \Ashrafi\PaymentGateways\Gateways\PerfectMoney\Model::class,array_merge($configs['gateways']['perfectMoney'])
);
//Set all needed gateways

$gateway=\Ashrafi\PaymentGateways\GatewayFactory::getInstance(['pm'=>$pmConfig],['proxy'=>$configs['proxy']]);


$currentUrl = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$currentFileName=basename(__FILE__, '.php');;
$callbackUrl=str_replace($currentFileName,'userCallback',$currentUrl);
$amount=1000;
$orderId="a".uniqid();


$payRequest=new \Ashrafi\PaymentGateways\Requests\PayRequest($amount,$callbackUrl,$orderId);
$payRequest->setInputs(['payerAccountId'=>'U12751272']);

$payRequest->setCallbackUrl((str_replace(trim(end(explode('/',(isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"))),'callback.php',(isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]")));
$payResponse=new \Ashrafi\PaymentGateways\Responses\Response($payRequest);

$gateway->pm->pay($payRequest,$payResponse);

var_dump($payResponse);