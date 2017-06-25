<?php

$samanConfig=new \Ashrafi\PaymentGateways\Gateways\Saman\SamanConfig(
    \Ashrafi\PaymentGateways\Gateways\Saman\Model::class,$configs['gateways']['saman']
);

$mellatConfig=new \Ashrafi\PaymentGateways\Gateways\Mellat\MellatConfig(
    \Ashrafi\PaymentGateways\Gateways\Mellat\Model::class,array_merge($configs['gateways']['mellat'],['proxy'=>['enable'=>false]])
);

$dalanpayConfig=new \Ashrafi\PaymentGateways\Gateways\Dalanpay\DalanpayConfig(
    \Ashrafi\PaymentGateways\Gateways\Dalanpay\Model::class,array_merge($configs['gateways']['dalanpay'],[])
);
//Set all needed gateways

$gateway=\Ashrafi\PaymentGateways\GatewayFactory::getInstance(['saman'=>$samanConfig,'mellat'=>$mellatConfig,'dalanpay'=>$dalanpayConfig],['proxy'=>$configs['proxy']]);
