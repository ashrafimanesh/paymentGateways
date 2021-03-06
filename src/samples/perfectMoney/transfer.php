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

$transferRequest=new \Ashrafi\PaymentGateways\Requests\TransferRequest();
$transferRequest->setPayer($_GET['payer_account_id'])->setPayee($_GET['payee_account_id'])->setAmount($_GET['amount']);

$transferResponse=new \Ashrafi\PaymentGateways\Responses\TransferResponse($transferRequest);

$gateway->pm->transfer($transferRequest,$transferResponse);

echo '<pre>';
var_dump($transferResponse);
