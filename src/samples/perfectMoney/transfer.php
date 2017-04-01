<?php
/**
 * Created by PhpStorm.
 * User: ashrafimanesh@gmail.com
 * Date: 4/1/17
 * Time: 10:04 AM
 */

require_once __DIR__ . '/../loader.php';

$model=new \Ashrafi\PaymentGateways\Gateways\PerfectMoney\Model();

$transferRequest=new \Ashrafi\PaymentGateways\Requests\TransferRequest();
$transferRequest->setPayer($_GET['payer_account_id'])->setPayee($_GET['payee_account_id'])->setAmount($_GET['amount']);
echo '<pre>';
var_dump($model->transfer($transferRequest));
