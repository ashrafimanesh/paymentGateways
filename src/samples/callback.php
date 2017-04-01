<?php
/**
 * Created by PhpStorm.
 * User: ashrafimanesh@gmail.com
 * Date: 3/24/17
 * Time: 9:11 AM
 */

require_once __DIR__.'/loader.php';

$model=new \Ashrafi\PaymentGateways\Gateways\Saman\Model();
$inputs=$_GET+$_POST;
$callbackRequest=new \Ashrafi\PaymentGateways\Requests\CallbackRequest($inputs['ResNum'],$inputs['RefNum'],$inputs);
$callbackResponse=$model->callback($callbackRequest);
echo '<pre>';
var_dump($callbackResponse->toArray());

