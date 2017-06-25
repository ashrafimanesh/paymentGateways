<?php
/**
 * Created by PhpStorm.
 * User: ashrafimanesh@gmail.com
 * Date: 6/25/17
 * Time: 10:08 PM
 */

namespace Ashrafi\PaymentGateways\Gateways\Dalanpay;


use Ashrafi\PaymentGateways\AbstractConfig;

class DalanpayConfig extends AbstractConfig
{

    function setHandler($handlerClass)
    {
        $this->handler=$handlerClass?:Model::class;
        return $this;
    }
}