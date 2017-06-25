<?php

require_once __DIR__.'/../loader.php';

require_once __DIR__.'/../gateway.php';

$inputs=$_GET+$_POST;
$callbackRequest=new \Ashrafi\PaymentGateways\Requests\CallbackRequest($inputs['orderId'],$inputs['refId'],$inputs);

$callbackResponse=new \Ashrafi\PaymentGateways\Responses\CallbackResponse($callbackRequest);

$gateway->dalanpay->callback($callbackRequest,$callbackResponse);

dd($callbackResponse);
