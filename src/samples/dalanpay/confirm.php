<?php
/**
 * Created by PhpStorm.
 * User: ashrafimanesh
 * Date: 3/24/17
 * Time: 1:26 PM
 */

require_once __DIR__.'/../loader.php';
$orderId=$_GET['orderId'];
$gatewayOrderId='1bc524750630055b06270770dce189742f746d29';

require_once __DIR__.'/../gateway.php';

$confirmRequest=new \Ashrafi\PaymentGateways\Requests\ConfirmRequest($orderId,$gatewayOrderId);
$confirmResponse=new \Ashrafi\PaymentGateways\Responses\ConfirmResponse($confirmRequest);

$gateway->dalanpay->confirm($confirmRequest,$confirmResponse);

dd($confirmResponse->toArray());