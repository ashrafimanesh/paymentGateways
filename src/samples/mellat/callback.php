<?php
/**
 * Created by PhpStorm.
 * User: ashrafimanesh@gmail.com
 * Date: 3/24/17
 * Time: 9:11 AM
 */

require_once __DIR__.'/loader.php';

require_once __DIR__.'/gateway.php';

$inputs=$_GET+$_POST;
$callbackRequest=new \Ashrafi\PaymentGateways\Requests\CallbackRequest($inputs['ResNum'],$inputs['RefNum'],$inputs);

$callbackResponse=new \Ashrafi\PaymentGateways\Responses\CallbackResponse($callbackRequest);

$gateway->saman->callback($callbackRequest,$callbackResponse);

var_dump($callbackResponse);
