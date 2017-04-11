<?php
/**
 * Created by PhpStorm.
 * User: ashrafimanesh
 * Date: 3/24/17
 * Time: 1:26 PM
 */

require_once __DIR__.'/loader.php';
$orderId=$_GET['orderId'];
$gatewayOrderId='HtuuD6rIhRr+AD5c8wDe5teDVDk0Pd';

require_once __DIR__.'/gateway.php';

$confirmRequest=new \Ashrafi\PaymentGateways\Requests\ConfirmRequest($orderId,$gatewayOrderId);
$confirmResponse=new \Ashrafi\PaymentGateways\Responses\ConfirmResponse($confirmRequest);

$gateway->saman->confirm($confirmRequest,$confirmResponse);

var_dump($confirmResponse->toArray());