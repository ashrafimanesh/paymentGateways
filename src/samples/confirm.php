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
$confirmRequest=new \Ashrafi\PaymentGateways\Requests\ConfirmRequest($orderId,$gatewayOrderId);

$model=new \Ashrafi\PaymentGateways\Gateways\Saman\Model();
echo '<pre>';
var_dump($model->confirm($confirmRequest)->toArray());