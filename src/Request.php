<?php
/**
 * Created by PhpStorm.
 * User: sonaa
 * Date: 3/24/17
 * Time: 1:36 PM
 */

namespace Ashrafi\PaymentGateways;


class Request
{
    use Collection;

    public function __construct($orderId=0,$gatewayOrderId=null){
        $this->setOrderId($orderId)->setGatewayOrderId($gatewayOrderId);
    }
}