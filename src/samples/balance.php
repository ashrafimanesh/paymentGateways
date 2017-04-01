<?php
/**
 * Created by PhpStorm.
 * User: ashrafimanesh@gmail.com
 * Date: 4/1/17
 * Time: 10:04 AM
 */

require_once __DIR__.'/loader.php';


$model=new \Ashrafi\PaymentGateways\Gateways\PerfectMoney\Model();

$balanceRequest=new \Ashrafi\PaymentGateways\Requests\BalanceRequest();
echo '<pre>';
var_dump($model->getBalance($balanceRequest));