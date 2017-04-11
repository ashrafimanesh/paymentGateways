<?php
/**
 * Created by PhpStorm.
 * User: ashrafimanesh@gmail.com
 * Date: 4/1/17
 * Time: 10:04 AM
 */

require_once __DIR__ . '/../loader.php';

$pmConfig=new \Ashrafi\PaymentGateways\Gateways\PerfectMoney\PerfectMoneyConfig(
    \Ashrafi\PaymentGateways\Gateways\PerfectMoney\Model::class,array_merge($configs['gateways']['perfectMoney'])
);
//Set all needed gateways

$gateway=\Ashrafi\PaymentGateways\GatewayFactory::getInstance(['pm'=>$pmConfig],['proxy'=>$configs['proxy']]);

$model=new \Ashrafi\PaymentGateways\Gateways\PerfectMoney\Model();

$balanceRequest=new \Ashrafi\PaymentGateways\Requests\BalanceRequest();

$balanceResponse=new \Ashrafi\PaymentGateways\Responses\BalanceResponse($gateway->pm->getAccountId());

$gateway->pm->getBalance($balanceRequest,$balanceResponse);
echo '<pre>';
var_dump($balanceResponse);