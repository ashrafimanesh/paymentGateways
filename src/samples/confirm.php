<?php
/**
 * Created by PhpStorm.
 * User: ashrafimanesh
 * Date: 3/24/17
 * Time: 1:26 PM
 */

require_once __DIR__.'/loader.php';
$orderId=$_GET['orderId'];
$gatewayOrderId='rC6uk3a+YNp2U7Mg679/8YTtKGVZRp';
$confirmRequest=new \Ashrafi\PaymentGateways\ConfirmRequest($orderId,$gatewayOrderId);

$model=new \Ashrafi\PaymentGateways\Gateways\Saman\Model();
echo '<pre>';
var_dump($model->confirm($confirmRequest)->toArray());