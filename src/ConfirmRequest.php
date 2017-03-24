<?php
/**
 * Created by PhpStorm.
 * User: sonaa
 * Date: 3/24/17
 * Time: 1:13 PM
 */

namespace Ashrafi\PaymentGateways;


class ConfirmRequest extends Request
{

    public function __construct($orderId,$gatewayOrderId){
        $this->setOrderId($orderId);
        $this->setGatewayOrderId($gatewayOrderId);
    }


}